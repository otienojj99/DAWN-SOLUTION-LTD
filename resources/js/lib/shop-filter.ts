import { facetAttributes, PRODUCTS_PER_PAGE } from '@/data/shop-config';
import type { FacetAttribute, FacetGroup, PageResult, ShopProduct, ShopQuery, SortKey } from '@/types/shop';
import { inCategory } from './shop-category';

/**
 * Pure filtering, sorting, faceting and paging. This is a line-for-line
 * description of what your Laravel controller needs to do with the same
 * query parameters (see README → "Moving the shop to the backend"), so the
 * client version and the server version can be compared side by side.
 */

export const EMPTY_QUERY: ShopQuery = {
    brands: [],
    attrs: {},
    inStock: false,
    onSale: false,
    sort: 'featured',
    page: 1,
};

/** Which parts of the query to leave out when applying filters (used to count facet options). */
type Skip = { brands?: boolean; attr?: string; rating?: boolean };

export function matches(product: ShopProduct, query: ShopQuery, skip: Skip = {}): boolean {
    if (!skip.brands && query.brands.length && !query.brands.includes(product.brand)) return false;

    for (const [key, values] of Object.entries(query.attrs)) {
        if (skip.attr === key || !values.length) continue;
        const value = product.attributes?.[key];
        if (!value || !values.includes(value)) return false;
    }

    if (query.minPrice !== undefined && product.price < query.minPrice) return false;
    if (query.maxPrice !== undefined && product.price > query.maxPrice) return false;
    if (query.inStock && product.inStock === false) return false;
    if (query.onSale && !(product.oldPrice && product.oldPrice > product.price)) return false;
    if (!skip.rating && query.minRating !== undefined && (product.rating ?? 0) < query.minRating) return false;
    return true;
}

export function filterProducts(products: ShopProduct[], query: ShopQuery): ShopProduct[] {
    return products.filter((p) => matches(p, query));
}

export function sortProducts(products: ShopProduct[], sort: SortKey): ShopProduct[] {
    const list = [...products]; // never mutate the catalogue
    const byName = (a: ShopProduct, b: ShopProduct) => a.name.localeCompare(b.name);

    switch (sort) {
        case 'price-asc':
            return list.sort((a, b) => a.price - b.price || byName(a, b));
        case 'price-desc':
            return list.sort((a, b) => b.price - a.price || byName(a, b));
        case 'rating':
            return list.sort((a, b) => (b.rating ?? 0) - (a.rating ?? 0) || (b.reviewCount ?? 0) - (a.reviewCount ?? 0));
        case 'newest':
            return list.sort((a, b) => b.addedAt.localeCompare(a.addedAt));
        case 'bestselling':
            return list.sort((a, b) => (b.sold ?? 0) - (a.sold ?? 0));
        case 'name':
            return list.sort(byName);
        case 'featured':
        default:
            // Best sellers first, then most-bought, so the first page is the strongest.
            return list.sort((a, b) => Number(!!b.bestSeller) - Number(!!a.bestSeller) || (b.sold ?? 0) - (a.sold ?? 0));
    }
}

export function paginate<T>(items: T[], page: number, perPage = PRODUCTS_PER_PAGE): PageResult<T> {
    const total = items.length;
    const pageCount = Math.max(1, Math.ceil(total / perPage));
    const current = Math.min(Math.max(1, Math.floor(page) || 1), pageCount);
    const start = (current - 1) * perPage;
    const slice = items.slice(start, start + perPage);

    return {
        items: slice,
        page: current,
        pageCount,
        total,
        from: total === 0 ? 0 : start + 1,
        to: start + slice.length,
    };
}

/* ------------------------------------------------------------------ */
/* Facets                                                              */
/* ------------------------------------------------------------------ */

function tally(products: ShopProduct[], read: (p: ShopProduct) => string | undefined): Map<string, number> {
    const counts = new Map<string, number>();
    for (const p of products) {
        const value = read(p);
        if (value) counts.set(value, (counts.get(value) ?? 0) + 1);
    }
    return counts;
}

