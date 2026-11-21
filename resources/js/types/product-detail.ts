import type { ProductSpec } from '@/types/product';
import type { ShopProduct } from '@/types/shop';

export interface GalleryImage {
    /** Large image for the main view. */
    src: string;
    /** Small image for the thumbnail strip. Falls back to `src`. */
    thumb?: string;
    alt: string;
}

export interface VariantOption {
    /** The value stored and sent to the cart, e.g. "16GB". */
    value: string;
    /** Display text when it differs from `value`. */
    label?: string;
    /** CSS colour for a colour swatch dot. */
    swatch?: string;
    /** Added to the base price when chosen (can be negative). */
    priceDelta?: number;
    /** Appended to the SKU when chosen, e.g. "16G". */
    skuPart?: string;
    /** Shown struck-through and not selectable (out of stock in this combination). */
    unavailable?: boolean;
}

/** One choice the buyer makes before ordering: RAM, storage, colour... */
export interface VariantGroup {
    id: string;
    label: string;
    options: VariantOption[];
    defaultValue: string;
}

/** Chosen value per variant group id, e.g. { ram: '16GB', storage: '512GB SSD' }. */
export type VariantSelection = Record<string, string>;

export interface ProductVideo {
    id: string;
    title: string;
    /** 'file' = an mp4/webm you upload; 'youtube' = a YouTube video id (loaded only when played). */
    kind: 'file' | 'youtube';
    /** URL of the video file, or the YouTube video id. */
    src: string;
    /** Still image shown before play. Strongly recommended for files. */
    poster?: string;
    /** WebVTT captions file for a 'file' video. */
    captionsSrc?: string;
    /** Display text such as "2:35". */
    duration?: string;
}

export interface Review {
    id: string;
    author: string;
    date: string;
    rating: number;
    text: string;
    verified?: boolean;
    adminReply?: string | null;
    adminRepliedAt?: string | null;
}
/** Number of reviews per star level, for the summary bars. */
export type RatingBreakdown = Record<1 | 2 | 3 | 4 | 5, number>;

export interface InfoSection {
    title: string;
    body: string;
}

export interface ProductDetail extends ShopProduct {
    sku: string;
    shortDescription: string;
    gallery: GalleryImage[];
    variants: VariantGroup[];
    tags: string[];
    overview: string;
    highlights: string[];
    specTable: ProductSpec[];
    shippingInfo: InfoSection[];
    videos: ProductVideo[];
    reviews: Review[];
    ratingBreakdown?: RatingBreakdown;
    /** Ids shown in the "You may also like" grid for products that don't use the carousel (accessories, add-ons). */
    relatedIds?: string[];
}

/** What the visitor chose, handed to the cart / checkout callbacks. */
export interface PurchaseItem {
    productId: string;
    slug: string;
    quantity: number;
    variants: VariantSelection;
    sku: string;
    unitPrice: number;
}

/** A review as submitted from the form. */
export interface NewReview {
    productId: string;
    rating: number;
    name: string;
    text: string;
}
