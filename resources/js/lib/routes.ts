import { show as showShopProduct } from '@/routes/shop/products';

export function productHref(product: { slug: string }): string {
    return showShopProduct.url({ product: product.slug });
}