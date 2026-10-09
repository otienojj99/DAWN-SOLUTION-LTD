// import type { Category } from '@/types/navigation';

// /**
//  * Main category navigation. Categories with `columns` render a mega menu
//  * on hover/focus; categories without `columns` render as a plain link.
//  *
//  * This is intentionally data-only — once product categories live in the
//  * database, this array can be replaced by a fetch/Inertia shared-prop
//  * without touching CategoryNavigation.tsx or CategoryMenu.tsx at all.
//  */
// export const categories: Category[] = [
//     {
//         id: 'all',
//         label: 'All Categories',
//         href: '/shop',
//         isPrimary: true,
//         columns: [
//             {
//                 heading: 'Laptops',
//                 links: [
//                     { label: 'Lenovo Laptops', href: '/shop/laptops/lenovo' },
//                     { label: 'Dell Laptops', href: '/shop/laptops/dell' },
//                     { label: 'HP Laptops', href: '/shop/laptops/hp' },
//                     { label: 'ASUS / Apple', href: '/shop/laptops/asus-apple' },
//                     { label: 'Gaming Laptops', href: '/shop/laptops/gaming' },
//                 ],
//             },
//             {
//                 heading: 'Desktops',
//                 links: [
//                     { label: 'Dell Desktops', href: '/shop/desktops/dell' },
//                     { label: 'HP Desktops', href: '/shop/desktops/hp' },
//                     { label: 'Custom Built PCs', href: '/shop/desktops/custom' },
//                     { label: 'All-in-One PCs', href: '/shop/desktops/aio' },
//                 ],
//             },
//             {
//                 heading: 'Components',
//                 links: [
//                     { label: 'CPUs & Motherboards', href: '/shop/components/cpu-motherboard' },
//                     { label: 'RAM & Storage', href: '/shop/components/ram-storage' },
//                     { label: 'Graphics Cards', href: '/shop/components/gpu' },
//                     { label: 'Cases & Cooling', href: '/shop/components/cases-cooling' },
//                 ],
//             },
//             {
//                 heading: 'Servers & Networking',
//                 links: [
//                     { label: 'Dell PowerEdge', href: '/shop/servers/dell-poweredge' },
//                     { label: 'HP ProLiant', href: '/shop/servers/hp-proliant' },
//                     { label: 'Routers & Switches', href: '/shop/networking/routers-switches' },
//                     { label: 'Server Accessories', href: '/shop/servers/accessories' },
//                 ],
//             },
//             {
//                 heading: 'Accessories',
//                 links: [
//                     { label: 'Keyboards & Mice', href: '/shop/accessories/keyboards-mice' },
//                     { label: 'Webcams & Headsets', href: '/shop/accessories/webcams-headsets' },
//                     { label: 'Shop Logitech', href: '/shop/brand/logitech', badge: 'Brand' },
//                     { label: 'Bags & UPS', href: '/shop/accessories/bags-ups' },
//                 ],
//             },
//         ],
//     },
//     {
//         id: 'laptops',
//         label: 'Laptops',
//         href: '/shop/laptops',
//         columns: [
//             {
//                 heading: 'Shop by Brand',
//                 links: [
//                     { label: 'Lenovo Laptops', href: '/shop/laptops/lenovo' },
//                     { label: 'Dell Laptops', href: '/shop/laptops/dell' },
//                     { label: 'HP Laptops', href: '/shop/laptops/hp' },
//                     { label: 'ASUS Laptops', href: '/shop/laptops/asus' },
//                     { label: 'Apple MacBooks', href: '/shop/laptops/apple' },
//                 ],
//             },
//             {
//                 heading: 'Shop by Use',
//                 links: [
//                     { label: 'Business Laptops', href: '/shop/laptops/business' },
//                     { label: 'Gaming Laptops', href: '/shop/laptops/gaming' },
//                     { label: 'Student / Budget', href: '/shop/laptops/budget' },
//                     { label: '2-in-1 & Touchscreen', href: '/shop/laptops/2-in-1' },
//                 ],
//             },
//         ],
//     },
//     {
//         id: 'desktops',
//         label: 'Desktops',
//         href: '/shop/desktops',
//         columns: [
//             {
//                 heading: 'Shop by Brand',
//                 links: [
//                     { label: 'Dell Desktops', href: '/shop/desktops/dell' },
//                     { label: 'HP Desktops', href: '/shop/desktops/hp' },
//                     { label: 'Lenovo Desktops', href: '/shop/desktops/lenovo' },
//                     { label: 'Custom Built PCs', href: '/shop/desktops/custom' },
//                 ],
//             },
//             {
//                 heading: 'Shop by Use',
//                 links: [
//                     { label: 'Gaming PCs', href: '/shop/desktops/gaming' },
//                     { label: 'Office / Business PCs', href: '/shop/desktops/office' },
//                     { label: 'All-in-One PCs', href: '/shop/desktops/aio' },
//                 ],
//             },
//         ],
//     },
//     {
//         id: 'components',
//         label: 'Components',
//         href: '/shop/components',
//         columns: [
//             {
//                 heading: 'Core Parts',
//                 links: [
//                     { label: 'Processors (CPUs)', href: '/shop/components/cpu' },
//                     { label: 'Motherboards', href: '/shop/components/motherboards' },
//                     { label: 'RAM / Memory', href: '/shop/components/ram' },
//                     { label: 'Graphics Cards', href: '/shop/components/gpu' },
//                 ],
//             },
//             {
//                 heading: 'Storage & Power',
//                 links: [
//                     { label: 'SSDs', href: '/shop/components/ssd' },
//                     { label: 'Hard Drives (HDD)', href: '/shop/components/hdd' },
//                     { label: 'Power Supplies', href: '/shop/components/psu' },
//                     { label: 'PC Cases & Cooling', href: '/shop/components/cases-cooling' },
//                 ],
//             },
//         ],
//     },
//     {
//         id: 'servers-networking',
//         label: 'Servers & Networking',
//         href: '/shop/servers-networking',
//         columns: [
//             {
//                 heading: 'Servers',
//                 links: [
//                     { label: 'Dell PowerEdge', href: '/shop/servers/dell-poweredge' },
//                     { label: 'HP ProLiant', href: '/shop/servers/hp-proliant' },
//                     { label: 'Lenovo ThinkSystem', href: '/shop/servers/lenovo-thinksystem' },
//                     { label: 'Server Accessories', href: '/shop/servers/accessories' },
//                 ],
//             },
//             {
//                 heading: 'Networking',
//                 links: [
//                     { label: 'Routers', href: '/shop/networking/routers' },
//                     { label: 'Switches', href: '/shop/networking/switches' },
//                     { label: 'Firewalls', href: '/shop/networking/firewalls' },
//                     { label: 'Access Points', href: '/shop/networking/access-points' },
//                 ],
//             },
//         ],
//     },
//     {
//         id: 'accessories',
//         label: 'Accessories',
//         href: '/shop/accessories',
//         columns: [
//             {
//                 heading: 'Input & Audio',
//                 links: [
//                     { label: 'Keyboards & Mice', href: '/shop/accessories/keyboards-mice' },
//                     { label: 'Webcams', href: '/shop/accessories/webcams' },
//                     { label: 'Headsets & Speakers', href: '/shop/accessories/headsets-speakers' },
//                 ],
//             },
//             {
//                 heading: 'Power & Carry',
//                 links: [
//                     { label: 'Docking Stations & Hubs', href: '/shop/accessories/docking-hubs' },
//                     { label: 'Laptop Bags & Sleeves', href: '/shop/accessories/bags' },
//                     { label: 'UPS & Power Backup', href: '/shop/accessories/ups' },
//                 ],
//             },
//         ],
//     },
//     {
//         id: 'printers',
//         label: 'Printers',
//         href: '/shop/printers',
//     },
//     {
//         id: 'software',
//         label: 'Software',
//         href: '/shop/software',
//     },
//     {
//         id: 'deals',
//         label: 'Deals',
//         href: '/deals',
//     },
// ];
