import { defaultRelatedIds } from '@/data/product-config';
import { discountPercent } from '@/lib/format';
import type { ProductDetail, RatingBreakdown, Review, VariantGroup, VariantSelection } from '@/types/product-detail';
import type { ShopProduct } from '@/types/shop';

/** The option each group starts on. */
export function defaultSelection(variants: VariantGroup[]): VariantSelection {
    return Object.fromEntries(variants.map((group) => [group.id, group.defaultValue]));
}

/** Base price plus the price change of every chosen option. */
export function priceFor(product: ProductDetail, selection: VariantSelection): { price: number; oldPrice?: number; savePercent: number } {
    const delta = product.variants.reduce((sum, group) => {
        const option = group.options.find((o) => o.value === selection[group.id]);
        return sum + (option?.priceDelta ?? 0);
    }, 0);
    const price = product.price + delta;
    const oldPrice = product.oldPrice !== undefined ? product.oldPrice + delta : undefined;
    return { price, oldPrice, savePercent: discountPercent(price, oldPrice) ?? 0 };
}

/** Base SKU plus the part contributed by each chosen option: DEL-VOS3550-I5-16G-512-BK. */
export function skuFor(product: ProductDetail, selection: VariantSelection): string {
    const parts = product.variants.map((group) => group.options.find((o) => o.value === selection[group.id])?.skuPart).filter(Boolean);
    return [product.sku, ...parts].join('-');
}

/** "16GB RAM / 512GB SSD / Carbon Black" style text, for messages and cart lines. */
export function selectionSummary(product: ProductDetail, selection: VariantSelection): string {
    return product.variants.map((group) => selection[group.id]).filter(Boolean).join(' / ');
}

/** Largest quantity that can be ordered in one go. */
export function maxQuantity(product: Pick<ProductDetail, 'stockCount'>): number {
    return product.stockCount !== undefined && product.stockCount > 0 ? product.stockCount : 99;
}

/** Percentage of reviews at each star level, for the summary bars. */
export function breakdownBars(breakdown: RatingBreakdown): { stars: 1 | 2 | 3 | 4 | 5; count: number; percent: number }[] {
    const total = Object.values(breakdown).reduce((a, b) => a + b, 0);
    return ([5, 4, 3, 2, 1] as const).map((stars) => ({
        stars,
        count: breakdown[stars],
        percent: total ? Math.round((breakdown[stars] / total) * 100) : 0,
    }));
}

/** Breakdown worked out from the reviews themselves, used when none is supplied. */
export function breakdownFromReviews(reviews: Review[]): RatingBreakdown {
    const result: RatingBreakdown = { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
    reviews.forEach((r) => {
        const stars = Math.min(5, Math.max(1, Math.round(r.rating))) as 1 | 2 | 3 | 4 | 5;
        result[stars] += 1;
    });
    return result;
}

/** Fixed-format date ("27 Oct 2026") so server-rendered and browser output are identical. */
export function formatReviewDate(iso: string): string {
    return new Date(`${iso}T00:00:00Z`).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', timeZone: 'UTC' });
}

export function initials(name: string): string {
    return (
        name
            .split(/\s+/)
            .filter(Boolean)
            .slice(0, 2)
            .map((part) => part[0]?.toUpperCase() ?? '')
            .join('') || '?'
    );
}

/* ------------------------------------------------------------------ */
/* Related products                                                    */
/* ------------------------------------------------------------------ */

/** Products three levels deep (Laptops → Dell → Vostro Series) get the carousel; shallower ones keep the original four-card grid. */
export function usesRelatedCarousel(product: Pick<ShopProduct, 'categoryPath'>): boolean {
    return product.categoryPath.length >= 3;
}

const CAROUSEL_LIMIT = 10;

/**
 * Carousel picks, best match first: the same series, then the same brand
 * in the same category, then anything else in the same top-level category.
 */
export function carouselRelated(product: ShopProduct, catalogue: ShopProduct[], limit = CAROUSEL_LIMIT): ShopProduct[] {
    const others = catalogue.filter((p) => p.id !== product.id);
    const shares = (p: ShopProduct, depth: number) => product.categoryPath.slice(0, depth).every((slug, i) => p.categoryPath[i] === slug);

    const tiers = [others.filter((p) => shares(p, 3)), others.filter((p) => shares(p, 2)), others.filter((p) => shares(p, 1))];
    const picked: ShopProduct[] = [];
    for (const tier of tiers) {
        for (const p of tier) if (!picked.includes(p) && picked.length < limit) picked.push(p);
    }
    return picked;
}

/** The "frequently bought together" four: the product's own `relatedIds`, otherwise popular accessories. */
export function gridRelated(product: ProductDetail, catalogue: ShopProduct[], limit = 4): ShopProduct[] {
    const ids = product.relatedIds?.length ? product.relatedIds : defaultRelatedIds;
    return ids
        .filter((id) => id !== product.id)
        .map((id) => catalogue.find((p) => p.id === id))
        .filter((p): p is ShopProduct => Boolean(p))
        .slice(0, limit);
}
