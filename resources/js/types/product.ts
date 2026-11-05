export interface ProductSpec {
    label: string;
    value: string;
}

export interface ProductBadge {
    label: string;
    color: string;
}

export interface Product {
    id: string;
    slug: string;
    name: string;
    brand: string;
    /** Optional — only present on shop-page products. */
    brandSlug?: string;
    category: string;
    /** Optional — only present on shop-page products. */
    categorySlug?: string;
    /** Optional — only present on shop-page products. */
    categoryPath?: string;
    image: string | null;
    price: number;
    oldPrice?: number | null;
    currency?: string;
    specs: ProductSpec[];
    rating?: number | null;
    reviewCount?: number | null;
    inStock?: boolean;
    stockCount?: number | null;
    badge?: ProductBadge | null;
    bestSeller?: boolean;

    /* Shop-page filter fields — extracted from specs server-side */
    ram?: string | null;
    storage?: string | null;
    screenSize?: string | null;
    cpu?: string | null;
    gpu?: string | null;
    os?: string | null;
}