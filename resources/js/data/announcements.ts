import type { Announcement } from '@/types/navigation';

/**
 * Messages shown in the AnnouncementBar. Add/remove freely — the component
 * automatically decides between a centered static message (one short item)
 * and a continuous marquee (multiple items, or any long message).
 */
export const announcements: Announcement[] = [
    { id: 'vat', message: 'Prices shown are VAT exclusive.' },
    {
        id: 'stock',
        message: 'New stock has arrived. Contact us for bulk and enterprise pricing.',
        href: '/contact',
    },
    { id: 'delivery', message: 'Free delivery within Nairobi on all orders above KES 10,000.' },
];
