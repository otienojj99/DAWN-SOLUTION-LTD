import { createContext, useCallback, useContext, useEffect, useMemo, useRef, useState } from 'react';
import type { ReactNode } from 'react';
import { router } from '@inertiajs/react';
import { CART_STORAGE_KEY } from '@/data/commerce';
import { calculateTotals, sanitizeLines } from '@/lib/cart-math';
import { serverCartToLines } from '@/lib/cart-adapter';
import type { CartLine, CartTotals, NewCartLine, ServerCart } from '@/types/cart';

interface CartContextValue {
    lines: CartLine[];
    itemCount: number;
    totals: CartTotals;
    add: (line: NewCartLine, quantity?: number) => void;
    setQuantity: (key: string, quantity: number) => void;
    remove: (key: string) => void;
    clear: () => void;
    drawerOpen: boolean;
    openDrawer: () => void;
    closeDrawer: () => void;
    pulse: number;
    lastAdded: { name: string; quantity: number; id: number } | null;
    hydrated: boolean;
    busy: boolean;
}

const CartContext = createContext<CartContextValue | null>(null);

export function CartProvider({ children, initialCart }: { children: ReactNode; initialCart?: ServerCart | null }) {
    const [pageCart, setPageCart] = useState<ServerCart | null>(initialCart ?? null);

    // Start from server cart if present, else from localStorage
    const [lines, setLines] = useState<CartLine[]>(() => {
        if (pageCart) return serverCartToLines(pageCart);
        if (typeof window === 'undefined') return [];
        try {
            return sanitizeLines(JSON.parse(window.localStorage.getItem(CART_STORAGE_KEY) ?? '[]'));
        } catch {
            return [];
        }
    });

    const [drawerOpen, setDrawerOpen] = useState(false);
    const [pulse, setPulse] = useState(0);
    const [lastAdded, setLastAdded] = useState<CartContextValue['lastAdded']>(null);
    const [busy, setBusy] = useState(false);
    const announcementId = useRef(0);
    const migratedRef = useRef(false);

    // The provider wraps the Inertia app, so observe page changes instead of calling usePage here.
    useEffect(() => {
        const syncPageCart = (cart: ServerCart | null | undefined) => setPageCart(cart ?? null);
        const unsubscribeNavigate = router.on('navigate', ({ detail }) => {
            syncPageCart(detail.page.props.cart as ServerCart | null | undefined);
        });
        const unsubscribeClientVisit = router.on('clientVisit', ({ detail }) => {
            syncPageCart(detail.page.props.cart as ServerCart | null | undefined);
        });

        return () => {
            unsubscribeNavigate();
            unsubscribeClientVisit();
        };
    }, []);

    // Sync from Inertia when the cart prop changes (navigation, login)
    useEffect(() => {
        if (pageCart) {
            setLines(serverCartToLines(pageCart));
        }
    }, [pageCart]);

    // Persist to localStorage as offline fallback
    useEffect(() => {
        if (typeof window === 'undefined') return;
        try {
            window.localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(lines));
        } catch { /* storage blocked */ }
    }, [lines]);

    // One-time migration: if localStorage had items but server cart is empty, push them
    useEffect(() => {
        if (migratedRef.current) return;
        migratedRef.current = true;

        if (!pageCart || pageCart.lines.length > 0 || lines.length === 0) return;

        (async () => {
            for (const line of lines) {
                try {
                    await request('POST', '/cart/items', {
                        product_id: line.productId,
                        quantity: line.quantity,
                    });
                } catch { /* skip failed items */ }
            }
        })();
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const csrf = typeof document !== 'undefined'
        ? (document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '')
        : '';

    const request = useCallback(async (
        method: 'POST' | 'PATCH' | 'DELETE',
        url: string,
        body?: unknown,
    ): Promise<ServerCart> => {
        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: body ? JSON.stringify(body) : undefined,
        });

        const json = await res.json();
        if (!res.ok) throw new Error(json?.message ?? 'Cart update failed');
        return json.data as ServerCart;
    }, [csrf]);

    const add = useCallback((line: NewCartLine, quantity = 1) => {
        // Optimistic UI update
        setLines(prev => {
            const key = line.sku ?? line.productId;
            const idx = prev.findIndex(l => l.key === key);
            if (idx >= 0) {
                const next = [...prev];
                next[idx] = { ...next[idx], quantity: next[idx].quantity + quantity };
                return next;
            }
            return [...prev, { ...line, key, quantity }];
        });

        setPulse(n => n + 1);
        announcementId.current += 1;
        setLastAdded({ name: line.name, quantity, id: announcementId.current });
        setDrawerOpen(true);

        // Background server sync
        setBusy(true);
        void (async () => {
            try {
                const cart = await request('POST', '/cart/items', {
                    product_id: line.productId,
                    quantity,
                });
                setLines(serverCartToLines(cart));
            } catch (err) {
                console.error('Cart sync failed:', err);
            } finally {
                setBusy(false);
            }
        })();
    }, [request]);

    const setQuantity = useCallback((key: string, quantity: number) => {
        const line = lines.find(l => l.key === key);
        if (!line?.id) return;

        // Optimistic
        setLines(prev =>
            quantity <= 0
                ? prev.filter(l => l.key !== key)
                : prev.map(l => l.key === key ? { ...l, quantity } : l)
        );

        void (async () => {
            try {
                const cart = await request('PATCH', `/cart/items/${line.id}`, { quantity });
                setLines(serverCartToLines(cart));
            } catch (err) {
                console.error('Cart sync failed:', err);
            }
        })();
    }, [lines, request]);

    const remove = useCallback((key: string) => {
        const line = lines.find(l => l.key === key);
        if (!line?.id) return;

        setLines(prev => prev.filter(l => l.key !== key));

        void (async () => {
            try {
                const cart = await request('DELETE', `/cart/items/${line.id}`);
                setLines(serverCartToLines(cart));
            } catch (err) {
                console.error('Cart sync failed:', err);
            }
        })();
    }, [lines, request]);

    const clear = useCallback(() => {
        setLines([]);

        void (async () => {
            try {
                await request('DELETE', '/cart');
            } catch (err) {
                console.error('Cart sync failed:', err);
            }
        })();
    }, [request]);

    const value = useMemo<CartContextValue>(() => ({
        lines,
        itemCount: lines.reduce((s, l) => s + l.quantity, 0),
        totals: calculateTotals(lines),
        add,
        setQuantity,
        remove,
        clear,
        drawerOpen,
        openDrawer: () => setDrawerOpen(true),
        closeDrawer: () => setDrawerOpen(false),
        pulse,
        lastAdded,
        hydrated: true,
        busy,
    }), [lines, add, setQuantity, remove, clear, drawerOpen, pulse, lastAdded, busy]);

    return <CartContext.Provider value={value}>{children}</CartContext.Provider>;
}

export function useCart(): CartContextValue {
    const ctx = useContext(CartContext);
    if (!ctx) throw new Error('useCart must be used inside <CommerceProvider>.');
    return ctx;
}

export function useOptionalCart(): CartContextValue | null {
    return useContext(CartContext);
}