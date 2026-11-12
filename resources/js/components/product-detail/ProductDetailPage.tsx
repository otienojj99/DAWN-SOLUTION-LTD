import { Star, Video } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import { WhatsAppChatWidget } from '@/components/chat';
import { ShopBreadcrumb } from '@/components/shop';
import { shopProducts } from '@/data/shop-products';
import { shopTree } from '@/data/shop-tree';
import { formatPrice } from '@/lib/format';
import { productHref } from '@/lib/routes';
import { defaultSelection, priceFor, selectionSummary, skuFor } from '@/lib/product-detail';
import { breadcrumbs, resolveCategory } from '@/lib/shop-category';
import type { Product } from '@/types/product';
import type { NewReview, ProductDetail, PurchaseItem } from '@/types/product-detail';
import type { CategoryNode, ShopProduct } from '@/types/shop';
import { ProductGallery } from './ProductGallery';
import { ProductJsonLd } from './ProductJsonLd';
import { ProductTabs } from './ProductTabs';
import type { ProductTabId } from './ProductTabs';
import { ProductVideos } from './ProductVideos';
import { PurchasePanel } from './PurchasePanel';
import { RelatedProducts } from './RelatedProducts';

interface ProductDetailPageProps {
    product: ProductDetail;
    /** Other products, used for the related section. Defaults to the demo catalogue. */
    catalogue?: ShopProduct[];
    tree?: CategoryNode[];
    onAddToCart?: (item: PurchaseItem) => void | Promise<void>;
    /** "Order Now": add to cart and go to checkout. */
    onOrderNow?: (item: PurchaseItem) => void | Promise<void>;
    onSubmitReview?: (review: NewReview) => Promise<void>;
    /** Add-to-cart from the related products. */
    onAddRelatedToCart?: (product: Product) => void;
    onToggleWishlist?: (product: Product, wishlisted: boolean) => void;
}

const SITE_NAME = 'Dawn Solution';


