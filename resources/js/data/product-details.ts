import { defaultShippingInfo } from '@/data/product-config';
import { shopProducts } from '@/data/shop-products';
import type { ProductDetail } from '@/types/product-detail';
import type { ShopProduct } from '@/types/shop';

/**
 * Demo product detail content. Two products have full hand-written detail
 * (a three-level laptop and a two-level desktop, so both "related" layouts
 * can be seen); every other product in the shop gets a sensible page built
 * from its card data by `deriveDetail()`, so every card links to a working page.
 *
 * In production, return the same `ProductDetail` shape from your controller
 * (`Inertia::render('Product', ['product' => ...])`) and delete this file.
 */

const IMG = (id: string, w: number) => `https://images.unsplash.com/photo-${id}?w=${w}&q=80`;

const explicit: Record<string, Omit<ProductDetail, keyof ShopProduct>> = {
    'dell-vostro-3550': {
        sku: 'DEL-VOS3550-I5',
        shortDescription:
            'A dependable, no-fuss business laptop built for everyday office work: solid performance, a durable chassis, and enough power to handle spreadsheets, video calls, and multitasking without slowing down.',
        gallery: [
            { src: IMG('1496181133206-80ce9b88a853', 1000), thumb: IMG('1496181133206-80ce9b88a853', 160), alt: 'Dell Vostro 3550 front view' },
            { src: IMG('1517336714731-489689fd1ca8', 1000), thumb: IMG('1517336714731-489689fd1ca8', 160), alt: 'Dell Vostro 3550 keyboard' },
            { src: IMG('1588872657578-7efd1f1555ed', 1000), thumb: IMG('1588872657578-7efd1f1555ed', 160), alt: 'Dell Vostro 3550 side ports' },
            { src: IMG('1541807084-5c52b6b3adef', 1000), thumb: IMG('1541807084-5c52b6b3adef', 160), alt: 'Dell Vostro 3550 open on a desk' },
            { src: IMG('1587829741301-dc798b83add3', 1000), thumb: IMG('1587829741301-dc798b83add3', 160), alt: 'Dell Vostro 3550 with accessories' },
        ],
        variants: [
            {
                id: 'ram',
                label: 'RAM',
                defaultValue: '16GB',
                options: [
                    { value: '8GB', priceDelta: -8000, skuPart: '8G' },
                    { value: '16GB', skuPart: '16G' },
                    { value: '32GB', priceDelta: 14000, skuPart: '32G' },
                ],
            },
            {
                id: 'storage',
                label: 'Storage',
                defaultValue: '512GB SSD',
                options: [
                    { value: '256GB SSD', priceDelta: -6000, skuPart: '256' },
                    { value: '512GB SSD', skuPart: '512' },
                    { value: '1TB SSD', priceDelta: 11000, skuPart: '1T' },
                ],
            },
            {
                id: 'color',
                label: 'Color',
                defaultValue: 'Carbon Black',
                options: [
                    { value: 'Carbon Black', swatch: '#2B2B2B', skuPart: 'BK' },
                    { value: 'Titan Grey', swatch: '#B0B4BA', skuPart: 'GY' },
                ],
            },
        ],
        tags: ['Business Laptop', 'Intel i5', 'Windows 11', 'Corporate', 'Dell'],
        overview:
            'The Dell Vostro 3550 is designed for small businesses and everyday professionals who need dependable performance without paying for features they won\'t use. Its durable chassis, responsive keyboard, and all-day battery life make it a practical choice for office environments, remote work, and light travel.',
        highlights: [
            '13th Gen Intel Core i5 processor for smooth multitasking',
            '16GB RAM handles spreadsheets, browser tabs, and video calls simultaneously',
            '512GB SSD for fast boot times and ample storage',
            'Full HD anti-glare display, comfortable for long work sessions',
            'Built-in security features including a fingerprint reader',
        ],
        specTable: [
            { label: 'Processor', value: 'Intel Core i5-1335U (13th Gen), up to 4.6GHz' },
            { label: 'RAM', value: '16GB DDR4 (upgradable to 32GB)' },
            { label: 'Storage', value: '512GB NVMe SSD' },
            { label: 'Display', value: '15.6" Full HD (1920x1080), Anti-Glare' },
            { label: 'Graphics', value: 'Intel Iris Xe Graphics (Integrated)' },
            { label: 'Operating System', value: 'Windows 11 Pro (Genuine License)' },
            { label: 'Battery Life', value: 'Up to 8 hours (typical office use)' },
            { label: 'Ports', value: '2x USB-A, 1x USB-C, HDMI, RJ45, 3.5mm Audio Jack' },
            { label: 'Connectivity', value: 'Wi-Fi 6, Bluetooth 5.2' },
            { label: 'Weight', value: '1.7 kg' },
            { label: 'Color Options', value: 'Carbon Black, Titan Grey' },
            { label: 'Warranty', value: '1 Year Manufacturer Warranty (Dell)' },
        ],
        shippingInfo: defaultShippingInfo,
        videos: [
            {
                id: 'vostro-3550-overview',
                title: 'Dell Vostro 3550: overview and first look',
                kind: 'file',
                // Sample clip so the player can be seen working. Replace with your uploaded product video.
                src: 'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4',
                poster: IMG('1496181133206-80ce9b88a853', 1200),
                duration: '0:06',
            },
        ],
        reviews: [
            { id: 'r1', author: 'James M.', rating: 5, date: '2026-10-27', verified: true, text: 'Bought 5 units for our office. Setup was quick and delivery was faster than expected. Battery easily lasts through a full workday.' },
            { id: 'r2', author: 'Sarah W.', rating: 4, date: '2026-10-08', verified: true, text: 'Good value for the price. Keyboard feels solid, screen is decent for office work. Only wish it had a slightly brighter display.' },
            { id: 'r3', author: 'David K.', rating: 5, date: '2026-09-30', verified: true, text: 'Reliable machine, no issues after 3 months of daily use. Support team was also helpful when I had a setup question.' },
            { id: 'r4', author: 'Grace N.', rating: 4, date: '2026-09-14', verified: true, text: 'Light enough to carry around all day and the fingerprint login is quick. Fans stay quiet under normal use.' },
        ],
        ratingBreakdown: { 5: 58, 4: 17, 3: 6, 2: 3, 1: 2 },
    },

    'dell-optiplex-7010': {
        sku: 'DEL-OPT7010-SFF',
        shortDescription: 'A compact business desktop that fits behind a monitor or under a desk, with the performance and manageability office fleets need.',
        gallery: [
            { src: IMG('1587831990711-23ca6441447b', 1000), thumb: IMG('1587831990711-23ca6441447b', 160), alt: 'Dell OptiPlex 7010 small form factor desktop' },
            { src: IMG('1527443224154-c4a3942d3acf', 1000), thumb: IMG('1527443224154-c4a3942d3acf', 160), alt: 'OptiPlex 7010 on a desk with a monitor' },
        ],
        variants: [
            {
                id: 'ram',
                label: 'RAM',
                defaultValue: '16GB',
                options: [
                    { value: '8GB', priceDelta: -7000, skuPart: '8G' },
                    { value: '16GB', skuPart: '16G' },
                    { value: '32GB', priceDelta: 13000, skuPart: '32G' },
                ],
            },
            {
                id: 'storage',
                label: 'Storage',
                defaultValue: '512GB SSD',
                options: [
                    { value: '256GB SSD', priceDelta: -5000, skuPart: '256' },
                    { value: '512GB SSD', skuPart: '512' },
                    { value: '1TB SSD', priceDelta: 10000, skuPart: '1T' },
                ],
            },
        ],
        tags: ['Business Desktop', 'Small Form Factor', 'Intel i5', 'Windows 11', 'Corporate'],
        overview: 'The OptiPlex 7010 Small Form Factor packs full desktop performance into a footprint that saves desk space. It is built for fleets: easy to deploy, easy to manage and easy to service.',
        highlights: [
            'Intel Core i5 processor for everyday multitasking',
            'Small form factor that fits almost anywhere',
            'Fast NVMe SSD storage for quick start-up',
            'Supports up to three displays',
            'Tool-less internal access for quick upgrades and repairs',
        ],
        specTable: [
            { label: 'Processor', value: 'Intel Core i5 (13th Gen)' },
            { label: 'RAM', value: '16GB DDR4 (up to 64GB)' },
            { label: 'Storage', value: '512GB NVMe SSD' },
            { label: 'Graphics', value: 'Intel UHD Graphics (Integrated)' },
            { label: 'Operating System', value: 'Windows 11 Pro (Genuine License)' },
            { label: 'Ports', value: '6x USB, HDMI, 2x DisplayPort, RJ45, Audio' },
            { label: 'Connectivity', value: 'Gigabit Ethernet, optional Wi-Fi 6' },
            { label: 'Warranty', value: '1 Year Manufacturer Warranty (Dell)' },
        ],
        shippingInfo: defaultShippingInfo,
        videos: [],
        reviews: [
            { id: 'r1', author: 'Peter O.', rating: 5, date: '2026-10-19', verified: true, text: 'We fitted 12 of these in our accounts office. Compact, quiet and they were ready to use out of the box.' },
            { id: 'r2', author: 'Lucy A.', rating: 4, date: '2026-09-25', verified: true, text: 'Very good for everyday office work. Wi-Fi is an optional extra, so we used cable.' },
        ],
        ratingBreakdown: { 5: 25, 4: 11, 3: 3, 2: 1, 1: 0 },
        relatedIds: ['logitech-mk540', 'dell-p2423', 'apc-back-ups-650', 'logitech-c920'],
    },
};

/** A usable detail page for any catalogue product that has no hand-written content. */
function deriveDetail(product: ShopProduct): ProductDetail {
    const specTable = product.specs.length ? product.specs : [{ label: 'Brand', value: product.brand }];
    return {
        ...product,
        sku: product.id.toUpperCase(),
        shortDescription: `${product.brand} ${product.name}. Genuine product with warranty, backed by Dawn Solution support.`,
        gallery: [{ src: product.image, alt: product.name }],
        variants: [],
        tags: [product.brand, product.category],
        overview: `The ${product.name} from ${product.brand} is available from Dawn Solution with a manufacturer warranty and local support. Contact us for bulk pricing, configuration and delivery.`,
        highlights: product.specs.map((s) => `${s.label}: ${s.value}`),
        specTable,
        shippingInfo: defaultShippingInfo,
        videos: [],
        reviews: [],
    };
}

export function getProductDetail(slug: string, catalogue: ShopProduct[] = shopProducts): ProductDetail | undefined {
    const product = catalogue.find((p) => p.slug === slug);
    if (!product) return undefined;
    const extra = explicit[product.id];
    return extra ? { ...product, ...extra } : deriveDetail(product);
}
