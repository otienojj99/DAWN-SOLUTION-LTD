import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import type { ReactNode } from 'react';
import { WISHLIST_STORAGE_KEY } from '@/data/commerce';
import type { WishlistItem } from '@/types/cart';

interface WishlistContextValue {
    items: WishlistItem[];
    count: number;
    has: (productId: string) => boolean;
    /** Adds or removes. Returns true when the product is now on the wishlist. */
    toggle: (item: WishlistItem) => boolean;
    remove: (productId: string) => void;
    hydrated: boolean;
}

const WishlistContext = createContext<WishlistContextValue | null>(null);

function sanitize(raw: unknown): WishlistItem[] {
    if (!Array.isArray(raw)) return [];
    return raw.filter(
        (i): i is WishlistItem =>
            !!i && typeof i === 'object' && typeof i.productId === 'string' && typeof i.slug === 'string' && typeof i.name === 'string' && typeof i.image === 'string' && typeof i.price === 'number',
    );
}

/** Wishlist state in localStorage (empty first render, filled after mount, like the cart). */
export function WishlistProvider({ children }: { children: ReactNode }) {
    const [items, setItems] = useState<WishlistItem[]>([]);
    const [hydrated, setHydrated] = useState(false);

    useEffect(() => {
        const read = () => {
            try {
                setItems(sanitize(JSON.parse(window.localStorage.getItem(WISHLIST_STORAGE_KEY) ?? '[]')));
            } catch {
                setItems([]);
            }
        };
        read();
        setHydrated(true);
        const onStorage = (event: StorageEvent) => event.key === WISHLIST_STORAGE_KEY && read();
        window.addEventListener('storage', onStorage);
        return () => window.removeEventListener('storage', onStorage);
    }, []);

    useEffect(() => {
        if (!hydrated) return;
        try {
            window.localStorage.setItem(WISHLIST_STORAGE_KEY, JSON.stringify(items));
        } catch {
            /* ignore */
        }
    }, [items, hydrated]);

    const has = useCallback((id: string) => items.some((i) => i.productId === id), [items]);

    const toggle = useCallback(
        (item: WishlistItem) => {
            const exists = items.some((i) => i.productId === item.productId);
            setItems((current) => (exists ? current.filter((i) => i.productId !== item.productId) : [item, ...current]));
            return !exists;
        },
        [items],
    );

    const value = useMemo<WishlistContextValue>(
        () => ({ items, count: items.length, has, toggle, remove: (id) => setItems((c) => c.filter((i) => i.productId !== id)), hydrated }),
        [items, has, toggle, hydrated],
    );

    return <WishlistContext.Provider value={value}>{children}</WishlistContext.Provider>;
}

export function useWishlist(): WishlistContextValue {
    const ctx = useContext(WishlistContext);
    if (!ctx) throw new Error('useWishlist must be used inside <CommerceProvider>.');
    return ctx;
}

export function useOptionalWishlist(): WishlistContextValue | null {
    return useContext(WishlistContext);
}
