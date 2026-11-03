import type { ProductBadge, ProductSpec } from '@/types/product';
import type { ShopProduct } from '@/types/shop';

/**
 * Demo shop catalogue. Replace with your backend's product list (same
 * `ShopProduct` shape) — nothing in the shop components imports this file
 * except the example page. Images are placeholders from a handful of stock
 * photos; your real product images go in `image`.
 */

const IMG = {
    laptopA: 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&q=80',
    laptopB: 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=800&q=80',
    laptopC: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&q=80',
    pc: 'https://images.unsplash.com/photo-1587831990711-23ca6441447b?w=800&q=80',
    accessory: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800&q=80',
    monitor: 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=800&q=80',
    server: 'https://images.unsplash.com/photo-1591405351990-4726e331f141?w=800&q=80',
} as const;

const ROOT_LABEL: Record<string, string> = {
    laptops: 'Laptops',
    desktops: 'Desktops',
    monitors: 'Monitors',
    servers: 'Servers & Networking',
    networking: 'Servers & Networking',
    accessories: 'Accessories',
};

interface Seed {
    id: string;
    name: string;
    brand: string;
    /** 'laptops/dell/latitude' */
    path: string;
    price: number;
    old?: number;
    cpu?: string;
    ram?: string;
    storage?: string;
    /** Used when the product has no cpu/ram/storage, e.g. a mouse. */
    specs?: ProductSpec[];
    img: keyof typeof IMG;
    rating: number;
    reviews: number;
    /** Omit = in stock. 0 = out of stock. 1-5 shows "Only N left". */
    stock?: number;
    badge?: SeedBadge;
    best?: boolean;
    sold: number;
    added: string;
}

type SeedBadge = 'corporate' | 'new' | 'sale' | 'bestseller';

const BADGES: Record<SeedBadge, ProductBadge> = {
    corporate: { label: 'Corporate', color: 'blue' },
    new: { label: 'New', color: 'green' },
    sale: { label: 'Sale', color: 'red' },
    bestseller: { label: 'Bestseller', color: 'amber' },
};

