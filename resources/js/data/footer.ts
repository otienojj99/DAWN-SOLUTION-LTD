import { BadgeCheck, Lock, ShieldCheck, Truck } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import type { FooterLink, FooterLinkGroup, SocialLink } from '@/types/footer';

export const footerAbout =
    'Your trusted technology partner for laptops, desktops, servers, networking and accessories — genuine products, competitive prices, reliable support.';

/**
 * Social profiles. The TikTok and Instagram links are the plain profile
 * URLs: the long share links they were copied from carry tracking
 * parameters (`_r`, `_t`, `igsi`) that aren't needed.
 */
export const socialLinks: SocialLink[] = [
    { platform: 'x', label: 'Follow us on X (@dawnsolutions)', href: 'https://x.com/dawnsolutions' },
    { platform: 'instagram', label: 'Follow us on Instagram', href: 'https://www.instagram.com/dawnsolutionslimited' },
    { platform: 'facebook', label: 'Follow us on Facebook', href: 'https://www.facebook.com/share/1CRAZK8Ppx/' },
    { platform: 'tiktok', label: 'Follow us on TikTok', href: 'https://www.tiktok.com/@dawnsolutionsltd' },
];

/** The link columns. Change hrefs to your real routes; add or remove links freely. */
export const footerGroups: FooterLinkGroup[] = [
    {
        id: 'quick-links',
        title: 'Quick Links',
        links: [
            { label: 'Home', href: '/' },
            { label: 'About Us', href: '/about' },
            { label: 'All Categories', href: '/shop' },
            { label: 'Deals & Clearance', href: '/deals/clearance' },
            { label: 'Request a Quotation', href: '/quote' },
            { label: 'Track Order', href: '/track-order' },
            { label: 'Blog', href: '/blog' },
        ],
    },
    {
        id: 'categories',
        title: 'Categories',
        links: [
            { label: 'Laptops', href: '/shop/laptops' },
            { label: 'Desktops', href: '/shop/desktops' },
            { label: 'Servers & Networking', href: '/shop/servers-networking' },
            { label: 'Accessories', href: '/shop/accessories' },
            { label: 'Monitors', href: '/shop/monitors' },
            { label: 'Printers', href: '/shop/printers' },
            { label: 'Software & Security', href: '/shop/software-security' },
        ],
    },
    {
        id: 'customer-service',
        title: 'Customer Service',
        links: [
            { label: 'FAQs', href: '/faqs' },
            { label: 'Terms & Conditions', href: '/terms' },
            { label: 'Privacy Policy', href: '/privacy' },
            { label: 'Returns & Refunds', href: '/returns' },
            { label: 'Warranty Policy', href: '/warranty' },
            { label: 'Shipping & Delivery', href: '/shipping' },
            { label: 'Business / Bulk Orders', href: '/business' },
        ],
    },
];

/**
 * Payment methods accepted. Only M-PESA to start; add 'Visa', 'Mastercard',
 * 'Bank Transfer' here when checkout supports them, so the footer never
 * advertises a way to pay that doesn't work.
 */
export const paymentMethods: string[] = ['M-PESA'];

export interface TrustPoint {
    id: string;
    label: string;
    icon: LucideIcon;
}

export const trustPoints: TrustPoint[] = [
    { id: 'genuine', label: 'Genuine Products', icon: BadgeCheck },
    { id: 'delivery', label: 'Nationwide Delivery', icon: Truck },
    { id: 'warranty', label: 'Warranty Included', icon: ShieldCheck },
    { id: 'checkout', label: 'Secure Checkout', icon: Lock },
];

export const legalLinks: FooterLink[] = [
    { label: 'Terms & Conditions', href: '/terms' },
    { label: 'Privacy Policy', href: '/privacy' },
    { label: 'Sitemap', href: '/sitemap' },
];

export const companyName = 'Dawn Solution Limited';
