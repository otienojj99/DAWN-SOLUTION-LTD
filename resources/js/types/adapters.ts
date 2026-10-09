import type { Category as NavCategory } from '@/types/navigation';

type CategoryProp = {
    id: number;
    name: string;
    slug: string;
    href: string;
    children?: CategoryProp[];
};

export function toNavCategories(props: CategoryProp[]): NavCategory[] {
    const items = props.map((cat) => ({
        id: String(cat.id),
        label: cat.name,
        href: cat.href,
        columns: cat.children?.length
            ? [
                  {
                      heading: cat.name,
                      links: cat.children.map((c) => ({
                          label: c.name,
                          href: c.href,
                      })),
                  },
              ]
            : undefined,
    }));

    return [
        {
            id: 'all',
            label: 'All Categories',
            href: '/shop/categories',
            isPrimary: true,
        },
        ...items,
    ]
}