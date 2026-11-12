export type ShopBannerKind = 'bundle' | 'discount' | 'promotion' | 'seasonal';

export interface ShopBanner {
    id: string;
    kind: ShopBannerKind;
    tag: string;
    /** Lucide icon key — resolved on the frontend. */
    icon: string | null;
    title: string;
    description: string | null;
    highlight: string | null;
    ctaLabel: string;
    href: string;
    image: string | null;
    imageAlt: string | null;
    /** Preset key (blue-deep, purple, green, orange) — resolved to Tailwind classes. */
    gradientKey: string;
}