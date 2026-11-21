import type { ReactNode } from 'react';
import { AuthProvider } from '@/store/auth';
import { CartProvider, useCart } from '@/store/cart';
import { WishlistProvider } from '@/store/wishlist';
import type { AuthUser, LoginCredentials, ServerCart } from '@/types/cart';
import { CartDrawer } from './CartDrawer';

interface CommerceProviderProps {
    children: ReactNode;
    initialCart?: ServerCart | null;
    /** Signed-in user from the server, or null/undefined for a guest. */
    user?: AuthUser | null;
    onLogin?: (credentials: LoginCredentials) => Promise<AuthUser | void>;
    onLogout?: () => Promise<void> | void;
}

/** Screen-reader announcement for "added to cart", which sighted users get from the icon animation. */
function CartAnnouncer() {
    const { lastAdded } = useCart();
    return (
        <div role="status" aria-live="polite" className="sr-only" data-testid="cart-announcer">
            {lastAdded ? `Added ${lastAdded.name} to cart` : ''}
        </div>
    );
}

/**
 * Wrap the site once, ideally in your persistent Inertia layout, so the cart,
 * wishlist and signed-in user stay alive between page visits. It also renders the cart drawer.
 */
export function CommerceProvider({ children, initialCart, user, onLogin, onLogout }: CommerceProviderProps) {
    return (
        <AuthProvider user={user} onLogin={onLogin} onLogout={onLogout}>
            <WishlistProvider>
                <CartProvider initialCart={initialCart}>
                    {children}
                    <CartDrawer />
                    <CartAnnouncer />
                </CartProvider>
            </WishlistProvider>
        </AuthProvider>
    );
}
