import { useCallback } from 'react';
import { productToCartLine, productToWishlistItem } from '@/lib/cart-items';
import { useOptionalCart } from '@/store/cart';
import { useOptionalWishlist } from '@/store/wishlist';
import type { Product } from '@/types/product';

/**
 * What a product card does on "Add to Cart": the handler passed as a prop if
 * there is one, otherwise the cart store when a CommerceProvider is present.
 * Returns undefined when neither exists.
 */
export function useAddToCart(override?: (product: Product) => void): ((product: Product) => void) | undefined {
    const cart = useOptionalCart();
    const add = cart?.add;
    const fallback = useCallback((product: Product) => add?.(productToCartLine(product), 1), [add]);
    if (override) return override;
    return add ? fallback : undefined;
}

/** Wishlist state for a product card: store-backed when a provider exists, else null so the card keeps local state. */
export function useWishlistFor(product: Product) {
    const wishlist = useOptionalWishlist();
    if (!wishlist) return null;
    return {
        active: wishlist.has(product.id),
        toggle: () => wishlist.toggle(productToWishlistItem(product)),
    };
}
