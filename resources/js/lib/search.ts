import type { ShopProduct } from '@/types/shop';

/**
 * Product search used by the header search panel and the shop's `?q=` filter.
 * Every word typed must appear somewhere in the product (name, brand,
 * category, specs or attributes), in any order, so "dell 16gb" finds a Dell
 * with 16GB of RAM. Matches in the name count for the most, then brand.
 * Your Laravel endpoint can do the same with a LIKE / full-text query.
 */

export function tokenize(query: string): string[] {
    return query
        .toLowerCase()
        .split(/[\s,]+/)
        .map((t) => t.trim())
        .filter(Boolean);
}

type Searchable = Pick<ShopProduct, 'name' | 'brand' | 'category' | 'specs' | 'attributes'>;

function haystacks(p: Searchable) {
    return {
        name: p.name.toLowerCase(),
        brand: p.brand.toLowerCase(),
        category: p.category.toLowerCase(),
        rest: [...p.specs.map((s) => `${s.label} ${s.value}`), ...Object.values(p.attributes ?? {})].join(' ').toLowerCase(),
    };
}

/** 0 when any word is missing; otherwise a relevance score (higher is better). */
export function scoreProduct(p: Searchable, tokens: string[]): number {
    if (tokens.length === 0) return 0;
    const h = haystacks(p);
    let score = 0;
    for (const t of tokens) {
        let s = 0;
        if (h.name.startsWith(t)) s = 12;
        else if (h.name.includes(` ${t}`)) s = 9;
        else if (h.name.includes(t)) s = 7;
        else if (h.brand.includes(t)) s = 5;
        else if (h.category.includes(t)) s = 3;
        else if (h.rest.includes(t)) s = 2;
        if (s === 0) return 0;
        score += s;
    }
    return score;
}

export function matchesSearch(p: Searchable, query?: string): boolean {
    if (!query) return true;
    return scoreProduct(p, tokenize(query)) > 0;
}

export interface SearchResult {
    /** Up to `limit` best matches. */
    items: ShopProduct[];
    /** Every match, so the panel can say "See all 23 results". */
    total: number;
}

export function searchProducts(catalogue: ShopProduct[], query: string, limit = 6): SearchResult {
    const tokens = tokenize(query);
    if (tokens.length === 0) return { items: [], total: 0 };
    const scored = catalogue
        .map((p) => ({ p, s: scoreProduct(p, tokens) }))
        .filter((x) => x.s > 0)
        .sort((a, b) => b.s - a.s || Number(b.p.inStock !== false) - Number(a.p.inStock !== false) || (b.p.sold ?? 0) - (a.p.sold ?? 0));
    return { items: scored.slice(0, limit).map((x) => x.p), total: scored.length };
}

/** Splits text around the matched words so the panel can bold them. Plain strings, so no HTML injection. */
export function highlightParts(text: string, tokens: string[]): { text: string; match: boolean }[] {
    if (tokens.length === 0) return [{ text, match: false }];
    const escaped = tokens.map((t) => t.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).sort((a, b) => b.length - a.length);
    const re = new RegExp(`(${escaped.join('|')})`, 'gi');
    const lower = tokens.map((t) => t.toLowerCase());
    return text
        .split(re)
        .filter((part) => part !== '')
        .map((part) => ({ text: part, match: lower.includes(part.toLowerCase()) }));
}
