import { ShoppingCart } from 'lucide-react';
import { ProductCard } from '@/components/products';
import { ScrollControls } from '@/components/common/ScrollControls';
import { productHref } from '@/lib/routes';
import { useScrollCarousel } from '@/hooks/useScrollCarousel';
import { formatPrice } from '@/lib/format';
import { carouselRelated, gridRelated, usesRelatedCarousel } from '@/lib/product-detail';
import type { Product } from '@/types/product';
import type { ProductDetail } from '@/types/product-detail';
import type { ShopProduct } from '@/types/shop';

interface RelatedProductsProps {
    product: ProductDetail;
    catalogue: ShopProduct[];
    onAddToCart?: (product: Product) => void;
    onToggleWishlist?: (product: Product, wishlisted: boolean) => void;
}

/**
 * "You may also like". Products three levels deep (Laptops → Dell → Vostro
 * Series) get a scrolling carousel of similar machines. Everything shallower
 * keeps the original four-card "frequently bought together" grid.
 */
export function RelatedProducts(props: RelatedProductsProps) {
    return usesRelatedCarousel(props.product) ? <RelatedCarousel {...props} /> : <RelatedGrid {...props} />;
}

/* ---------------- Original layout: four add-on cards ---------------- */

function RelatedGrid({ product, catalogue, onAddToCart }: RelatedProductsProps) {
    const items = gridRelated(product, catalogue);
    if (items.length === 0) return null;

    return (
        <section aria-labelledby="related-title">
            <div className="mb-5">
                <h2 id="related-title" className="text-[22px] font-extrabold text-[#152B4D] md:text-[26px]">
                    You May Also Like
                </h2>
                <p className="mt-1 text-[14px] text-[#5B6470]">Frequently bought together with the {product.name}</p>
            </div>

            <ul className="grid grid-cols-2 gap-3 md:gap-5 lg:grid-cols-4">
                {items.map((item) => (
                    <li key={item.id} className="flex">
                        <article className="group flex w-full flex-col overflow-hidden rounded-xl border border-[#E4E7EC] bg-white transition-shadow duration-300 hover:shadow-[0_10px_24px_rgba(21,43,77,0.12)] motion-reduce:transition-none">
                            <a href={productHref(item)} tabIndex={-1} aria-hidden="true" className="block aspect-[4/3] overflow-hidden bg-[#F4F5F7]">
                                <img src={item.image} alt="" loading="lazy" className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none" />
                            </a>
                            <div className="flex flex-1 flex-col p-3.5">
                                <p className="mb-1 text-[11px] font-extrabold uppercase tracking-wider text-[#325A96]">{item.brand}</p>
                                <h3 className="mb-2 flex-1 text-[14px] font-bold leading-snug text-[#152B4D]">
                                    <a href={productHref(item)} className="hover:text-[#325A96] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]">
                                        {item.name}
                                    </a>
                                </h3>
                                <p className="mb-3 text-[15px] font-extrabold text-[#152B4D]">{formatPrice(item.price)}</p>
                                <button
                                    type="button"
                                    onClick={() => onAddToCart?.(item)}
                                    className="inline-flex items-center justify-center gap-2 rounded-lg border-[1.5px] border-[#325A96] px-3 py-2 text-[13px] font-bold text-[#325A96] transition-colors hover:bg-[#FAB40A] hover:border-[#FAB40A] hover:text-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]"
                                >
                                    <ShoppingCart className="h-4 w-4" aria-hidden="true" />
                                    Add to Cart
                                </button>
                            </div>
                        </article>
                    </li>
                ))}
            </ul>
        </section>
    );
}

/* ---------------- Three-level products: carousel ---------------- */

function RelatedCarousel({ product, catalogue, onAddToCart, onToggleWishlist }: RelatedProductsProps) {
    const items = carouselRelated(product, catalogue);
    const { containerRef, canScrollPrev, canScrollNext, scrollPrev, scrollNext } = useScrollCarousel<HTMLUListElement>();
    if (items.length === 0) return null;

    return (
        <section aria-labelledby="related-title" aria-roledescription="carousel">
            <div className="mb-5 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 id="related-title" className="text-[22px] font-extrabold text-[#152B4D] md:text-[26px]">
                        You Might Also Like
                    </h2>
                    <p className="mt-1 text-[14px] text-[#5B6470]">Similar machines picked for the {product.name}</p>
                </div>
                <ScrollControls onPrev={scrollPrev} onNext={scrollNext} canPrev={canScrollPrev} canNext={canScrollNext} label="related products" />
            </div>

            <ul
                ref={containerRef}
                tabIndex={0}
                aria-label="Related products"
                className="-mx-1 flex snap-x snap-mandatory gap-5 overflow-x-auto px-1 pb-5 pt-1.5 outline-none [scrollbar-width:none] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#325A96] [&::-webkit-scrollbar]:hidden"
            >
                {items.map((item) => (
                    <li key={item.id} className="w-[240px] shrink-0 snap-start sm:w-[260px]">
                        <ProductCard product={item} onAddToCart={onAddToCart} onToggleWishlist={onToggleWishlist} className="h-full" />
                    </li>
                ))}
            </ul>
        </section>
    );
}
