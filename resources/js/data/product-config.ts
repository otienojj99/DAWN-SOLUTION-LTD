import { BadgeCheck, RotateCcw, ShieldCheck, Truck } from 'lucide-react';
import type { ComponentType } from 'react';
import type { InfoSection } from '@/types/product-detail';

/** Settings shared by every product page. Per-product content lives in data/product-details.ts. */

export const productTrust: { id: string; label: string; icon: ComponentType<{ className?: string }> }[] = [
    { id: 'warranty', label: '1 Year Warranty', icon: ShieldCheck },
    { id: 'genuine', label: 'Genuine Product', icon: BadgeCheck },
    { id: 'delivery', label: 'Free Delivery (Nairobi)', icon: Truck },
    { id: 'returns', label: '7-Day Returns', icon: RotateCcw },
];

export const productCopy = {
    vatNote: 'Price inclusive of VAT · Free delivery within Nairobi',
    paymentNote: 'Pay securely via M-Pesa at checkout. Lipa na M-Pesa Till Number provided after order confirmation.',
    paymentMethod: 'M-PESA',
    shipsIn: 'Ships in 1–2 days',
};

/** Fallback used when a product has no `shippingInfo` of its own. */
export const defaultShippingInfo: InfoSection[] = [
    {
        title: 'Delivery',
        body: 'Free delivery within Nairobi (1–3 business days). Nationwide delivery is available at checkout, typically 2–4 business days depending on location.',
    },
    {
        title: 'Warranty & Returns',
        body: 'Comes with a 1-year manufacturer warranty covering hardware defects. We also offer a 7-day return window for unopened or defective units. Reach out to our support team to start a return.',
    },
];

/** Used by the "You May Also Like" grid when a product lists no `relatedIds`: popular accessories that suit most purchases. */
export const defaultRelatedIds = ['logitech-mx-master-3s', 'logitech-mk540', 'targus-backpack-15', 'apc-back-ups-650'];

/** Copy for the WhatsApp chat window. The phone number comes from data/contact.ts. */
export const whatsappChat = {
    agentName: 'Dawn Solution',
    agentRole: 'Sales & Support',
    replyTime: 'Typically replies within minutes',
    greeting: 'Hello! How can we help you today? Ask about stock, prices, bulk orders or delivery.',
    placeholder: 'Type your message...',
    startLabel: 'Start chat',
    openLabel: 'Chat with us on WhatsApp',
};
