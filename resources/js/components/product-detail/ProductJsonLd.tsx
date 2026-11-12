import type { ProductDetail } from '@/types/product-detail';

/**
 * schema.org Product data for search engines (price, availability, rating).
 * `<` is escaped so product text can never close the script tag.
 */
export function ProductJsonLd({ product, price }: { product: ProductDetail; price: number }) {
    const data = {
        '@context': 'https://schema.org',
        '@type': 'Product',
        name: product.name,
        sku: product.sku,
        brand: { '@type': 'Brand', name: product.brand },
        image: product.gallery.map((g) => g.src),
        description: product.shortDescription,
        offers: {
            '@type': 'Offer',
            priceCurrency: product.currency ?? 'KES',
            price,
            availability: product.inStock === false ? 'https://schema.org/OutOfStock' : 'https://schema.org/InStock',
        },
        ...(product.rating && product.reviewCount
            ? { aggregateRating: { '@type': 'AggregateRating', ratingValue: product.rating, reviewCount: product.reviewCount } }
            : {}),
    };

    return <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(data).replace(/</g, '\\u003c') }} />;
}
