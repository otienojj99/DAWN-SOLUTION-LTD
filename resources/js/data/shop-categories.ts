import type { ShopCategory } from '@/types/category';

export const shopCategorySection = {
    title: 'Shop by Category',
    description: 'Browse our full range across every product line',
};

/**
 * Demo category tiles. Ten entries matching the categories seeded in
 * CategorySeeder. When categories come from the database, project each
 * row to this shape (name → label, slug → `/shop/${slug}`, image, and a
 * `withCount('products')` count) and nothing in the components changes.
 */
export const shopCategories: ShopCategory[] = [
    {
        id: 'laptops',
        label: 'Laptops',
        href: '/shop/laptops',
        image: 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=300&q=80',
        productCount: 120,
    },
    {
        id: 'desktops',
        label: 'Desktops',
        href: '/shop/desktops',
        image: 'https://images.unsplash.com/photo-1587831990711-23ca6441447b?w=300&q=80',
        productCount: 65,
        badge: 'New',
    },
    {
        id: 'servers',
        label: 'Servers',
        href: '/shop/servers-networking',
        image: 'https://images.unsplash.com/photo-1591405351990-4726e331f141?w=300&q=80',
        productCount: 30,
    },
    {
        id: 'networking',
        label: 'Networking',
        href: '/shop/servers-networking/networking',
        image: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=300&q=80',
        productCount: 45,
    },
    {
        id: 'accessories',
        label: 'Accessories',
        href: '/shop/accessories',
        image: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=300&q=80',
        productCount: 200,
        badge: 'Hot',
    },
    {
        id: 'monitors',
        label: 'Monitors',
        href: '/shop/monitors',
        image: 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=300&q=80',
        productCount: 54,
    },
    {
        id: 'gaming',
        label: 'Gaming Gear',
        href: '/shop/laptops/gaming',
        image: 'https://images.unsplash.com/photo-1612287230202-1ff1d85d1bdf?w=300&q=80',
        productCount: 80,
    },
    {
        id: 'printers',
        label: 'Printers',
        href: '/shop/printers',
        image: 'https://images.unsplash.com/photo-1612815154858-60aa4c59eabd?w=300&q=80',
        productCount: 28,
    },
    {
        id: 'components',
        label: 'Components',
        href: '/shop/components',
        image: 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=300&q=80',
        productCount: 140,
    },
    {
        id: 'software',
        label: 'Software',
        href: '/shop/software-security',
        image: 'https://images.unsplash.com/photo-1585792180666-f7347c490ee2?w=300&q=80',
        productCount: 18,
    },
];
