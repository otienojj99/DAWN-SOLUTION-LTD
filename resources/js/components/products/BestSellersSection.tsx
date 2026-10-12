import { useEffect, useMemo, useState } from 'react';
import { products as allProducts } from '@/data/products';
import { useScrollCarousel } from '@/hooks/useScrollCarousel';
import type { Product } from '@/types/product';
import { FilterTabs } from '@/components/common/FilterTabs';
import { ScrollControls } from '@/components/common/ScrollControls';
import { SectionHeading } from '@/components/common/SectionHeading';
import { ProductCard } from './ProductCard';

const ALL_TAB = 'All';
const PANEL_ID = 'best-sellers-panel';

interface BestSellersSectionProps {
    /** Defaults to the demo catalog, filtered to products flagged `bestSeller`. */
    products?: Product[];
    title?: string;
    description?: string;
    onAddToCart?: (product: Product) => void;
    onToggleWishlist?: (product: Product, wishlisted: boolean) => void;
}

/**
 * "Best-Selling Products" — category filter tabs above a horizontally
 * scrolling row of ProductCards. Tabs are derived from the categories
 * present in the data, so adding a product in a new category adds its tab
 * automatically. Switching tabs resets the row to the start.
 */
export function BestSellersSection({
    products = allProducts.filter((p) => p.bestSeller),
    title = 'Best-Selling Products',
    description = 'Top picks our customers keep coming back for',
    onAddToCart,
    onToggleWishlist,
}: BestSellersSectionProps) {
    const [activeTab, setActiveTab] = useState(ALL_TAB);
    const { containerRef, canScrollPrev, canScrollNext, scrollPrev, scrollNext, reset } = useScrollCarousel<HTMLUListElement>();

    const tabs = useMemo(() => [ALL_TAB, ...Array.from(new Set(products.map((p) => p.category)))], [products]);

    const visible = useMemo(
        () => (activeTab === ALL_TAB ? products : products.filter((p) => p.category === activeTab)),
        [products, activeTab],
    );

    useEffect(() => {
        reset();
    }, [activeTab, reset]);

    return (
        <section aria-labelledby="best-sellers-heading" className="bg-[#F4F5F7]">
            <div className="mx-auto max-w-[1400px] px-4 py-12 md:px-10 md:py-14">
                <SectionHeading
                    id="best-sellers-heading"
                    title={title}
                    description={description}
                    actions={<FilterTabs tabs={tabs} active={activeTab} onChange={setActiveTab} panelId={PANEL_ID} label="Filter products by category" />}
                />

                <div className="mb-4 flex justify-end">
                    <ScrollControls
                        onPrev={scrollPrev}
                        onNext={scrollNext}
                        canPrev={canScrollPrev}
                        canNext={canScrollNext}
                        label="best-selling products"
                    />
                </div>

                <div id={PANEL_ID} role="tabpanel" aria-live="polite">
                    {visible.length === 0 && (
                        <p className="rounded-xl border border-dashed border-[#C8CED8] bg-white py-12 text-center text-sm text-[#5B6470]">
                            No best sellers in this category yet.
                        </p>
                    )}
                    {/* Always mounted so the carousel's scroll listeners stay attached across tab changes. */}
                    <ul
                        ref={containerRef}
                        className="-mx-1 flex snap-x snap-mandatory gap-5 overflow-x-auto px-1 pb-5 pt-1.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                    >
                        {visible.map((product) => (
                            <li key={product.id} className="w-[240px] shrink-0 snap-start sm:w-[260px]">
                                <ProductCard
                                    product={product}
                                    onAddToCart={onAddToCart}
                                    onToggleWishlist={onToggleWishlist}
                                    className="h-full"
                                />
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </section>
    );
}
