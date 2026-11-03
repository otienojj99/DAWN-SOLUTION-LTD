import { useCallback, useEffect, useRef, useState } from 'react';
import { EMPTY_QUERY } from '@/lib/shop-filter';
import { buildShopUrl, parseShopPath, parseShopQuery } from '@/lib/shop-url';
import type { ShopQuery } from '@/types/shop';

interface ShopState {
    path: string[];
    query: ShopQuery;
}

interface UseShopStateOptions {
    /** Category path from the URL, e.g. ['laptops', 'dell']. Pass what the server rendered so SSR and the first client render match. */
    initialPath: string[];
    /** `location.search` (with or without the leading "?"). */
    initialSearch?: string;
}

/**
 * Holds "which category + which filters/sort/page" and keeps it in the URL
 * with the History API (pushState on change, popstate for Back/Forward).
 * Nothing here touches the window while rendering, so it is SSR-safe.
 *
 * To let Inertia/Laravel own navigation instead, replace the `commit`
 * function below with `router.get(url, {}, { preserveState: true })` and
 * feed the props back in (README → "Moving the shop to the backend").
 */
export function useShopState({ initialPath, initialSearch = '' }: UseShopStateOptions) {
    const [state, setState] = useState<ShopState>(() => ({ path: initialPath, query: parseShopQuery(initialSearch) }));
    const stateRef = useRef(state);
    stateRef.current = state;

    // Back / Forward buttons.
    useEffect(() => {
        const onPopState = () => {
            setState({ path: parseShopPath(window.location.pathname), query: parseShopQuery(window.location.search) });
        };
        window.addEventListener('popstate', onPopState);
        return () => window.removeEventListener('popstate', onPopState);
    }, []);

    const commit = useCallback((next: ShopState, mode: 'push' | 'replace' = 'push') => {
        stateRef.current = next;
        setState(next);
        const url = buildShopUrl(next.path, next.query);
        if (mode === 'replace') window.history.replaceState(null, '', url);
        else window.history.pushState(null, '', url);
    }, []);

    /** Change filters/sort/page. Anything except an explicit `page` sends the visitor back to page 1. */
    const updateQuery = useCallback(
        (patch: Partial<ShopQuery>) => {
            const { path, query } = stateRef.current;
            commit({ path, query: { ...query, page: 1, ...patch } });
        },
        [commit],
    );

    /** Move to another category. Filters belong to the category you were in, so they reset; the sort order is kept. */
    const setPath = useCallback(
        (path: string[]) => {
            const { query } = stateRef.current;
            commit({ path, query: { ...EMPTY_QUERY, sort: query.sort } });
        },
        [commit],
    );

    const clearFilters = useCallback(() => {
        const { path, query } = stateRef.current;
        commit({ path, query: { ...EMPTY_QUERY, sort: query.sort } });
    }, [commit]);

    return { path: state.path, query: state.query, updateQuery, setPath, clearFilters };
}
