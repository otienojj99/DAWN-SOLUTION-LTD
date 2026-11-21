<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Controller;
use App\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller 
{
    public function __construct(protected CartService $cart) {}


    public function show(Request $request): JsonResponse
    {
        $cart = $this->cart->resolve($request);

        return $this->ok($cart);
    }

     public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity'   => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $cart = $this->cart->resolve($request);
        $cart = $this->cart->add(
            $cart,
            (int) $validated['product_id'],
            $validated['variant_id'] ?? null,
            (int) ($validated['quantity'] ?? 1)
        );

        return $this->ok($cart, 'Added to cart.');
    }

    public function updateItem(Request $request, int $itemId): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:20'],
        ]);

        $cart = $this->cart->resolve($request);
        $cart = $this->cart->setQuantity($cart, $itemId, (int) $validated['quantity']);

        return $this->ok($cart, 'Cart updated.');
    }

    public function removeItem(Request $request, int $itemId): JsonResponse
    {
        $cart = $this->cart->resolve($request);
        $cart = $this->cart->removeLine($cart, $itemId);

        return $this->ok($cart, 'Item removed.');
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = $this->cart->resolve($request);
        $cart = $this->cart->clear($cart);

        return $this->ok($cart, 'Cart cleared.');
    }

     protected function ok($cart, string $message = 'Cart fetched.'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $this->cart->toPayload($cart),
        ]);
    }
}