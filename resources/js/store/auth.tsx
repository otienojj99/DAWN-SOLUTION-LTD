import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import type { ReactNode } from 'react';
import type { AuthUser, LoginCredentials } from '@/types/cart';

interface AuthContextValue {
    user: AuthUser | null;
    /** Rejects with an Error whose message is safe to show the visitor. */
    login: (credentials: LoginCredentials) => Promise<void>;
    logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextValue | null>(null);

interface AuthProviderProps {
    /** The signed-in user from the server (Inertia: `usePage().props.auth.user`), or null. */
    user?: AuthUser | null;
    /**
     * Signs the visitor in. Either return the user, or resolve after a
     * full Inertia visit that re-renders with the new `user` prop. Throw an
     * Error("Incorrect email or password.") to show a message in the panel.
     */
    onLogin?: (credentials: LoginCredentials) => Promise<AuthUser | void>;
    onLogout?: () => Promise<void> | void;
    children: ReactNode;
}

export function AuthProvider({ user: userProp = null, onLogin, onLogout, children }: AuthProviderProps) {
    const [user, setUser] = useState<AuthUser | null>(userProp);

    // Follow the server when it sends a different user (after login/logout page visits).
    useEffect(() => setUser(userProp), [userProp]);

    const login = useCallback(
        async (credentials: LoginCredentials) => {
            // Never pretend a sign-in worked: without a handler the visitor is told it isn't connected.
            if (!onLogin) throw new Error('Sign in is not connected yet.');
            const signedIn = await onLogin(credentials);
            if (signedIn) setUser(signedIn);
        },
        [onLogin],
    );

    const logout = useCallback(async () => {
        await onLogout?.();
        setUser(null);
    }, [onLogout]);

    const value = useMemo(() => ({ user, login, logout }), [user, login, logout]);
    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth(): AuthContextValue {
    const ctx = useContext(AuthContext);
    if (!ctx) throw new Error('useAuth must be used inside <CommerceProvider>.');
    return ctx;
}

export function useOptionalAuth(): AuthContextValue | null {
    return useContext(AuthContext);
}
