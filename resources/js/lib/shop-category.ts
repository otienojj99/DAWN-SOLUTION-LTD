import { SHOP_BASE_PATH } from '@/data/shop-config';
import type { CategoryNode, ShopProduct } from '@/types/shop';

/** Result of walking a URL path (['laptops','dell','latitude']) down the category tree. */
export interface ResolvedCategory {
    /** One node per path segment that exists, root first. Empty for the all-products page. */
    trail: CategoryNode[];
    /** Slugs that matched. Shorter than the requested path when part of it was unknown. */
    path: string[];
    /** The deepest selected node, or undefined on the all-products page. */
    current?: CategoryNode;
    /** True when every requested segment matched a node. Use it to show a 404 for bad URLs. */
    valid: boolean;
}

export function resolveCategory(tree: CategoryNode[], requested: string[]): ResolvedCategory {
    const trail: CategoryNode[] = [];
    let level: CategoryNode[] | undefined = tree;

    for (const slug of requested) {
        const node: CategoryNode | undefined = level?.find((n) => n.slug === slug);
        if (!node) break;
        trail.push(node);
        level = node.children;
    }

    return {
        trail,
        path: trail.map((n) => n.slug),
        current: trail[trail.length - 1],
        valid: trail.length === requested.length,
    };
}

/** True when the product sits in (or below) the category at `path`. An empty path matches everything. */
export function inCategory(product: Pick<ShopProduct, 'categoryPath'>, path: string[]): boolean {
    return path.every((slug, i) => product.categoryPath[i] === slug);
}

export function categoryHref(path: string[]): string {
    return path.length ? `${SHOP_BASE_PATH}/${path.join('/')}` : SHOP_BASE_PATH;
}

/** A root counts as "deep" when its children have children of their own (Laptops → Dell → Latitude). */
export function isDeepRoot(node: CategoryNode): boolean {
    return !!node.children?.some((child) => child.children?.length);
}

export interface Crumb {
    label: string;
    href: string;
}

/** Home / Laptops / Dell Laptops / Latitude Series. The last crumb is the current page. */
export function breadcrumbs(trail: CategoryNode[]): Crumb[] {
    return [
        { label: 'Home', href: '/' },
        { label: 'Shop', href: SHOP_BASE_PATH },
        ...trail.map((node, i) => ({ label: node.label, href: categoryHref(trail.slice(0, i + 1).map((n) => n.slug)) })),
    ];
}

/* ------------------------------------------------------------------ */
/* Sidebar model                                                       */
/* ------------------------------------------------------------------ */

export interface SidebarLink {
    label: string;
    href: string;
    path: string[];
    count: number;
    active: boolean;
}

export interface SidebarGroup {
    /** Small heading above the links, e.g. the brand ("Dell") when several brands' series are listed together. */
    heading?: string;
    links: SidebarLink[];
}

export interface SidebarSection {
    id: string;
    title: string;
    /** Collapsible sections render as a dropdown; others are always open. */
    collapsible: boolean;
    open: boolean;
    /** "All Dell Laptops" style link at the top of the list. */
    viewAll?: SidebarLink;
    groups: SidebarGroup[];
}

function countIn(products: ShopProduct[], path: string[]): number {
    return products.filter((p) => inCategory(p, path)).length;
}

function link(node: CategoryNode, path: string[], current: string[], products: ShopProduct[], label = node.label): SidebarLink {
    return {
        label,
        href: categoryHref(path),
        path,
        count: countIn(products, path),
        active: path.length === current.length && path.every((s, i) => s === current[i]),
    };
}

/**
 * Builds what the category sidebar shows.
 *
 *  - Three-level categories (Laptops → Dell → Latitude Series): the root and
 *    brand live in the header menu, so the sidebar lists ONLY the series.
 *    With a brand selected that is just its series; at the Laptops root the
 *    series of every brand, grouped under brand headings.
 *  - Two-level categories (Desktops → Dell Desktops): each root is a dropdown
 *    holding its children. The root you are in is open.
 *  - On the all-products page every root is a dropdown (three-level roots
 *    contain their series grouped by brand).
 */
