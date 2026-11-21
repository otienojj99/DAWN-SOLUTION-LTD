import type { NewCartLine, WishlistItem } from '@/types/cart';
import type { Product } from '@/types/product';

/** Turns a catalogue product into a cart line (no variants). */
export function productToCartLine(product: Product): NewCartLine {
    return {
        productId: product.id,
        slug: product.slug,
        name: product.name,
        brand: product.brand,
        image: product.image ?? '',
        unitPrice: product.price,
        maxQuantity: product.stockCount ?? undefined,
    };
}

export function productToWishlistItem(product: Product): WishlistItem {
    return {
        productId: product.id,
        slug: product.slug,
        name: product.name,
        brand: product.brand,
        image: product.image ?? '',
        price: product.price,
        oldPrice: product.oldPrice ?? undefined,
        inStock: product.inStock,
    };
}
