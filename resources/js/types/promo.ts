/**
 * A single promotional card in the "Today's Best Offers" grid.
 *
 * Offers are campaign/collection banners (e.g. "Up to 30% off business
 * laptops"), so they link to a collection or category URL rather than a
 * single product page. `size` controls grid placement: exactly one
 * `featured` offer is expected — it spans both rows on desktop.
 */
export interface PromoOffer {
    id: string;
    size: 'featured' | 'standard';
    /** Short badge text, e.g. "Clearance Sale", "New Stock". Ties back to the utility bar labels. */
    tag: string | null;
    /** `accent` = solid gold badge, `glass` = translucent white badge. */
    tagStyle?: 'accent' | 'glass';
    title: string;
    subtitle: string | null;
    ctaLabel: string;
    href: string;
    image: string | null;
    imageAlt: string | null;
}
