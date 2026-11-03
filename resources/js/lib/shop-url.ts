import { facetAttributes, SHOP_BASE_PATH, sortOptions } from '@/data/shop-config';
import type { ShopQuery, SortKey } from '@/types/shop';
import { EMPTY_QUERY } from './shop-filter';

/**
 * The URL is the single source of truth for what the shop shows, so every
 * filtered view can be bookmarked, shared and reloaded:
 *
 *   /shop/laptops/dell/latitude?brand[]=Dell&ram[]=16GB&min=50000&max=120000
 *        &stock=1&sale=1&rating=4&sort=price-asc&page=2
 *
 * Array parameters use the `name[]` form, which Laravel reads as arrays
 * (`$request->input('brand')`) with no extra parsing.
 */

const SORT_KEYS = new Set<string>(sortOptions.map((o) => o.key));

function positiveNumber(value: string | null): number | undefined {
    if (value === null || value.trim() === '') return undefined;
    const n = Number(value);
    return Number.isFinite(n) && n >= 0 ? n : undefined;
}

export function parseShopQuery(search: string): ShopQuery {
    const params = new URLSearchParams(search);
    const attrs: Record<string, string[]> = {};

    for (const { key } of facetAttributes) {
        const values = params.getAll(`${key}[]`).filter(Boolean);
        if (values.length) attrs[key] = values;
    }

    const sort = params.get('sort') ?? '';
    const rating = positiveNumber(params.get('rating'));
    const page = Math.floor(positiveNumber(params.get('page')) ?? 1);

    return {
        brands: params.getAll('brand[]').filter(Boolean),
        attrs,
        minPrice: positiveNumber(params.get('min')),
        maxPrice: positiveNumber(params.get('max')),
        inStock: params.get('stock') === '1',
        onSale: params.get('sale') === '1',
        minRating: rating && rating > 0 && rating <= 5 ? rating : undefined,
        sort: (SORT_KEYS.has(sort) ? sort : EMPTY_QUERY.sort) as SortKey,
        page: page >= 1 ? page : 1,
    };
}

/** Query string for a ShopQuery, leaving out anything at its default so URLs stay short. */
export function buildShopSearch(query: ShopQuery): string {
    const params = new URLSearchParams();

    query.brands.forEach((b) => params.append('brand[]', b));
    for (const [key, values] of Object.entries(query.attrs)) values.forEach((v) => params.append(`${key}[]`, v));
    if (query.minPrice !== undefined) params.set('min', String(query.minPrice));
    if (query.maxPrice !== undefined) params.set('max', String(query.maxPrice));
    if (query.inStock) params.set('stock', '1');
    if (query.onSale) params.set('sale', '1');
    if (query.minRating !== undefined) params.set('rating', String(query.minRating));
    if (query.sort !== EMPTY_QUERY.sort) params.set('sort', query.sort);
    if (query.page > 1) params.set('page', String(query.page));

    const search = params.toString();
    return search ? `?${search}` : '';
}

export function buildShopUrl(path: string[], query: ShopQuery): string {
    const base = path.length ? `${SHOP_BASE_PATH}/${path.join('/')}` : SHOP_BASE_PATH;
    return `${base}${buildShopSearch(query)}`;
}

/** '/shop/laptops/dell' → ['laptops', 'dell']. Anything outside the shop base path gives []. */
export function parseShopPath(pathname: string): string[] {
    const rest = pathname.startsWith(SHOP_BASE_PATH) ? pathname.slice(SHOP_BASE_PATH.length) : '';
    return rest.split('/').filter(Boolean);
}
