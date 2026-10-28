import type { ContactInfo } from '@/types/footer';

/**
 * Single source of truth for how to reach the company. The footer and the
 * quote form's "call us directly" link both read from here, so a number
 * only ever needs changing in one place.
 */
export const contactInfo: ContactInfo = {
    emails: [
        { label: 'General', address: 'info@dawnsolutionsltd.com' },
        { label: 'Support', address: 'support@dawnsolution.com' },
        { label: 'Sales', address: 'sales@dawnsolution.com' },
    ],
    phones: [
        { display: '+254 781 001144', dial: '+254781001144' },
        { display: '+254 780 001140', dial: '+254780001140' },
    ],
    whatsapp: {
        number: '254781001144',
        message: 'Hello Dawn Solution, I would like some help with a product.',
    },
};

/** Builds the wa.me chat link, with the first message pre-filled. */
export function whatsappHref(info: ContactInfo['whatsapp'] = contactInfo.whatsapp): string {
    return `https://wa.me/${info.number}?text=${encodeURIComponent(info.message)}`;
}
