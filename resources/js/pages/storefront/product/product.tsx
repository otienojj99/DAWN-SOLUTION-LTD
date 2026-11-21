import { Header } from '@/components/header';
import { Footer } from '@/components/Footer';
import { ProductDetailPage } from '@/components/product-detail';
import { defaultShippingInfo } from '@/data/product-config';
import type { ProductBadge, ProductSpec } from '@/types/product';
import type { ProductDetail, VariantGroup, NewReview  } from '@/types/product-detail';
import type { CategoryNode, ShopProduct } from '@/types/shop';
import { api } from '@/lib/api';



interface ProductPageProps {
    product: StorefrontProduct;
    related: StorefrontProduct[];
}

interface StorefrontProduct {
    id: number | string;
    name: string;
    slug: string;
    sku: string;
    short_description: string | null;
    long_description?: string | null;
    specifications: ProductSpec[] | null;
    stock_quantity: number;
    is_best_seller: boolean;
    is_in_stock: boolean;
    in_stock: boolean;
    badge: ProductBadge | null;
    price: {
        base_kes: number;
        effective_kes: number;
        compare_at_kes: number | null;
        currency: string;
    };
    category: {
        name: string;
        slug: string;
        path?: string;
        breadcrumb?: string;
    } | null;
    brand: {
        name: string;
        slug: string;
    } | null;
    images: ResourceList<{
        url: string | null;
        alt_text: string | null;
    }>;
    variants?: ResourceList<{
        sku: string;
        name: string;
        price: number | null;
        stock_quantity: number;
        allow_backorder: boolean;
        is_default: boolean;
        attributes: ResourceList<{
            attribute_name: string | null;
            attribute_slug: string | null;
            value: string;
        }>;
    }>;
    use_cases?: ResourceList<{
        name: string;
    }>;
}

type ResourceList<T> = T[] | { data: T[] };

function collectionItems<T>(value: ResourceList<T> | null | undefined): T[] {
    if (Array.isArray(value)) return value;
    return value && Array.isArray(value.data) ? value.data : [];
}

function categoryPath(product: StorefrontProduct): string[] {
    const path = product.category?.path;

    if (typeof path === 'string' && path.length > 0) {
        return path.split('/').filter(Boolean);
    }

    return product.category?.slug ? [product.category.slug] : [];
}

function categoryTree(product: StorefrontProduct, path: string[]): CategoryNode[] {
    if (path.length === 0) return [];

    const labels = product.category?.breadcrumb?.split(' › ') ?? [];
    let child: CategoryNode | undefined;

    for (let index = path.length - 1; index >= 0; index--) {
        const label = labels[index] ?? (index === path.length - 1
            ? product.category?.name ?? path[index]
            : path[index].replaceAll('-', ' '));

        child = {
            slug: path[index],
            label,
            heading: label,
            children: child ? [child] : [],
        };
    }

    return child ? [child] : [];
}

function variantGroups(product: StorefrontProduct): VariantGroup[] {
    const groups = new Map<string, VariantGroup>();

    for (const variant of collectionItems(product.variants)) {
        for (const attribute of collectionItems(variant.attributes)) {
            if (!attribute.attribute_name || !attribute.value) continue;

            const id = attribute.attribute_slug ?? attribute.attribute_name.toLowerCase().replaceAll(/\s+/g, '-');
            let group = groups.get(id);

            if (!group) {
                group = {
                    id,
                    label: attribute.attribute_name,
                    options: [],
                    defaultValue: attribute.value,
                };
                groups.set(id, group);
            }

            if (!group.options.some((option) => option.value === attribute.value)) {
                group.options.push({
                    value: attribute.value,
                    priceDelta: variant.price === null ? 0 : variant.price - product.price.base_kes,
                    unavailable: variant.stock_quantity <= 0 && !variant.allow_backorder,
                });
            }

            if (variant.is_default) group.defaultValue = attribute.value;
        }
    }

    return [...groups.values()];
}

function toShopProduct(product: StorefrontProduct): ShopProduct {
    const path = categoryPath(product);
    const specs = product.specifications ?? [];
    const images = collectionItems(product.images);

    return {
        id: String(product.id),
        slug: product.slug,
        name: product.name,
        brand: product.brand?.name ?? 'Unknown',
        brandSlug: product.brand?.slug,
        category: path[0] ?? product.category?.name ?? 'Other',
        categorySlug: path[path.length - 1],
        categoryPath: path,
        image: images.find((image) => image.url)?.url ?? null,
        price: product.price.effective_kes,
        oldPrice: product.price.compare_at_kes,
        currency: product.price.currency,
        specs,
        inStock: product.is_in_stock,
        stockCount: product.stock_quantity,
        badge: product.badge,
        bestSeller: product.is_best_seller,
        addedAt: '',
    };
}

function toProductDetail(product: StorefrontProduct, related: StorefrontProduct[]): ProductDetail {
    const shopProduct = toShopProduct(product);
    const specs = product.specifications ?? [];
    const images = collectionItems(product.images);

    return {
        ...shopProduct,
        relatedIds: related.map((item) => String(item.id)),
        sku: product.sku,
        shortDescription: product.short_description ?? '',
        gallery: images
            .filter((image): image is typeof image & { url: string } => image.url !== null)
            .map((image) => ({
                src: image.url,
                thumb: image.url,
                alt: image.alt_text ?? product.name,
            })),
        variants: variantGroups(product),
        tags: collectionItems(product.use_cases).map((useCase) => useCase.name),
        overview: product.long_description ?? product.short_description ?? '',
        highlights: [],
        specTable: specs,
        shippingInfo: defaultShippingInfo,
        videos: [],
        reviews: [],
    };
}

export default function Product({ product, related }: ProductPageProps) {

   async function handleSubmitReview(review: NewReview): Promise<void> {
    await api.post('/reviews', {
        product_id: review.productId,
        rating:     review.rating,
        name:       review.name,
        text:       review.text,
    });
}
    const detail = toProductDetail(product, related);

    return (
        <>
            <Header />
            <main>
                <ProductDetailPage
                    product={detail}
                    catalogue={related.map(toShopProduct)}
                    tree={categoryTree(product, detail.categoryPath)}
                    onSubmitReview={handleSubmitReview}
                />
            </main>
            <Footer />
        </>
    );
}
