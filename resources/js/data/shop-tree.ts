import type { CategoryNode } from '@/types/shop';

/**
 * Shop category tree. URLs match the header dropdown links
 * (/shop/laptops/dell, /shop/desktops/hp ...).
 *
 * Two shapes exist, and the sidebar treats them differently:
 *  - three levels (Laptops → Dell → Latitude Series): the root and brand
 *    live in the header menu, so the sidebar lists only the series.
 *  - two levels (Accessories → Keyboards & Mice): the sidebar shows the
 *    parent as a dropdown with its children inside.
 */
export const shopTree: CategoryNode[] = [
    {
        slug: 'laptops',
        label: 'Laptops',
        heading: 'Laptops',
        description: 'Business, student and gaming laptops from the brands you trust, all genuine with warranty.',
        children: [
            {
                slug: 'dell',
                label: 'Dell Laptops',
                short: 'Dell',
                heading: 'Dell Laptops',
                description: 'Dependable Dell laptops for business, study and creative work.',
                children: [
                    { slug: 'latitude', label: 'Latitude Series', heading: 'Dell Latitude Series', description: 'Durable, secure business laptops built for corporate fleets.' },
                    { slug: 'xps', label: 'XPS Series', heading: 'Dell XPS Series', description: 'Premium, slim laptops for professionals and creators.' },
                    { slug: 'vostro', label: 'Vostro Series', heading: 'Dell Vostro Series', description: 'Affordable, reliable laptops for small businesses.' },
                    { slug: 'inspiron', label: 'Inspiron Series', heading: 'Dell Inspiron Series', description: 'Everyday laptops for home, school and light office work.' },
                    { slug: 'chromebook', label: 'Chromebook Series', heading: 'Dell Chromebook Series', description: 'Simple, fast and secure laptops for schools and cloud-first teams.' },
                ],
            },
            {
                slug: 'lenovo',
                label: 'Lenovo Laptops',
                short: 'Lenovo',
                heading: 'Lenovo Laptops',
                description: 'ThinkPad toughness, IdeaPad value and Legion gaming power.',
                children: [
                    { slug: 'thinkpad', label: 'ThinkPad Series', heading: 'Lenovo ThinkPad Series', description: 'The business laptop standard: legendary keyboards and security.' },
                    { slug: 'ideapad', label: 'IdeaPad Series', heading: 'Lenovo IdeaPad Series', description: 'Good-value laptops for work, study and entertainment.' },
                    { slug: 'legion', label: 'Legion Series', heading: 'Lenovo Legion Series', description: 'High-performance gaming laptops with serious cooling.' },
                ],
            },
            {
                slug: 'hp',
                label: 'HP Laptops',
                short: 'HP',
                heading: 'HP Laptops',
                description: 'HP laptops for the office, the classroom and play.',
                children: [
                    { slug: 'elitebook', label: 'EliteBook Series', heading: 'HP EliteBook Series', description: 'Premium business laptops with enterprise-grade security.' },
                    { slug: 'probook', label: 'ProBook Series', heading: 'HP ProBook Series', description: 'Solid everyday business laptops at a fair price.' },
                    { slug: 'pavilion', label: 'Pavilion Series', heading: 'HP Pavilion Series', description: 'Stylish all-rounders for home and study.' },
                ],
            },
            {
                slug: 'asus',
                label: 'ASUS Laptops',
                short: 'ASUS',
                heading: 'ASUS Laptops',
                description: 'Thin, light and powerful ASUS laptops.',
                children: [
                    { slug: 'vivobook', label: 'Vivobook Series', heading: 'ASUS Vivobook Series', description: 'Colourful, capable laptops for everyday computing.' },
                    { slug: 'zenbook', label: 'Zenbook Series', heading: 'ASUS Zenbook Series', description: 'Ultraportable premium laptops with OLED displays.' },
                    { slug: 'rog', label: 'ROG Series', heading: 'ASUS ROG Series', description: 'Republic of Gamers: top-end gaming laptops.' },
                ],
            },
        ],
    },
    {
        slug: 'desktops',
        label: 'Desktops',
        heading: 'Desktops',
        description: 'Office desktops, all-in-ones and custom-built performance PCs.',
        children: [
            { slug: 'dell', label: 'Dell Desktops', heading: 'Dell Desktops' },
            { slug: 'hp', label: 'HP Desktops', heading: 'HP Desktops' },
            { slug: 'custom', label: 'Custom Built PCs', heading: 'Custom Built PCs', description: 'Built, tested and warrantied by our own technicians.' },
            { slug: 'aio', label: 'All-in-One PCs', heading: 'All-in-One PCs' },
        ],
    },
    {
        slug: 'monitors',
        label: 'Monitors',
        heading: 'Monitors',
        description: 'Office, design and gaming displays.',
        children: [
            { slug: 'business', label: 'Business Monitors', heading: 'Business Monitors' },
            { slug: 'gaming', label: 'Gaming Monitors', heading: 'Gaming Monitors' },
        ],
    },
    {
        slug: 'servers',
        label: 'Servers',
        heading: 'Servers',
        description: 'Tower and rack servers, plus the power protection to keep them running.',
        children: [
            { slug: 'dell-poweredge', label: 'Dell PowerEdge', heading: 'Dell PowerEdge Servers' },
            { slug: 'hp-proliant', label: 'HP ProLiant', heading: 'HP ProLiant Servers' },
            { slug: 'accessories', label: 'Server Accessories & UPS', heading: 'Server Accessories & UPS' },
        ],
    },
    {
        slug: 'networking',
        label: 'Networking',
        heading: 'Networking',
        description: 'Switches, routers and wireless access points for offices and campuses.',
        children: [
            { slug: 'routers-switches', label: 'Routers & Switches', heading: 'Routers & Switches' },
            { slug: 'wireless', label: 'Wireless Access Points', heading: 'Wireless Access Points' },
        ],
    },
    {
        slug: 'accessories',
        label: 'Accessories',
        heading: 'Accessories',
        description: 'Keyboards, mice, headsets, webcams, bags and everything that completes the desk.',
        children: [
            { slug: 'keyboards-mice', label: 'Keyboards & Mice', heading: 'Keyboards & Mice' },
            { slug: 'webcams-headsets', label: 'Webcams & Headsets', heading: 'Webcams & Headsets' },
            { slug: 'bags-ups', label: 'Bags & UPS', heading: 'Bags & Power Backup' },
        ],
    },
];
