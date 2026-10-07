import type { HeroSlide } from '@/types/hero';

/**
 * Hero carousel content. Each slide references a real product via
 * `productId` (see data/products.ts) — the hero never stores its own copy
 * of price/image/specs, so "View Product" always lands on a page that
 * actually matches what was shown. Add a slide by adding an entry here;
 * remove the product it's no longer needed.
 */
export const heroSlides: HeroSlide[] = [
    {
        id: 'slide-laptops',
        productId: 'dell-latitude-5440',
        eyebrow: 'New Arrival',
        headline: 'Power That Moves',
        headlineHighlight: 'With You.',
        description:
            'The latest Dell Latitude and Lenovo ThinkPad laptops — engineered for business, built for speed. Free delivery countrywide.',
        ctaLabel: 'Shop Laptops',
        ctaHref: '/shop/laptops',
    },
    {
        id: 'slide-gaming',
        productId: 'dell-g15-gaming',
        eyebrow: 'Best Deal',
        headline: 'Built For The',
        headlineHighlight: 'Next Level.',
        description:
            'High-refresh displays, RTX graphics, and RGB precision — gaming laptops and desktops made to dominate.',
        ctaLabel: 'Shop Gaming',
        ctaHref: '/shop/laptops/gaming',
    },
    {
        id: 'slide-enterprise',
        productId: 'dell-poweredge-t340',
        eyebrow: 'For Enterprise',
        headline: 'Infrastructure',
        headlineHighlight: 'You Can Trust.',
        description:
            'Dell PowerEdge and HP ProLiant servers, networking gear, and IT support — scaled for growing businesses.',
        ctaLabel: 'Shop Servers',
        ctaHref: '/shop/servers-networking',
    },
];