const seeds: Seed[] = [
    // Laptops: Dell
    { id: 'dell-latitude-5440', name: 'Latitude 5440 Business Laptop', brand: 'Dell', path: 'laptops/dell/latitude', price: 98000, cpu: 'Intel Core i7', ram: '16GB', storage: '512GB SSD', img: 'laptopA', rating: 4.8, reviews: 94, badge: 'corporate', best: true, sold: 310, added: '2026-08-12' },
    { id: 'dell-latitude-7440', name: 'Latitude 7440 Ultralight', brand: 'Dell', path: 'laptops/dell/latitude', price: 165000, cpu: 'Intel Core i7', ram: '32GB', storage: '1TB SSD', img: 'laptopB', rating: 4.9, reviews: 41, badge: 'new', sold: 72, added: '2026-10-02' },
    { id: 'dell-latitude-3440', name: 'Latitude 3440 Essential', brand: 'Dell', path: 'laptops/dell/latitude', price: 62000, old: 69000, cpu: 'Intel Core i5', ram: '8GB', storage: '256GB SSD', img: 'laptopC', rating: 4.4, reviews: 58, badge: 'sale', sold: 205, added: '2026-06-20' },
    { id: 'dell-xps-13', name: 'XPS 13 Plus', brand: 'Dell', path: 'laptops/dell/xps', price: 185000, old: 205000, cpu: 'Intel Core i7', ram: '16GB', storage: '512GB SSD', img: 'laptopA', rating: 4.7, reviews: 63, badge: 'sale', sold: 88, added: '2026-07-14' },
    { id: 'dell-xps-15', name: 'XPS 15 Creator Edition', brand: 'Dell', path: 'laptops/dell/xps', price: 268000, cpu: 'Intel Core i9', ram: '32GB', storage: '1TB SSD', img: 'laptopB', rating: 4.8, reviews: 27, stock: 3, sold: 34, added: '2026-09-05' },
    { id: 'dell-vostro-3510', name: 'Vostro 3510 Office Laptop', brand: 'Dell', path: 'laptops/dell/vostro', price: 54000, cpu: 'Intel Core i5', ram: '8GB', storage: '256GB SSD', img: 'laptopC', rating: 4.2, reviews: 77, sold: 260, added: '2026-03-18' },
    { id: 'dell-vostro-5640', name: 'Vostro 5640 Business Laptop', brand: 'Dell', path: 'laptops/dell/vostro', price: 78000, cpu: 'Intel Core i5', ram: '16GB', storage: '512GB SSD', img: 'laptopA', rating: 4.5, reviews: 36, badge: 'corporate', sold: 120, added: '2026-09-22' },
    { id: 'dell-inspiron-15-3520', name: 'Inspiron 15 3520', brand: 'Dell', path: 'laptops/dell/inspiron', price: 64000, old: 72000, cpu: 'Intel Core i5', ram: '8GB', storage: '512GB SSD', img: 'laptopB', rating: 4.3, reviews: 112, badge: 'sale', best: true, sold: 340, added: '2026-04-09' },
    { id: 'dell-chromebook-3110', name: 'Chromebook 3110 (Education)', brand: 'Dell', path: 'laptops/dell/chromebook', price: 34000, cpu: 'Intel Celeron', ram: '4GB', storage: '64GB eMMC', img: 'laptopC', rating: 4.1, reviews: 49, sold: 180, added: '2026-02-11' },
    // Laptops: Lenovo
    { id: 'lenovo-thinkpad-e14', name: 'ThinkPad E14 Gen 5', brand: 'Lenovo', path: 'laptops/lenovo/thinkpad', price: 88000, cpu: 'Intel Core i5', ram: '16GB', storage: '512GB SSD', img: 'laptopA', rating: 4.7, reviews: 128, badge: 'corporate', best: true, sold: 295, added: '2026-07-30' },
    { id: 'lenovo-thinkpad-t14', name: 'ThinkPad T14 Gen 4', brand: 'Lenovo', path: 'laptops/lenovo/thinkpad', price: 152000, cpu: 'Intel Core i7', ram: '16GB', storage: '512GB SSD', img: 'laptopC', rating: 4.8, reviews: 52, sold: 96, added: '2026-08-25' },
    { id: 'lenovo-thinkpad-x1', name: 'ThinkPad X1 Carbon Gen 11', brand: 'Lenovo', path: 'laptops/lenovo/thinkpad', price: 245000, cpu: 'Intel Core i7', ram: '32GB', storage: '1TB SSD', img: 'laptopB', rating: 4.9, reviews: 19, stock: 2, badge: 'new', sold: 21, added: '2026-10-10' },
    { id: 'lenovo-ideapad-slim-3', name: 'IdeaPad Slim 3', brand: 'Lenovo', path: 'laptops/lenovo/ideapad', price: 56000, old: 63000, cpu: 'Intel Core i3', ram: '8GB', storage: '256GB SSD', img: 'laptopA', rating: 4.3, reviews: 143, badge: 'sale', sold: 410, added: '2026-01-25' },
    { id: 'lenovo-ideapad-5', name: 'IdeaPad 5 Pro', brand: 'Lenovo', path: 'laptops/lenovo/ideapad', price: 92000, cpu: 'AMD Ryzen 7', ram: '16GB', storage: '512GB SSD', img: 'laptopC', rating: 4.6, reviews: 64, sold: 140, added: '2026-05-16' },
    { id: 'lenovo-legion-5', name: 'Legion 5 Gaming Laptop', brand: 'Lenovo', path: 'laptops/lenovo/legion', price: 178000, cpu: 'AMD Ryzen 7', ram: '16GB', storage: '1TB SSD', img: 'laptopB', rating: 4.8, reviews: 88, stock: 4, badge: 'bestseller', best: true, sold: 150, added: '2026-06-02' },
    // Laptops: HP
    { id: 'hp-elitebook-840-g10', name: 'EliteBook 840 G10', brand: 'HP', path: 'laptops/hp/elitebook', price: 172000, cpu: 'Intel Core i7', ram: '16GB', storage: '512GB SSD', img: 'laptopA', rating: 4.8, reviews: 33, badge: 'corporate', sold: 64, added: '2026-09-12' },
    { id: 'hp-probook-450-g10', name: 'ProBook 450 G10', brand: 'HP', path: 'laptops/hp/probook', price: 96000, cpu: 'Intel Core i5', ram: '16GB', storage: '512GB SSD', img: 'laptopC', rating: 4.5, reviews: 71, sold: 175, added: '2026-07-07' },
    { id: 'hp-pavilion-gaming-15', name: 'Pavilion Gaming 15', brand: 'HP', path: 'laptops/hp/pavilion', price: 118000, old: 135000, cpu: 'AMD Ryzen 5', ram: '16GB', storage: '512GB SSD', img: 'laptopB', rating: 4.4, reviews: 99, badge: 'sale', sold: 185, added: '2026-05-28' },
    { id: 'hp-pavilion-15', name: 'Pavilion 15 Everyday', brand: 'HP', path: 'laptops/hp/pavilion', price: 74000, cpu: 'Intel Core i5', ram: '8GB', storage: '512GB SSD', img: 'laptopC', rating: 4.2, reviews: 80, sold: 230, added: '2026-04-21' },
    // Laptops: ASUS
    { id: 'asus-vivobook-15', name: 'Vivobook 15', brand: 'ASUS', path: 'laptops/asus/vivobook', price: 68000, cpu: 'Intel Core i5', ram: '8GB', storage: '512GB SSD', img: 'laptopA', rating: 4.3, reviews: 90, sold: 220, added: '2026-03-03' },
    { id: 'asus-vivobook-pro-16', name: 'Vivobook Pro 16', brand: 'ASUS', path: 'laptops/asus/vivobook', price: 155000, cpu: 'Intel Core i7', ram: '16GB', storage: '1TB SSD', img: 'laptopB', rating: 4.6, reviews: 25, sold: 48, added: '2026-08-30' },
    { id: 'asus-zenbook-14-oled', name: 'Zenbook 14 OLED', brand: 'ASUS', path: 'laptops/asus/zenbook', price: 142000, old: 156000, cpu: 'Intel Core i7', ram: '16GB', storage: '512GB SSD', img: 'laptopC', rating: 4.7, reviews: 38, badge: 'sale', sold: 70, added: '2026-06-27' },
    { id: 'asus-rog-strix-g16', name: 'ROG Strix G16', brand: 'ASUS', path: 'laptops/asus/rog', price: 248000, cpu: 'Intel Core i9', ram: '32GB', storage: '1TB SSD', img: 'laptopB', rating: 4.9, reviews: 45, stock: 0, sold: 52, added: '2026-09-18' },
    // Desktops
    { id: 'dell-optiplex-7010', name: 'OptiPlex 7010 Small Form Factor', brand: 'Dell', path: 'desktops/dell', price: 89000, cpu: 'Intel Core i5', ram: '16GB', storage: '512GB SSD', img: 'pc', rating: 4.6, reviews: 40, badge: 'corporate', sold: 130, added: '2026-07-19' },
    { id: 'hp-prodesk-400-g9', name: 'ProDesk 400 G9 Mini Tower', brand: 'HP', path: 'desktops/hp', price: 72000, cpu: 'Intel Core i5', ram: '8GB', storage: '256GB SSD', img: 'pc', rating: 4.4, reviews: 29, sold: 95, added: '2026-05-05' },
    { id: 'custom-gaming-pc-4070', name: 'Custom Gaming PC (RTX 4070)', brand: 'Dawn Solution', path: 'desktops/custom', price: 210000, cpu: 'Intel Core i7', ram: '32GB', storage: '1TB SSD', img: 'pc', rating: 4.9, reviews: 56, stock: 5, badge: 'bestseller', best: true, sold: 60, added: '2026-08-08' },
    { id: 'custom-workstation', name: 'Custom Workstation (Render & CAD)', brand: 'Dawn Solution', path: 'desktops/custom', price: 310000, cpu: 'Intel Core i9', ram: '64GB', storage: '2TB SSD', img: 'pc', rating: 4.8, reviews: 14, sold: 18, added: '2026-09-29' },
    { id: 'dell-inspiron-aio-24', name: 'Inspiron 24 All-in-One', brand: 'Dell', path: 'desktops/aio', price: 98000, old: 108000, cpu: 'Intel Core i5', ram: '16GB', storage: '512GB SSD', img: 'monitor', rating: 4.5, reviews: 31, badge: 'sale', sold: 77, added: '2026-06-11' },
    { id: 'hp-aio-24', name: 'HP All-in-One 24', brand: 'HP', path: 'desktops/aio', price: 92000, cpu: 'Intel Core i5', ram: '8GB', storage: '512GB SSD', img: 'monitor', rating: 4.3, reviews: 22, sold: 58, added: '2026-04-30' },
    // Monitors
    { id: 'dell-ultrasharp-27-4k', name: 'UltraSharp 27 4K Monitor', brand: 'Dell', path: 'monitors/business', price: 68000, old: 79000, specs: [{ label: 'Size', value: '27 inch 4K' }, { label: 'Panel', value: 'IPS' }, { label: 'Ports', value: 'USB-C, HDMI, DP' }], img: 'monitor', rating: 4.9, reviews: 112, badge: 'sale', best: true, sold: 160, added: '2026-05-20' },
    { id: 'dell-p2423', name: 'Dell P2423 24 inch Monitor', brand: 'Dell', path: 'monitors/business', price: 28500, specs: [{ label: 'Size', value: '24 inch' }, { label: 'Panel', value: 'IPS' }, { label: 'Ports', value: 'HDMI, DP, VGA' }], img: 'monitor', rating: 4.5, reviews: 85, sold: 300, added: '2026-02-02' },
    { id: 'msi-optix-g27', name: 'MSI Optix G27 165Hz', brand: 'MSI', path: 'monitors/gaming', price: 36000, specs: [{ label: 'Size', value: '27 inch' }, { label: 'Refresh', value: '165Hz' }, { label: 'Response', value: '1ms' }], img: 'monitor', rating: 4.6, reviews: 47, stock: 5, sold: 90, added: '2026-07-25' },
    // Servers
    { id: 'dell-poweredge-t340', name: 'PowerEdge T340 Tower Server', brand: 'Dell', path: 'servers/dell-poweredge', price: 265000, cpu: 'Intel Xeon E', ram: '16GB', storage: '2TB HDD', img: 'server', rating: 4.8, reviews: 18, badge: 'corporate', sold: 24, added: '2026-06-15' },
    { id: 'dell-poweredge-r250', name: 'PowerEdge R250 Rack Server', brand: 'Dell', path: 'servers/dell-poweredge', price: 310000, cpu: 'Intel Xeon E', ram: '32GB', storage: '2TB HDD', img: 'server', rating: 4.7, reviews: 11, sold: 16, added: '2026-08-04' },
    { id: 'hp-proliant-ml30', name: 'ProLiant ML30 Gen10 Plus', brand: 'HP', path: 'servers/hp-proliant', price: 240000, cpu: 'Intel Xeon E', ram: '16GB', storage: '1TB HDD', img: 'server', rating: 4.6, reviews: 9, sold: 12, added: '2026-05-12' },
    { id: 'hp-proliant-dl380', name: 'ProLiant DL380 Gen10', brand: 'HP', path: 'servers/hp-proliant', price: 480000, old: 520000, cpu: 'Intel Xeon Silver', ram: '64GB', storage: '4TB HDD', img: 'server', rating: 4.9, reviews: 7, badge: 'sale', sold: 9, added: '2026-09-09' },
    { id: 'apc-smart-ups-1500', name: 'APC Smart-UPS 1500VA', brand: 'APC', path: 'servers/accessories', price: 42000, specs: [{ label: 'Capacity', value: '1500VA / 980W' }, { label: 'Type', value: 'Line interactive' }, { label: 'Outlets', value: '8' }], img: 'server', rating: 4.7, reviews: 26, sold: 70, added: '2026-04-14' },
    // Networking
    { id: 'tplink-omada-sg3428', name: 'TP-Link Omada 24-Port Switch', brand: 'TP-Link', path: 'networking/routers-switches', price: 38000, specs: [{ label: 'Ports', value: '24 x Gigabit + 4 SFP' }, { label: 'Type', value: 'Managed L2+' }, { label: 'Mount', value: 'Rack 1U' }], img: 'server', rating: 4.6, reviews: 34, sold: 85, added: '2026-03-27' },
    { id: 'cisco-cbs350-24', name: 'Cisco CBS350 Managed Switch', brand: 'Cisco', path: 'networking/routers-switches', price: 96000, specs: [{ label: 'Ports', value: '24 x Gigabit' }, { label: 'Type', value: 'Managed L3' }, { label: 'Mount', value: 'Rack 1U' }], img: 'server', rating: 4.8, reviews: 15, badge: 'corporate', sold: 30, added: '2026-07-02' },
    { id: 'ubiquiti-unifi-u6', name: 'Ubiquiti UniFi U6 Pro Access Point', brand: 'Ubiquiti', path: 'networking/wireless', price: 28000, specs: [{ label: 'Standard', value: 'Wi-Fi 6' }, { label: 'Speed', value: 'Up to 5.3Gbps' }, { label: 'Power', value: 'PoE' }], img: 'server', rating: 4.7, reviews: 42, sold: 120, added: '2026-08-17' },
    // Accessories
    { id: 'logitech-mx-master-3s', name: 'Logitech MX Master 3S Mouse', brand: 'Logitech', path: 'accessories/keyboards-mice', price: 13500, specs: [{ label: 'Type', value: 'Wireless' }, { label: 'DPI', value: '8000' }, { label: 'Battery', value: '70 days' }], img: 'accessory', rating: 4.9, reviews: 210, badge: 'bestseller', best: true, sold: 520, added: '2026-03-10' },
    { id: 'logitech-mk540', name: 'Logitech MK540 Keyboard & Mouse', brand: 'Logitech', path: 'accessories/keyboards-mice', price: 6800, old: 7900, specs: [{ label: 'Type', value: 'Wireless combo' }, { label: 'Layout', value: 'Full size' }], img: 'accessory', rating: 4.5, reviews: 134, badge: 'sale', sold: 380, added: '2026-01-18' },
    { id: 'logitech-k380', name: 'Logitech K380 Bluetooth Keyboard', brand: 'Logitech', path: 'accessories/keyboards-mice', price: 4500, specs: [{ label: 'Type', value: 'Bluetooth' }, { label: 'Devices', value: '3 paired' }], img: 'accessory', rating: 4.6, reviews: 98, sold: 290, added: '2026-02-24' },
    { id: 'jabra-evolve2-65', name: 'Jabra Evolve2 65 Headset', brand: 'Jabra', path: 'accessories/webcams-headsets', price: 32000, specs: [{ label: 'Type', value: 'Wireless, on-ear' }, { label: 'Mic', value: 'Noise cancelling' }, { label: 'Battery', value: '37 hours' }], img: 'accessory', rating: 4.7, reviews: 37, badge: 'corporate', sold: 66, added: '2026-06-06' },
    { id: 'logitech-c920', name: 'Logitech C920 HD Pro Webcam', brand: 'Logitech', path: 'accessories/webcams-headsets', price: 9800, specs: [{ label: 'Video', value: '1080p 30fps' }, { label: 'Mic', value: 'Stereo' }], img: 'accessory', rating: 4.7, reviews: 156, sold: 340, added: '2026-02-08' },
    { id: 'jbl-quantum-100', name: 'JBL Quantum 100 Gaming Headset', brand: 'JBL', path: 'accessories/webcams-headsets', price: 5500, specs: [{ label: 'Type', value: 'Wired, over-ear' }, { label: 'Mic', value: 'Detachable' }], img: 'accessory', rating: 4.3, reviews: 61, stock: 0, sold: 140, added: '2026-04-02' },
    { id: 'targus-backpack-15', name: 'Targus 15.6 inch Laptop Backpack', brand: 'Targus', path: 'accessories/bags-ups', price: 4200, specs: [{ label: 'Fits', value: 'Up to 15.6 inch' }, { label: 'Feature', value: 'Water resistant' }], img: 'accessory', rating: 4.4, reviews: 73, sold: 250, added: '2026-01-30' },
    { id: 'apc-back-ups-650', name: 'APC Back-UPS 650VA', brand: 'APC', path: 'accessories/bags-ups', price: 9500, old: 11000, specs: [{ label: 'Capacity', value: '650VA / 400W' }, { label: 'Outlets', value: '4' }], img: 'accessory', rating: 4.5, reviews: 88, badge: 'sale', sold: 310, added: '2026-03-15' },
];

