export function productHref(product: { slug: string }): string {
    return `/shop/products/${product.slug}`;
}