/** Orders memory/storage values by size ("8GB" < "16GB" < "1TB") and everything else alphabetically. */
function compareValues(a: string, b: string): number {
    const size = (v: string) => {
        const m = v.match(/^(\d+(?:\.\d+)?)\s*(GB|TB)/i);
        return m ? parseFloat(m[1]) * (m[2].toUpperCase() === 'TB' ? 1024 : 1) : null;
    };
    const sa = size(a);
    const sb = size(b);
    return sa !== null && sb !== null ? sa - sb : a.localeCompare(b);
}

export function brandFacet(inScope: ShopProduct[], query: ShopQuery): FacetGroup {
    // Counts show what you would get by ticking the option, so apply every filter except brands.
    const pool = inScope.filter((p) => matches(p, query, { brands: true }));
    const counts = tally(pool, (p) => p.brand);
    // Keep ticked brands visible even if the other filters currently exclude them.
    for (const brand of query.brands) if (!counts.has(brand)) counts.set(brand, 0);

    return {
        key: 'brand',
        label: 'Brand',
        options: [...counts.entries()]
            .sort((a, b) => a[0].localeCompare(b[0]))
            .map(([value, count]) => ({ value, count, selected: query.brands.includes(value) })),
    };
}

/** One group per spec attribute that at least one product in the current category has. */
export function attributeFacets(inScope: ShopProduct[], query: ShopQuery, attributes: FacetAttribute[] = facetAttributes): FacetGroup[] {
    return attributes
        .map((attr): FacetGroup => {
            const selected = query.attrs[attr.key] ?? [];
            const pool = inScope.filter((p) => matches(p, query, { attr: attr.key }));
            const counts = tally(pool, (p) => p.attributes?.[attr.key]);
            for (const value of selected) if (!counts.has(value)) counts.set(value, 0);

            return {
                key: attr.key,
                label: attr.label,
                options: [...counts.entries()]
                    .sort((a, b) => compareValues(a[0], b[0]))
                    .map(([value, count]) => ({ value, count, selected: selected.includes(value) })),
            };
        })
        // A group is shown only if some product in the category has the attribute at all.
        .filter((group) => inScope.some((p) => p.attributes?.[group.key]));
}

/** Lowest and highest price in the category, for the price inputs' placeholders. */
export function priceBounds(inScope: ShopProduct[]): { min: number; max: number } | null {
    if (!inScope.length) return null;
    const prices = inScope.map((p) => p.price);
    return { min: Math.min(...prices), max: Math.max(...prices) };
}

export function ratingCount(inScope: ShopProduct[], query: ShopQuery, stars: number): number {
    return inScope.filter((p) => matches(p, query, { rating: true }) && (p.rating ?? 0) >= stars).length;
}

/* ------------------------------------------------------------------ */
/* One call that does it all                                           */
/* ------------------------------------------------------------------ */

export interface ShopResults {
    /** Products in the category before the sidebar filters: the facets are built from these. */
    inScope: ShopProduct[];
    page: PageResult<ShopProduct>;
    brandFacet: FacetGroup;
    attributeFacets: FacetGroup[];
    bounds: { min: number; max: number } | null;
    /** How many filters are applied (a price range counts as one). */
    activeFilterCount: number;
}

export function activeFilterCount(query: ShopQuery): number {
    return (
        query.brands.length +
        Object.values(query.attrs).reduce((sum, values) => sum + values.length, 0) +
        (query.minPrice !== undefined || query.maxPrice !== undefined ? 1 : 0) +
        (query.inStock ? 1 : 0) +
        (query.onSale ? 1 : 0) +
        (query.minRating !== undefined ? 1 : 0)
    );
}

export function computeShopResults(catalogue: ShopProduct[], categoryPath: string[], query: ShopQuery): ShopResults {
    const inScope = catalogue.filter((p) => inCategory(p, categoryPath));
    const filtered = filterProducts(inScope, query);
    const sorted = sortProducts(filtered, query.sort);

    return {
        inScope,
        page: paginate(sorted, query.page),
        brandFacet: brandFacet(inScope, query),
        attributeFacets: attributeFacets(inScope, query),
        bounds: priceBounds(inScope),
        activeFilterCount: activeFilterCount(query),
    };
}
