import type { PromoOffer } from '@/types/promo';

export const promoSection = {
    title: "Today's Best Offers",
    description: 'Handpicked deals across laptops, servers, gaming and accessories',
    viewAllLabel: 'View All Deals',
    viewAllHref: '/deals',
};

/**
 * Demo promo cards. Keep exactly one `featured` offer — it renders as the
 * large card spanning two rows. The remaining `standard` offers fill the
 * 2x2 grid beside it (4 is the designed-for count).
 *
 * In production, serve this same shape from your backend / CMS so the
 * marketing team can change offers without a deploy.
 */
export const promoOffers: PromoOffer[] = [
    {
        id: 'offer-laptops-clearance',
        size: 'featured',
        tag: 'Clearance Sale',
        tagStyle: 'accent',
        title: 'Up to 30% Off Business Laptops',
        subtitle: 'Lenovo ThinkPad, Dell Latitude & HP EliteBook',
        ctaLabel: 'Shop Laptops',
        href: '/shop/laptops?filter=discounted',
        image: 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=1000&q=80',
        imageAlt: 'Business laptop on a desk',
    },
    {
        id: 'offer-gaming-desktops',
        size: 'standard',
        tag: 'New Stockhssjsjsjs',
        tagStyle: 'glass',
        title: 'Gaming Desktops',
        subtitle: 'RTX-powered rigs, ready to ship',
        ctaLabel: 'Explore',
        href: '/shop/desktops/gaming',
        image: 'https://images.unsplash.com/photo-1587831990711-23ca6441447b?w=800&q=80',
        imageAlt: 'Gaming desktop PC with RGB lighting',
    },
    {
        id: 'offer-servers',
        size: 'standard',
        tag: 'For Enterprise',
        tagStyle: 'glass',
        title: 'Servers & Racks',
        subtitle: 'Dell PowerEdge, HP ProLiant',
        ctaLabel: 'Get a Quote',
        href: '/shop/servers-networking',
        image: 'https://images.unsplash.com/photo-1591405351990-4726e331f141?w=800&q=80',
        imageAlt: 'Server rack in a data center',
    },
    {
        id: 'offer-accessories',
        size: 'standard',
        tag: 'Best Deals',
        tagStyle: 'accent',
        title: 'Accessories',
        subtitle: 'Logitech keyboards, mice & headsets',
        ctaLabel: 'Shop Now',
        href: '/shop/accessories',
        image: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800&q=80',
        imageAlt: 'Keyboard and mouse',
    },
    {
        id: 'offer-monitors',
        size: 'standard',
        tag: 'Discounts',
        tagStyle: 'glass',
        title: 'Monitors',
        subtitle: 'Curved, 4K & gaming displays',
        ctaLabel: 'View Range',
        href: '/shop/monitors',
        image: 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=800&q=80',
        imageAlt: 'Computer monitor on a desk',
    },
];
