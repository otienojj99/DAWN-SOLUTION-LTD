export interface CartLine {
    id?: number;               // ← server line id, used for PATCH/DELETE
    key: string;
    productId: string;
    slug: string;
    name: string;
    brand: string;
    image: string;
    unitPrice: number;
    quantity: number;
    variantSummary?: string;
    sku?: string;
    maxQuantity?: number;
}

/** What is passed to `cart.add()`: a line without its key or quantity. */
export type NewCartLine = Omit<CartLine, 'key' | 'quantity'>;

export type DeliveryArea = 'nairobi' | 'outside';

export interface CartTotals {
    itemCount: number;
    /** Sum of unit price x quantity, before VAT. */
    subtotal: number;
    vat: number;
    /** Delivery fee included in `total`. 0 until Nairobi is chosen, and always 0 for "outside Nairobi" (agreed separately). */
    shipping: number;
    shippingStatus: 'not-chosen' | 'included' | 'to-confirm';
    /** subtotal + VAT + shipping (when included). */
    total: number;
}

/** A saved copy of what the wishlist panel needs to draw a product, so it works without the catalogue. */
export interface WishlistItem {
    productId: string;
    slug: string;
    name: string;
    brand: string;
    image: string;
    price: number;
    oldPrice?: number;
    inStock?: boolean;
}

export interface AuthUser {
    id: string;
    name: string;
    email: string;
}

export interface LoginCredentials {
    email: string;
    password: string;
}

/** Everything the checkout form collects, sent to `onPlaceOrder`. */
export interface CheckoutOrder {
    customer: { fullName: string; phone: string; email: string };
    delivery: { area: DeliveryArea; address: string; town: string; notes: string };
    /** Safaricom number the M-Pesa prompt is sent to. */
    mpesaPhone: string;
    lines: CartLine[];
    /** What the buyer was shown. The server must recompute and not trust it. */
    totals: CartTotals;
}

export interface ServerCartLine {
    id: number;
    key: string;
    productId: string;
    slug: string;
    name: string;
    brand: string;
    image: string | null;
    unitPrice: number;
    quantity: number;
    sku?: string;
    maxQuantity?: number;
    priceChanged?: boolean;
    outOfStock?: boolean;
}

export interface ServerCart {
    id: number;
    currency: string;
    lines: ServerCartLine[];
    itemCount: number;
    subtotal: number;
}