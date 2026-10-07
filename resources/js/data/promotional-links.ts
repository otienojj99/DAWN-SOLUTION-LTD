import type { UtilityLink } from '@/types/navigation';

/**
 * Right-hand group of the utility bar — answers "what's the best offer
 * right now?". `emphasis: true` gets the small gold "hot" dot treatment.
 */
export const promotionalLinks: UtilityLink[] = [
    { label: 'Clearance Sale', href: '/deals/clearance', emphasis: true },
    { label: 'New Arrivals', href: '/new-arrivals' },
    { label: 'Discounts', href: '/deals/discounts' },
    { label: 'Best Deals', href: '/deals/best', emphasis: true },
];
