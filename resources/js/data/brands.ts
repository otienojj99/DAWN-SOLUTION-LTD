// import type { Brand } from '@/types/brand';

// export const brandSection = {
//     eyebrow: 'Authorized Reseller',
//     title: 'Trusted Global Brands',
//     description: "We stock genuine products from the world's leading technology manufacturers",
//     allBrandsLabel: 'Browse all brands',
//     allBrandsHref: '/brands',
// };

// /**
//  * Where brand logo files live (served from `public/`). To add a logo, save
//  * it as `public/images/brands/<id>.<ext>` using the id from the list below
//  * (for example `dell.png`, `tp-link.png`). Change the extension here if your
//  * files are SVG/WebP.
//  */
// const BRAND_LOGO_DIR = '/images/brands';
// const BRAND_LOGO_EXT = 'png';

// function brand(name: string, id: string = name.toLowerCase().replace(/[^a-z0-9]+/g, '-')): Brand {
//     return {
//         id,
//         name,
//         logo: `${BRAND_LOGO_DIR}/${id}.${BRAND_LOGO_EXT}`,
//         href: `/shop/brand/${id}`,
//     };
// }

// export const brands: Brand[] = [
//     brand('Apple'),
//     brand('ASUS'),
//     brand('Dell'),
//     brand('Lenovo'),
//     brand('HP'),
//     brand('Logitech'),
//     brand('D-Link'),
//     brand('Starlink'),
//     brand('Epson'),
//     brand('Zebra'),
//     brand('Jabra'),
//     brand('JBL'),
//     brand('Plantronics'),
//     brand('Hikvision'),
//     brand('SanDisk'),
//     brand('MSI'),
//     brand('Huawei'),
//     brand('Cisco'),
//     brand('Transcend'),
//     brand('Microsoft'),
//     brand('Kaspersky'),
//     brand('Ubiquiti'),
//     brand('UGREEN'),
//     brand('LiteWave'),
//     brand('Lexar'),
//     brand('APC'),
//     brand('Honeywell'),
//     brand('Bravo'),
//     brand('Extell'),
//     brand('Delta'),
//     brand('TP-Link'),
//     brand('Canon'),
//     brand('Kingston'),
// ];
