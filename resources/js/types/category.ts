/**
 * One circular tile in the "Shop by Category" carousel. Maps to a
 * top-level (Level 1) or notable Level 2 category from the `categories`
 * table, so when categories come from the database this shape is a
 * straightforward projection of a row (name → label, slug → href,
 * image → image) plus a product count.
 */
export interface ShopCategory {
    id: string;
    label: string;
    href: string;
    image: string | null;    // ← was `string`
    imageAlt?: string | null;
    productCount?: number;
    badge?: string | null;
}


export interface Category {}