function toProduct(seed: Seed): ShopProduct {
    const categoryPath = seed.path.split('/');
    const attributes: Record<string, string> = {};
    if (seed.cpu) attributes.processor = seed.cpu;
    if (seed.ram) attributes.ram = seed.ram;
    if (seed.storage) attributes.storage = seed.storage;

    const derivedSpecs: ProductSpec[] = [];
    if (seed.cpu) derivedSpecs.push({ label: 'Processor', value: seed.cpu });
    if (seed.ram) derivedSpecs.push({ label: 'Memory', value: `${seed.ram} RAM` });
    if (seed.storage) derivedSpecs.push({ label: 'Storage', value: seed.storage });

    return {
        id: seed.id,
        slug: seed.id,
        name: seed.name,
        brand: seed.brand,
        category: ROOT_LABEL[categoryPath[0]] ?? 'Other',
        image: IMG[seed.img],
        price: seed.price,
        oldPrice: seed.old,
        currency: 'KES',
        specs: seed.specs ?? derivedSpecs,
        rating: seed.rating,
        reviewCount: seed.reviews,
        inStock: seed.stock !== 0,
        stockCount: seed.stock !== undefined && seed.stock > 0 ? seed.stock : undefined,
        badge: seed.badge ? BADGES[seed.badge] : undefined,
        bestSeller: seed.best,
        categoryPath,
        attributes: Object.keys(attributes).length ? attributes : undefined,
        addedAt: seed.added,
        sold: seed.sold,
    };
}

export const shopProducts: ShopProduct[] = seeds.map(toProduct);
