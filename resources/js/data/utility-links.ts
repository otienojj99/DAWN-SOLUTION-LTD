import type { UtilityLink } from '@/types/navigation';

/**
 * Left-hand group of the utility bar — answers "who is this for?".
 * Swap `href` for real route()/Ziggy helpers once wired to Laravel routes.
 */
export const utilityLinks: UtilityLink[] = [
    { label: 'For Business', href: '/business' },
    { label: 'Enterprise', href: '/enterprise' },
    { label: 'Education', href: '/education' },
    { label: 'Gaming', href: '/gaming' },
    { label: 'Lifestyle', href: '/lifestyle' },
];
