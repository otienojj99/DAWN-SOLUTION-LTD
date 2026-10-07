import type { Product } from '@/types/product';

/**
 * Demo product catalog. In production this array is replaced by data from
 * your backend (an Inertia shared prop, an API fetch, or Eloquent query
 * results serialized the same shape) — nothing that *consumes* `Product`
 * objects (HeroSlide, ProductCard, the product page) needs to change when
 * that swap happens, since they all import the `Product` type, not this
 * file's contents.
 *
 * `slug` must match the route your product detail page lives at —
 * currently assumed to be `/products/:slug`. Change `productHref()` below
 * if your routing differs.
 */
export const products: Product[] = [
    {
        id: 'dell-latitude-5440',
        slug: 'dell-latitude-5440',
        name: 'Latitude 5440 Business Laptop',
        brand: 'Dell',
        category: 'Laptops',
        image: 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=1000&q=80',
        price: 98000,
        currency: 'KES',
        specs: [
            { label: 'Processor', value: 'Intel i7, 13th Gen' },
            { label: 'Memory', value: '16GB RAM' },
            { label: 'Storage', value: '512GB SSD' },
        ],
        rating: 4.8,
        reviewCount: 94,
        inStock: true,
    },
    {
        id: 'dell-g15-gaming',
        slug: 'dell-g15-gaming-laptop',
        name: 'G15 Gaming Laptop',
        brand: 'Dell',
        category: 'Laptops',
        image: 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=1000&q=80',
        price: 148000,
        currency: 'KES',
        specs: [
            { label: 'Graphics', value: 'RTX 4060' },
            { label: 'Display', value: '165Hz' },
            { label: 'Memory', value: '16GB RAM' },
        ],
        rating: 4.9,
        reviewCount: 340,
        inStock: true,
    },
    {
        id: 'dell-poweredge-t340',
        slug: 'dell-poweredge-t340-server',
        name: 'PowerEdge T340 Tower Server',
        brand: 'Dell',
        category: 'Servers & Networking',
        image: 'https://images.unsplash.com/photo-1591405351990-4726e331f141?w=1000&q=80',
        price: 285000,
        currency: 'KES',
        specs: [
            { label: 'Processor', value: 'Intel Xeon' },
            { label: 'Memory', value: '32GB+ ECC RAM' },
            { label: 'Storage', value: 'RAID Configured' },
        ],
        rating: 4.5,
        reviewCount: 44,
        inStock: true,
    },
];

/** Looks up a product by id. Throws in dev if a HeroSlide references a bad id — better to fail loudly than silently render a blank slide. */
export function getProductById(id: string): Product {
    const product = products.find((p) => p.id === id);
    if (!product) {
        throw new Error(`[data/products] No product found with id "${id}". Check data/hero-slides.ts for a stale productId.`);
    }
    return product;
}

/** Builds the product detail page URL for a product. Centralized here so the route shape only needs to change in one place. */
export function productHref(product: Pick<Product, 'slug'>): string {
    return `/products/${product.slug}`;
}
