import { Gamepad2, Keyboard, Laptop, Monitor, Printer, Server } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

export interface QuickCategory {
    id: string;
    label: string;
    href: string;
    icon: LucideIcon;
}

/** The icon-strip row directly beneath the hero carousel. */
export const quickCategories: QuickCategory[] = [
    { id: 'laptops', label: 'Laptops', href: '/shop/laptops', icon: Laptop },
    { id: 'desktops', label: 'Desktops', href: '/shop/desktops', icon: Monitor },
    { id: 'gaming', label: 'Gaming', href: '/shop/laptops/gaming', icon: Gamepad2 },
    { id: 'servers', label: 'Servers', href: '/shop/servers-networking', icon: Server },
    { id: 'accessories', label: 'Accessories', href: '/shop/accessories', icon: Keyboard },
    { id: 'printers', label: 'Printers', href: '/shop/printers', icon: Printer },
];