// export function buildSidebar(tree: CategoryNode[], currentPath: string[], products: ShopProduct[]): SidebarSection[] {
//     const root = tree.find((n) => n.slug === currentPath[0]);

//     // Inside a three-level root: series only.
//     if (root && isDeepRoot(root)) {
//         const brand = root.children?.find((c) => c.slug === currentPath[1]);

//         if (brand?.children) {
//             return [
//                 {
//                     id: `${root.slug}-${brand.slug}`,
//                     title: `${brand.short ?? brand.label} series`,
//                     collapsible: false,
//                     open: true,
//                     viewAll: link(brand, [root.slug, brand.slug], currentPath, products, `All ${brand.label}`),
//                     groups: [{ links: brand.children.map((s) => link(s, [root.slug, brand.slug, s.slug], currentPath, products)) }],
//                 },
//             ];
//         }

//         return [
//             {
//                 id: `${root.slug}-series`,
//                 title: `${root.label} series`,
//                 collapsible: false,
//                 open: true,
//                 viewAll: link(root, [root.slug], currentPath, products, `All ${root.label}`),
//                 groups: (root.children ?? [])
//                     .filter((b) => b.children?.length)
//                     .map((b) => ({
//                         heading: b.short ?? b.label,
//                         links: b.children!.map((s) => link(s, [root.slug, b.slug, s.slug], currentPath, products)),
//                     })),
//             },
//         ];
//     }

//     // Everywhere else: one dropdown per root (the active one open).
//     return tree
//         .filter((n) => n.children?.length)
//         .map((node) => {
//             const deep = isDeepRoot(node);
//             return {
//                 id: node.slug,
//                 title: node.label,
//                 collapsible: true,
//                 open: node.slug === currentPath[0],
//                 viewAll: link(node, [node.slug], currentPath, products, `All ${node.label}`),
//                 groups: deep
//                     ? node.children!
//                           .filter((b) => b.children?.length)
//                           .map((b) => ({
//                               heading: b.short ?? b.label,
//                               links: b.children!.map((s) => link(s, [node.slug, b.slug, s.slug], currentPath, products)),
//                           }))
//                     : [{ links: node.children!.map((c) => link(c, [node.slug, c.slug], currentPath, products)) }],
//             };
//         });
// }

/**
 * Contextual drill-down sidebar.
 *
 * Shows ONLY the direct children of the category you are currently in:
 *   /shop                                          → top-level categories
 *   /shop/laptops                                  → its children (brands)
 *   /shop/laptops/lenovo-laptops                   → its children (series)
 *   /shop/laptops/lenovo-laptops/lenovo-ideapads   → nothing (leaf)
 *
 * Upward navigation is the breadcrumb's job.
 */
export function buildSidebar(
    tree: CategoryNode[],
    currentPath: string[],
    products: ShopProduct[],
): SidebarSection[] {
    // Walk the tree following currentPath, stopping at the deepest matching node.
    const trail: CategoryNode[] = [];
    let level: CategoryNode[] | undefined = tree;
    for (const slug of currentPath) {
        const node: CategoryNode | undefined = level?.find((n) => n.slug === slug);
        if (!node) break;
        trail.push(node);
        level = node.children;
    }

    const currentNode = trail.length > 0 ? trail[trail.length - 1] : null;

    // Root — show top-level categories.
    if (!currentNode) {
        return [
            {
                id: 'root',
                title: 'Categories',
                collapsible: false,
                open: true,
                groups: [
                    {
                        links: tree.map((node) => link(node, [node.slug], currentPath, products)),
                    },
                ],
            },
        ];
    }

    // Leaf — nothing to drill into. The filters panel takes over.
    if (!currentNode.children?.length) {
        return [];
    }

    // Interior — show children only.
    return [
        {
            id: `section-${currentNode.slug}`,
            title: currentNode.label,
            collapsible: false,
            open: true,
            groups: [
                {
                    links: currentNode.children.map((child) =>
                        link(child, [...currentPath, child.slug], currentPath, products),
                    ),
                },
            ],
        },
    ];
}
