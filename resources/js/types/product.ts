export interface ProductSpec {
    label: string;
    value: string;
}

export type ProductBadge = 'sale' | 'new' | 'bestseller' | 'corporate';

/**
 * A real, linkable product. This is the shape the hero carousel, product
 * cards, and (eventually) the shop grid all consume — one definition shared
 * everywhere, so "the hero shows real products" stays true as the catalog
 * grows instead of drifting into hero-only placeholder data.
 */
export interface Product {
    id: string;
    /** Used to build the product page URL: `/products/${slug}`. */
    slug: string;
    name: string;
    brand: string;
    /** Top-level category label, used by the best-sellers tabs (e.g. "Laptops"). */
    category: string;
    image: string;
    price: number;
    oldPrice?: number;
    currency?: string;
    specs: ProductSpec[];
    rating?: number;
    reviewCount?: number;
    inStock?: boolean;
    /** When set and low (<= 5), cards show an urgency "Only N left" message. */
    stockCount?: number;
    badge?: ProductBadge;
    /** Marks the product as a best seller for the BestSellersSection. */
    bestSeller?: boolean;
}
