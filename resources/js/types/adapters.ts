// import type { Category as NavCategory } from '@/types/navigation';

// type CategoryProp = {
//     id: number;
//     name: string;
//     slug: string;
//     href: string;
//     children?: CategoryProp[];
// };

// const PRIORITIZED_IN_NAV: string[] = [
//     'laptops',
//     'desktops',
//     'components',
//     'servers-networking',
//     'accessories',
//     'printers',
// ];

// export function toNavCategories(props: CategoryProp[]): NavCategory[] {
//     const items = props.map((cat) => ({
//         id: String(cat.id),
//         label: cat.name,
//         href: cat.href,
//         columns: cat.children?.length
//             ? [
//                   {
//                       heading: cat.name,
//                       links: cat.children.map((c) => ({
//                           label: c.name,
//                           href: c.href,
//                       })),
//                   },
//               ]
//             : undefined,
//     }));

//     return [
//         {
//             id: 'all',
//             label: 'All Categories',
//             href: '/shop/categories',
//             isPrimary: true,
//         },
//         ...items,
//     ]
// }

import type { Category as NavCategory, CategoryColumn } from '@/types/navigation';

type CategoryProp = {
    id: number;
    name: string;
    slug: string;
    href: string;
    children?: CategoryProp[];
};

/**
 * Which parent categories appear as direct links next to "All Categories".
 * Everything else only appears inside the All Categories mega-menu.
 * Edit this list to prioritise different categories — order here is display order.
 */
const PRIORITIZED_IN_NAV: string[] = [
    'laptops',
    'desktops',
    'components',
    'servers-networking',
    'accessories',
    'printers',
];

export function toNavCategories(props: CategoryProp[]): NavCategory[] {
    // ── 1. Build the All Categories mega-menu — one column per parent ──
    const allCategoriesColumns: CategoryColumn[] = props.map((parent) => ({
        heading: parent.name,
        links: [
            // "All Laptops" shortcut at the top of each column
            { label: `All ${parent.name}`, href: `/shop/${parent.slug}` },
            // Children below it
            ...(parent.children ?? []).map((child) => ({
                label: child.name,
                href: `/shop/${parent.slug}/${child.slug}`,
            })),
        ],
    }));

    // ── 2. Direct parent links shown next to All Categories ──
    //    No `columns` property → renders as a plain link.
    const parentLinks: NavCategory[] = props
        .filter((p) => PRIORITIZED_IN_NAV.includes(p.slug))
        .map((parent) => ({
            id: String(parent.id),
            label: parent.name,
            href: `/shop/${parent.slug}`,
        }));

    return [
        {
            id: 'all',
            label: 'All Categories',
            href: '/shop',
            isPrimary: true,
            columns: allCategoriesColumns,
        },
        ...parentLinks,
    ];
}