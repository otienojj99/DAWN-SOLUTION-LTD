import { Gift, Percent, Sparkles, Package, Tag, type LucideIcon } from 'lucide-react';

const ICON_MAP: Record<string, LucideIcon> = {
    gift: Gift,
    percent: Percent,
    sparkles: Sparkles,
    package: Package,
};

export function resolveBannerIcon(key: string | null | undefined): LucideIcon {
    if (!key) return Tag;
    return ICON_MAP[key] ?? Tag;
}