import { usePage } from '@inertiajs/react';
import { useState } from 'react';
import { UtilityBar } from './UtilityBar';
import { AnnouncementBar } from './AnnouncementBar';
import { MainHeader } from './MainHeader';
import { CategoryNavigation } from './CategoryNavigation';
import { MobileNavigation } from './MobileNavigation';
// import { categories } from '@/data/categories';
import { announcements } from '@/data/announcements';
import { toNavCategories } from '@/types/adapters';

export interface HeaderProps {
    logoSrc?: string;
    wishlistCount?: number;
    cartCount?: number;
    /** Pass the authenticated user's display name; omit to show "Sign In". */
    userName?: string;
    onSearch?: (query: string) => void;
}

interface CategoryProp {
    id: number;
    name: string;
    slug: string;
    href: string;
    children?: CategoryProp[];
}

/**
 * Top-level header composer. This file intentionally contains almost no
 * markup of its own — it only lays out the five tiers (utility bar,
 * announcement bar, main header, category nav, mobile drawer) and wires
 * the one piece of shared state the mobile drawer needs (open/closed).
 *
 * Usage (e.g. in an Inertia layout):
 *
 *   import { Header } from '@/components/header/Header';
 *   import logo from '../../images/dawn-logo.png';
 *
 *   <Header
 *     logoSrc={logo}
 *     wishlistCount={wishlist.count}
 *     cartCount={cart.count}
 *     userName={auth.user?.name}
 *     onSearch={(q) => router.get('/shop', { q })}
 *   />
 */
export function Header({ logoSrc, wishlistCount, cartCount, userName, onSearch }: HeaderProps) {
    const { categoryTree } = usePage<{ categoryTree: CategoryProp[] }>().props;
    const navCategories = toNavCategories(categoryTree);
    const [mobileNavOpen, setMobileNavOpen] = useState(false);

    return (
        <header>
            <UtilityBar />
            <AnnouncementBar announcements={announcements} />
            <MainHeader
                logoSrc={logoSrc}
                wishlistCount={wishlistCount}
                cartCount={cartCount}
                userName={userName}
                onSearch={onSearch}
                onOpenMobileMenu={() => setMobileNavOpen(true)}
            />
            <CategoryNavigation categories={navCategories} />

            <MobileNavigation
                categories={navCategories}
                open={mobileNavOpen}
                onClose={() => setMobileNavOpen(false)}
            />
        </header>
    );
}
