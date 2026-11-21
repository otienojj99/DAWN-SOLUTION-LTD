import { useEffect, useRef, useState } from 'react';
import { searchProducts } from '@/lib/search';
import type { SearchResult } from '@/lib/search';
import type { ShopProduct } from '@/types/shop';

/** What an async (server) search returns. Same shape as the built-in one. */
export type SearchFn = (query: string, signal: AbortSignal) => Promise<SearchResult>;

export interface ProductSearchState {
    result: SearchResult;
    loading: boolean;
    error: string | null;
    /** The trimmed query the result belongs to. */
    settledFor: string;
}

const EMPTY: SearchResult = { items: [], total: 0 };

/**
 * Debounced search. With `search` (e.g. a fetch to your Laravel endpoint) it
 * asks the server and ignores answers that arrive after a newer query; with
 * `catalogue` it searches locally and instantly. Needs 2+ characters.
 */
export function useProductSearch(query: string, options: { catalogue?: ShopProduct[]; search?: SearchFn; limit?: number; delay?: number; minLength?: number }): ProductSearchState {
    const { catalogue, search, limit = 6, delay = 150, minLength = 2 } = options;
    const [state, setState] = useState<ProductSearchState>({ result: EMPTY, loading: false, error: null, settledFor: '' });
    const run = useRef(0);

    useEffect(() => {
        const q = query.trim();
        run.current += 1;
        const mine = run.current;
        if (q.length < minLength) {
            setState({ result: EMPTY, loading: false, error: null, settledFor: '' });
            return;
        }
        const controller = new AbortController();
        setState((s) => ({ ...s, loading: !!search }));
        const timer = setTimeout(async () => {
            try {
                const result = search ? await search(q, controller.signal) : searchProducts(catalogue ?? [], q, limit);
                if (mine === run.current) setState({ result, loading: false, error: null, settledFor: q });
            } catch (err) {
                if (controller.signal.aborted || mine !== run.current) return;
                setState({ result: EMPTY, loading: false, error: err instanceof Error ? err.message : 'Search is unavailable right now.', settledFor: q });
            }
        }, delay);
        return () => {
            clearTimeout(timer);
            controller.abort();
        };
    }, [query, catalogue, search, limit, delay, minLength]);

    return state;
}
