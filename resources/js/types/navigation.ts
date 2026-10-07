import type { LucideIcon } from 'lucide-react';

/**
 * A single link used in the utility bar (left "For Business / Enterprise..."
 * group and right "Clearance Sale / New Arrivals..." group).
 */
export interface UtilityLink {
    label: string;
    href: string;
    /** Marks a link as "hot" (e.g. Clearance Sale, Best Deals) for visual emphasis. */
    emphasis?: boolean;
}

/**
 * Generic navigation item — used for simple links such as the account
 * dropdown menu items or mobile drawer links that don't need extra metadata.
 */
export interface NavigationItem {
    label: string;
    href: string;
    icon?: LucideIcon;
}

/** A single link inside a category mega menu column. */
export interface CategoryLink {
    label: string;
    href: string;
    badge?: string;
}

/** A labeled group of links inside a category's mega menu (e.g. "Shop by Brand"). */
export interface CategoryColumn {
    heading: string;
    links: CategoryLink[];
}

/**
 * A top-level category in the main navigation (e.g. Laptops, Accessories).
 * `columns` is optional — categories without it render as a plain link
 * with no mega menu (e.g. Printers, Deals).
 */
export interface Category {
    id: string;
    label: string;
    href: string;
    /** Renders the item with the "All Categories" gold-pill treatment. */
    isPrimary?: boolean;
    columns?: CategoryColumn[];
}

/** A single message shown in the announcement/info bar. */
export interface Announcement {
    id: string;
    message: string;
    href?: string;
}

/** Shared shape for the wishlist/cart icon buttons in the main header. */
export interface IconActionProps {
    count?: number;
    label: string;
    icon: LucideIcon;
    href?: string;
    onClick?: () => void;
}
