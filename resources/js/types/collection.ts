export type UseCase = {
    id: number;
    title: string;
    description: string | null;
    ctaLabel: string;
    href: string;
    image: string | null;
    imageAlt: string | null;
    icon: string | null;      // ← string key, not LucideIcon
    color: string | null;
    itemCount?: number;
};