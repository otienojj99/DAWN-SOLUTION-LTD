import { contactInfo } from '@/data/contact';

/** wa.me link that opens a chat with the company number and the given text pre-filled. */
export function whatsappUrl(text: string, number: string = contactInfo.whatsapp.number): string {
    return `https://wa.me/${number}?text=${encodeURIComponent(text)}`;
}
