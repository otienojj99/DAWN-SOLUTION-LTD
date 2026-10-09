import type { UtilityLink } from '@/types/navigation';

/**
 * Right-hand group of the utility bar — answers "what's the best offer
 * right now?". `emphasis: true` gets the small gold "hot" dot treatment.
 */
export const promotionalLinks: UtilityLink[] = [
    { label: 'Clearance Sale', href: '/shop/deals/clearance',    emphasis: true },
    { label: 'New Arrivals',   href: '/shop/new-arrivals' },
    { label: 'Discounts',      href: '/shop/deals/discounts' },
    { label: 'Best Deals',     href: '/shop/deals/best',         emphasis: true },
];
