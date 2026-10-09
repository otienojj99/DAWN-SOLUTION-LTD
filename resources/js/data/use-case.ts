export type UseCase = {
    id: number;
    name: string;
    slug: string;
    icon: string | null;
    color: string | null;
    href: string;
    products_count?: number;
    description?: string | null;
}