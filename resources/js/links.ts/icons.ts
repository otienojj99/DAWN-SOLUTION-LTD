import {
    Briefcase,
    Building2,
    GraduationCap,
    Gamepad2,
    Sparkles,
    Tag,
    type LucideIcon,
} from 'lucide-react';

const ICON_MAP: Record<string, LucideIcon> = {
    briefcase: Briefcase,
    building: Building2,
    'graduation-cap': GraduationCap,
    gamepad: Gamepad2,
    sparkles: Sparkles,
};

export function resolveIcon(key: string | null | undefined): LucideIcon {
    if (!key) return Tag;                // fallback
    return ICON_MAP[key] ?? Tag;
}