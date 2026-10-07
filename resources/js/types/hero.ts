/**
 * A single hero slide. Deliberately holds only the *copy* for the slide —
 * the product itself (image, price, specs) is looked up by `productId` from
 * `data/products.ts`, so the hero never carries its own duplicate/outdated
 * copy of product data. Swap a slide's `productId` and everything — image,
 * price, the "View Product" link — updates automatically.
 */
export interface HeroSlide {
    id: string;
    /** References Product['id'] in data/products.ts. */
    productId: string;
    eyebrow: string;
    headline: string;
    /** Rendered as a second line, in the gold accent color. */
    headlineHighlight: string;
    description: string;
    /** Label + href for the category-level CTA, e.g. "Shop Laptops" → /shop/laptops. */
    ctaLabel: string;
    ctaHref: string;
}
