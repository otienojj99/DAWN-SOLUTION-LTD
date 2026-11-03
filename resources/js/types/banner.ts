import type { LucideIcon } from 'lucide-react';

export type ShopBannerKind = 'bundle' | 'discount' | 'promotion' | 'seasonal';

/** One slide of the shop page promo slider (bundles, discounts, promotions, seasonal sales). */
export interface ShopBanner {
    id: string;
    kind: ShopBannerKind;
    /** Small label on the slide, e.g. "Bundle deal". */
    tag: string;
    icon: LucideIcon;
    title: string;
    description: string;
    /** Big highlight, e.g. "Save KES 6,500" or "Up to 25% off". */
    highlight: string;
    ctaLabel: string;
    href: string;
    image: string;
    imageAlt: string;
    /** Tailwind gradient classes for the slide background. */
    gradient: string;
}
