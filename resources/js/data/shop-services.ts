import { Briefcase, Cpu, MapPin, Network, Server, Wrench } from 'lucide-react';
import type { Service } from '@/types/service';

/**
 * The services highlighted on the shop page. The full list (data-centre
 * installation, WAN design, managed support...) belongs on the services
 * page; add more entries there and link to them with `href`.
 */
export const services: Service[] = [
    {
        id: 'repair',
        title: 'Computer Repair',
        description: 'Fast diagnosis and repair for laptops, desktops and printers, with genuine parts and a warranty on the work.',
        icon: Wrench,
        highlights: ['Genuine parts', 'Warranty on repairs'],
        href: '/services/computer-repair',
    },
    {
        id: 'upgrades',
        title: 'Upgrades & Optimisation',
        description: 'More memory, faster SSDs and a clean install can make an old machine feel new, at a fraction of the cost of replacing it.',
        icon: Cpu,
        highlights: ['RAM & SSD upgrades', 'Data kept safe'],
        href: '/services/upgrades',
    },
    {
        id: 'onsite',
        title: 'Onsite Repair & Support',
        description: 'Our technicians come to your office, school or site and fix machines where they stand, so your team keeps working.',
        icon: MapPin,
        highlights: ['We come to you', 'Same-day for urgent faults'],
        href: '/services/onsite-support',
    },
    {
        id: 'consulting',
        title: 'Business IT Consulting',
        description: 'Not sure which machines your team needs? Get a recommendation, an onsite demo and instalment options before you commit.',
        icon: Briefcase,
        highlights: ['Free recommendation', 'Onsite demos', 'Instalment plans'],
        href: '/services/business-consulting',
    },
    {
        id: 'servers',
        title: 'Server & Data Centre Setup',
        description: 'Server installation, racking, configuration and backup, built for uptime and sized for where your business is going.',
        icon: Server,
        highlights: ['Install & configure', 'Backup & recovery'],
        href: '/services/server-installation',
    },
    {
        id: 'networks',
        title: 'LAN & WAN Networking',
        description: 'Structured cabling, switching, Wi-Fi and site-to-site links, designed, installed and documented by certified engineers.',
        icon: Network,
        highlights: ['Office LAN', 'Branch WAN links'],
        href: '/services/networking',
    },
];

export const servicesIntro = {
    kicker: 'Beyond the box',
    title: 'IT services that keep your business running',
    description: 'Buy the machine, then let our engineers repair, upgrade, install and support it, in the workshop or at your site.',
    allServicesLabel: 'View all services',
    allServicesHref: '/services',
};

/** The "not sure what to buy?" journey shown on the consulting call-to-action. */
export const consultingSteps = ['Tell us what your team does', 'We recommend the right machines', 'See them in an onsite demo', 'Pay in instalments if you prefer'];

export const consultingCta = {
    title: 'Not sure which machines your team needs?',
    description: 'Book a free consultation. We recommend, demo onsite and arrange instalments, with no obligation to buy.',
    primaryLabel: 'Book a free consultation',
    primaryHref: '/services/business-consulting',
};
