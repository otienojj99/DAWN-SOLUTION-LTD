import type { HeroSlide } from '@/types/hero';

/**
 * Hero carousel copy. Product details come from the homepage product data.
 * Add or remove slides here to control the number of featured products.
 */
export const heroSlides: HeroSlide[] = [
    {
        id: 'slide-laptops',
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
        eyebrow: 'For Enterprise',
        headline: 'Infrastructure',
        headlineHighlight: 'You Can Trust.',
        description:
            'Dell PowerEdge and HP ProLiant servers, networking gear, and IT support — scaled for growing businesses.',
        ctaLabel: 'Shop Servers',
        ctaHref: '/shop/servers-networking',
    },
];
