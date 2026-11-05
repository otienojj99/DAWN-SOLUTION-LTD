import type { Product } from '@/types/product';

/**
 * One node of the shop category tree. `slug` is the URL segment, so the
 * node at laptops → dell → latitude lives at `/shop/laptops/dell/latitude`.
 * The same shape can be produced from your `categories` table (see the
 * README for the nested-set / parent_id → tree conversion).
 */
export interface CategoryNode {
    slug: string;
    /** Name in lists, breadcrumbs and the sidebar, e.g. "Latitude Series". */
    label: string;
    /** Shorter name used for brand group headings in the sidebar, e.g. "Dell". Falls back to `label`. */
    short?: string;
    /** The page's H1 when this node is the deepest one selected, e.g. "Dell Latitude Series". */
    heading: string;
    description?: string;
    children?: CategoryNode[];
}

/** A product in the shop catalogue: a normal `Product` plus where it lives and what can be filtered on. */
export interface ShopProduct extends Omit<Product, 'categoryPath'> {
    /** Category slugs from the root down, e.g. ['laptops', 'dell', 'latitude']. */
    categoryPath: string[];
    /** Filterable specs keyed by `FacetAttribute.key` (e.g. { ram: '16GB' }). */
    attributes?: Record<string, string>;
    /** ISO date, used by the "Newest" sort. */
    addedAt: string;
    /** Units sold, used by the "Best selling" sort. */
    sold?: number;
}

export type SortKey = 'featured' | 'bestselling' | 'price-asc' | 'price-desc' | 'rating' | 'newest' | 'name';

export interface SortOption {
    key: SortKey;
    label: string;
}

/** A spec shown as a checkbox filter, e.g. Memory (RAM). Appears only where products in the current category have it. */
export interface FacetAttribute {
    key: string;
    label: string;
}

/** Everything the visitor can change on the shop page. Serialised to / parsed from the URL query string. */
export interface ShopQuery {
    brands: string[];
    /** Selected values per attribute key, e.g. { ram: ['16GB', '32GB'] }. */
    attrs: Record<string, string[]>;
    minPrice?: number;
    maxPrice?: number;
    inStock: boolean;
    onSale: boolean;
    /** Show products rated at least this many stars. */
    minRating?: number;
    sort: SortKey;
    page: number;
}

export interface FacetOption {
    value: string;
    /** How many products you would get by ticking it, given the other filters. */
    count: number;
    selected: boolean;
}

export interface FacetGroup {
    key: string;
    label: string;
    options: FacetOption[];
}

export interface PageResult<T> {
    items: T[];
    page: number;
    pageCount: number;
    total: number;
    /** 1-based position of the first/last item shown, for "Showing 1–12 of 28". */
    from: number;
    to: number;
}
