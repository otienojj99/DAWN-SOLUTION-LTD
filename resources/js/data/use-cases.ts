import { Building2, Briefcase, Gamepad2, GraduationCap } from 'lucide-react';
import type { UseCase } from '@/types/collection';

export const useCaseSection = {
    eyebrow: 'Curated Collections',
    title: 'Shop by Use Case',
    description: "Whatever you're building, running, or playing — we've put together the right gear for it",
};

/**
 * Demo use-case banners. These match the audience links in the utility bar
 * (For Business / Enterprise / Gaming / Education), so the homepage and the
 * header point at the same collections. Add a "Lifestyle" entry here to
 * match the fifth utility link; the grid wraps automatically.
 */
export const useCases: UseCase[] = [
    {
        id: 'business',
        title: 'For Business',
        description: 'Reliable laptops, monitors & accessories to keep your team productive',
        ctaLabel: 'Shop Business',
        href: '/business',
        image: 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=700&q=80',
        imageAlt: 'Business professionals working together',
        icon: Briefcase,
        itemCount: 180,
    },
    {
        id: 'enterprise',
        title: 'For Enterprise',
        description: 'Servers, networking & infrastructure built to scale with you',
        ctaLabel: 'Shop Enterprise',
        href: '/enterprise',
        image: 'https://images.unsplash.com/photo-1591405351990-4726e331f141?w=700&q=80',
        imageAlt: 'Enterprise server room',
        icon: Building2,
        itemCount: 90,
    },
    {
        id: 'gaming',
        title: 'For Gaming',
        description: 'High-refresh displays, RTX power & gear built to dominate',
        ctaLabel: 'Shop Gaming',
        href: '/gaming',
        image: 'https://images.unsplash.com/photo-1542751371-6533d0f7a5a4?w=700&q=80',
        imageAlt: 'Gaming setup with RGB lighting',
        icon: Gamepad2,
        itemCount: 150,
    },
    {
        id: 'education',
        title: 'For Education',
        description: 'Affordable, durable laptops built for students & educators',
        ctaLabel: 'Shop Education',
        href: '/education',
        image: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=700&q=80',
        imageAlt: 'Students studying with laptops',
        icon: GraduationCap,
        itemCount: 70,
    },
];
