import type { FacetAttribute, SortOption } from '@/types/shop';

export const PRODUCTS_PER_PAGE = 12;

export const sortOptions: SortOption[] = [
    { key: 'featured', label: 'Featured' },
    { key: 'bestselling', label: 'Best selling' },
    { key: 'price-asc', label: 'Price: low to high' },
    { key: 'price-desc', label: 'Price: high to low' },
    { key: 'rating', label: 'Top rated' },
    { key: 'newest', label: 'Newest first' },
    { key: 'name', label: 'Name: A to Z' },
];

/** Spec filters. Each shows up only in categories where at least one product has that attribute. */
export const facetAttributes: FacetAttribute[] = [
    { key: 'processor', label: 'Processor' },
    { key: 'ram', label: 'Memory (RAM)' },
    { key: 'storage', label: 'Storage' },
];

export const ratingOptions = [4, 3] as const;

/** Base path of the shop. Category URLs are `${SHOP_BASE_PATH}/${slug}/${slug}...`. */
export const SHOP_BASE_PATH = '/shop';
