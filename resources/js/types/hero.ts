/**
 * A single hero slide. Product details are supplied separately from the
 * homepage product data, so this type holds only slide copy.
 */
export interface HeroSlide {
    id: string;
    eyebrow: string;
    headline: string;
    /** Rendered as a second line, in the gold accent color. */
    headlineHighlight: string;
    description: string;
    /** Label + href for the category-level CTA, e.g. "Shop Laptops" → /shop/laptops. */
    ctaLabel: string;
    ctaHref: string;
}