export function ProductDetailPage({ product, catalogue = shopProducts, tree = shopTree, onAddToCart, onOrderNow, onSubmitReview, onAddRelatedToCart, onToggleWishlist }: ProductDetailPageProps) {
    const [selection, setSelection] = useState(() => defaultSelection(product.variants));
    const [quantity, setQuantity] = useState(1);
    const [tab, setTab] = useState<ProductTabId>('description');
    const [chatOpen, setChatOpen] = useState(false);

    const category = useMemo(() => resolveCategory(tree, product.categoryPath), [tree, product.categoryPath]);
    const crumbs = useMemo(() => [...breadcrumbs(category.trail), { label: product.name, href: productHref(product) }], [category.trail, product.name, product.slug]);
    const { price, oldPrice, savePercent } = priceFor(product, selection);
    const sku = skuFor(product, selection);
    const summary = selectionSummary(product, selection);
    const reviewCount = product.reviewCount ?? product.reviews.length;
    const brandLine = [product.brand, category.trail[category.trail.length - 1]?.label].filter(Boolean).join(' · ');

    useEffect(() => {
        document.title = `${product.name} | ${SITE_NAME}`;
    }, [product.name]);

    const buildItem = (): PurchaseItem => ({ productId: product.id, slug: product.slug, quantity, variants: selection, sku, unitPrice: price });

    function goToReviews() {
        setTab('reviews');
        // Wait a frame so the tab panel has switched before scrolling to it.
        requestAnimationFrame(() => document.getElementById('product-tabs')?.scrollIntoView({ block: 'start', behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }));
    }

    const chatMessage = `Hi ${SITE_NAME}, I'd like to know more about the ${product.name}${summary ? ` (${summary})` : ''}. Is it available?`;

    return (
        <div className="bg-white">
            <ProductJsonLd product={product} price={price} />

            <div className="mx-auto max-w-[1200px] px-4 pb-14 pt-5 sm:px-6">
                <ShopBreadcrumb crumbs={crumbs} />

                {/* Gallery + buying information */}
                <div className="mt-5 grid gap-8 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)] lg:gap-12">
                    <ProductGallery images={product.gallery} />

                    <div>
                        <p className="mb-1.5 text-[12.5px] font-extrabold uppercase tracking-wider text-[#325A96]">{brandLine}</p>
                        <h1 className="mb-3 text-[26px] font-extrabold leading-tight text-[#152B4D] md:text-[32px]">{product.name}</h1>

                        <div className="mb-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-[13px]">
                            {product.rating !== undefined && (
                                <button
                                    type="button"
                                    onClick={goToReviews}
                                    className="inline-flex items-center gap-2 rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]"
                                    aria-label={`Rated ${product.rating.toFixed(1)} out of 5 from ${reviewCount} reviews. Go to reviews`}
                                >
                                    <span className="flex gap-0.5" aria-hidden="true">
                                        {[1, 2, 3, 4, 5].map((n) => (
                                            <Star key={n} className={`h-4 w-4 ${n <= Math.round(product.rating ?? 0) ? 'fill-[#FAB40A] text-[#FAB40A]' : 'fill-transparent text-[#D5DAE1]'}`} strokeWidth={2} />
                                        ))}
                                    </span>
                                    <span className="font-semibold text-[#325A96] underline-offset-4 hover:underline" aria-hidden="true">
                                        {product.rating.toFixed(1)} ({reviewCount} reviews)
                                    </span>
                                </button>
                            )}
                            <span className="text-[#5B6470]">SKU: {sku}</span>
                            {product.videos.length > 0 && (
                                <a href="#product-video" className="inline-flex items-center gap-1.5 font-semibold text-[#325A96] underline-offset-4 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]">
                                    <Video className="h-4 w-4" aria-hidden="true" />
                                    Watch video
                                </a>
                            )}
                        </div>

                        <div className="mb-1 flex flex-wrap items-baseline gap-x-3 gap-y-1">
                            <p className="text-[32px] font-extrabold text-[#152B4D]">{formatPrice(price)}</p>
                            {oldPrice !== undefined && oldPrice > price && (
                                <>
                                    <p className="text-[16px] text-[#8A93A3] line-through">
                                        <span className="sr-only">Was </span>
                                        {formatPrice(oldPrice)}
                                    </p>
                                    <p className="rounded-full bg-[#C0292B] px-3 py-1 text-[12px] font-extrabold text-white">Save {savePercent}%</p>
                                </>
                            )}
                        </div>
                        <p className="mb-5 text-[12.5px] text-[#5B6470]">Price inclusive of VAT · Free delivery within Nairobi</p>

                        <p className="mb-6 text-[14.5px] leading-relaxed text-[#2B2B2B]">{product.shortDescription}</p>

                        <PurchasePanel
                            product={product}
                            selection={selection}
                            onSelectionChange={(groupId, value) => setSelection((s) => ({ ...s, [groupId]: value }))}
                            quantity={quantity}
                            onQuantityChange={setQuantity}
                            buildItem={buildItem}
                            onAddToCart={onAddToCart}
                            onOrderNow={onOrderNow}
                            onAskOnWhatsApp={() => setChatOpen(true)}
                        />
                    </div>
                </div>

                <div className="mt-12">
                    <ProductTabs product={product} active={tab} onChange={setTab} onSubmitReview={onSubmitReview} />
                </div>

                {product.videos.length > 0 && (
                    <div className="mt-14">
                        <ProductVideos videos={product.videos} productName={product.name} />
                    </div>
                )}
            </div>

            <div className="bg-[#F4F5F7]">
                <div className="mx-auto max-w-[1200px] px-4 py-12 sm:px-6">
                    <RelatedProducts product={product} catalogue={catalogue} onAddToCart={onAddRelatedToCart} onToggleWishlist={onToggleWishlist} />
                </div>
            </div>

            <WhatsAppChatWidget open={chatOpen} onOpenChange={setChatOpen} initialMessage={chatMessage} messageSuffix={() => `Product page: ${window.location.href}`} />
        </div>
    );
}