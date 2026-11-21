<?php

namespace App\Services\Cart;

use App\Enums\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartService
{
    public const MAX_QTY_PER_LINE = 20;
    public const MAX_LINES         = 30;

    /**
     * Find or create the active cart for the current request context.
     * Merges any guest cart into the user cart if the user just logged in.
     */
    public function resolve(Request $request): Cart
    {
        $user      = $request->user();
        $sessionId = $request->hasSession() ? $request->session()->getId() : null;

        if ($user) {
            return $this->resolveForUser($user, $sessionId);
        }

        return $this->resolveForGuest($sessionId);
    }

    protected function resolveForUser(User $user, ?string $sessionId): Cart
    {
        return DB::transaction(function () use ($user, $sessionId) {
            // If a guest cart exists for this session, merge it into the user's cart
            if ($sessionId) {
                $guestCart = Cart::active()
                    ->whereNull('user_id')
                    ->where('session_id', $sessionId)
                    ->lockForUpdate()
                    ->first();

                if ($guestCart) {
                    $this->mergeGuestCartIntoUserCart($guestCart, $user->id);
                }
            }

            $cart = Cart::active()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $cart) {
                $cart = Cart::create([
                    'user_id'    => $user->id,
                    'status'     => 'active',
                    'session_id' => null,
                ]);
            }

            // Attach session_id for the merge check above to work next time
            if ($sessionId && $cart->session_id !== $sessionId) {
                $cart->update(['session_id' => $sessionId]);
            }

            return $cart;
        });
    }

    protected function resolveForGuest(?string $sessionId): Cart
    {
        if (! $sessionId) {
            throw new ApiException(
                ApiErrorCode::UNKNOWN,
                'Unable to identify your session. Please refresh and try again.'
            );
        }

        $cart = Cart::active()->where('session_id', $sessionId)->lockForUpdate()->first();

        if (! $cart) {
            $cart = Cart::create([
                'session_id' => $sessionId,
                'status'     => 'active',
            ]);
        }

        return $cart;
    }

    /**
     * Add quantity of a product. Merges into existing line if already present.
     */
    public function add(Cart $cart, int $productId, ?int $variantId, int $quantity = 1): Cart
    {
        if ($quantity < 1) {
            throw new ApiException(ApiErrorCode::UNKNOWN, 'Quantity must be at least 1.');
        }

        return DB::transaction(function () use ($cart, $productId, $variantId, $quantity) {
            $product = Product::query()
                ->visible()
                ->whereKey($productId)
                ->lockForUpdate()
                ->first();

            if (! $product) {
                throw new ApiException(ApiErrorCode::PRODUCT_NOT_FOUND, 'This product is not available.');
            }

            $existing = $cart->items()->where('product_id', $productId)->where('variant_id', $variantId)->first();
            $currentQty = $existing?->quantity ?? 0;
            $requestedQty = $currentQty + $quantity;

            // Check line limit
            if ($requestedQty > self::MAX_QTY_PER_LINE) {
                throw new ApiException(
                    ApiErrorCode::UNKNOWN,
                    'You can add up to ' . self::MAX_QTY_PER_LINE . ' of this product to your cart.'
                );
            }

            // Check distinct line count
            if (! $existing && $cart->items()->count() >= self::MAX_LINES) {
                throw new ApiException(
                    ApiErrorCode::UNKNOWN,
                    'Your cart can hold up to ' . self::MAX_LINES . ' different products.'
                );
            }

            // Check stock
            if ($product->track_inventory && ! $product->allow_backorder) {
                if ($product->stock_quantity < $requestedQty) {
                    throw new ApiException(
                        ApiErrorCode::PRODUCT_OUT_OF_STOCK,
                        "Only {$product->stock_quantity} left in stock for {$product->name}."
                    );
                }
            }

            $unitPrice = (float) $product->effective_price;

            if ($existing) {
                $existing->update([
                    'quantity'   => $requestedQty,
                    'unit_price' => $unitPrice,
                ]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'variant_id' => $variantId,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                ]);
            }

            $cart->touchActivity();

            return $cart->fresh('items.product');
        });
    }

    /**
     * Set exact quantity for a line. Quantity 0 = remove.
     */
    public function setQuantity(Cart $cart, int $itemId, int $quantity): Cart
    {
        return DB::transaction(function () use ($cart, $itemId, $quantity) {
            $item = $cart->items()->whereKey($itemId)->lockForUpdate()->first();

            if (! $item) {
                throw new ApiException(ApiErrorCode::UNKNOWN, 'Cart item not found.');
            }

            if ($quantity <= 0) {
                $item->delete();
                $cart->touchActivity();
                return $cart->fresh('items.product');
            }

            if ($quantity > self::MAX_QTY_PER_LINE) {
                throw new ApiException(
                    ApiErrorCode::UNKNOWN,
                    'You can add up to ' . self::MAX_QTY_PER_LINE . ' of this product.'
                );
            }

            $product = Product::whereKey($item->product_id)->lockForUpdate()->first();

            if ($product && $product->track_inventory && ! $product->allow_backorder) {
                if ($product->stock_quantity < $quantity) {
                    throw new ApiException(
                        ApiErrorCode::PRODUCT_OUT_OF_STOCK,
                        "Only {$product->stock_quantity} left in stock."
                    );
                }
            }

            $item->update([
                'quantity'   => $quantity,
                'unit_price' => $product ? (float) $product->effective_price : $item->unit_price,
            ]);

            $cart->touchActivity();

            return $cart->fresh('items.product');
        });
    }

    public function removeLine(Cart $cart, int $itemId): Cart
    {
        $cart->items()->whereKey($itemId)->delete();
        $cart->touchActivity();

        return $cart->fresh('items.product');
    }

    public function clear(Cart $cart): Cart
    {
        $cart->items()->delete();
        $cart->touchActivity();

        return $cart->fresh('items.product');
    }

    /**
     * Merge a guest cart into the user's cart, summing quantities.
     */
    protected function mergeGuestCartIntoUserCart(Cart $guestCart, int $userId): void
    {
        $userCart = Cart::active()->where('user_id', $userId)->lockForUpdate()->first();

        if (! $userCart) {
            // No existing user cart — take over the guest cart
            $guestCart->update(['user_id' => $userId]);
            return;
        }

        foreach ($guestCart->items as $guestItem) {
            $existing = $userCart->items()
                ->where('product_id', $guestItem->product_id)
                ->where('variant_id', $guestItem->variant_id)
                ->first();

            if ($existing) {
                $existing->update([
                    'quantity' => min(self::MAX_QTY_PER_LINE, $existing->quantity + $guestItem->quantity),
                ]);
            } else {
                $userCart->items()->create([
                    'product_id' => $guestItem->product_id,
                    'variant_id' => $guestItem->variant_id,
                    'quantity'   => $guestItem->quantity,
                    'unit_price' => $guestItem->unit_price,
                ]);
            }
        }

        $guestCart->update(['status' => 'converted']);
        $guestCart->items()->delete();
    }

    /**
     * Serialize cart to the shape the frontend expects.
     */
    public function toPayload(Cart $cart): array
    {
        $cart->loadMissing(['items.product.brand', 'items.product.primaryImage', 'items.product.category', 'items.product.promotions']);

        $lines = $cart->items->map(function (CartItem $item) {
            $product = $item->product;
            if (! $product) {
                return null;
            }

            $image = $product->primaryImage->first()?->url
                ?? $product->images->first()?->url
                ?? null;

            // Re-validate: if the product is no longer visible or out of stock, flag it
            $effectivePrice = (float) $product->effective_price;
            $priceChanged   = abs($effectivePrice - (float) $item->unit_price) > 0.01;
            $outOfStock     = $product->track_inventory
                && ! $product->allow_backorder
                && $product->stock_quantity < $item->quantity;

            return [
                'id'          => $item->id,
                'key'         => $product->sku ?? (string) $product->id,
                'productId'   => (string) $product->id,
                'slug'        => $product->slug,
                'name'        => $product->name,
                'brand'       => $product->brand?->name ?? '',
                'image'       => $image,
                'unitPrice'   => $effectivePrice,
                'quantity'    => $item->quantity,
                'sku'         => $product->sku,
                'maxQuantity' => $product->track_inventory
                    ? min(self::MAX_QTY_PER_LINE, (int) $product->stock_quantity)
                    : self::MAX_QTY_PER_LINE,
                'priceChanged' => $priceChanged,
                'outOfStock'   => $outOfStock,
            ];
        })->filter()->values();

        return [
            'id'         => $cart->id,
            'currency'   => $cart->currency,
            'lines'      => $lines->all(),
            'itemCount'  => $lines->sum('quantity'),
            'subtotal'   => round($lines->sum(fn ($l) => $l['unitPrice'] * $l['quantity']), 2),
        ];
    }
}