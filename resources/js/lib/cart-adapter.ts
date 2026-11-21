import type { CartLine, ServerCart, ServerCartLine } from '@/types/cart';

export function serverLineToCartLine(line: ServerCartLine): CartLine {
    return {
        id: line.id,
        key: line.key,
        productId: line.productId,
        slug: line.slug,
        name: line.name,
        brand: line.brand,
        image: line.image ?? '',
        unitPrice: line.unitPrice,
        quantity: line.quantity,
        sku: line.sku,
        maxQuantity: line.maxQuantity
    };
}

export function serverCartToLines(cart: ServerCart | null | undefined): CartLine[] {
    if (!cart?.lines) return [];
    return cart.lines.map(serverLineToCartLine);
}

export function lineIdFor(lines: CartLine[], key: string): number | null {
    // Server-side line id is looked up from the ORIGINAL server payload
    // (this is a fallback; the mutation methods carry the id through)
    return null;
}