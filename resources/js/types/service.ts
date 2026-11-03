import type { LucideIcon } from 'lucide-react';

export interface Service {
    id: string;
    title: string;
    description: string;
    icon: LucideIcon;
    /** Two or three short points shown as chips. */
    highlights: string[];
    href: string;
}
