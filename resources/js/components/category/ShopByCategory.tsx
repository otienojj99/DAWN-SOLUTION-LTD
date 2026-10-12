// import { shopCategories, shopCategorySection } from '@/data/shop-categories';
import { useScrollCarousel } from '@/hooks/useScrollCarousel';
import type { ShopCategory } from '@/types/category';
import { ScrollControls } from '@/components/common/ScrollControls';
import { SectionHeading } from '@/components/common/SectionHeading';
import { CategoryCircle } from './CategoryCircle';

interface ShopByCategoryProps {
    categories: ShopCategory[];    // ← required, no default
    title?: string;
    description?: string;
}

/**
 * "Shop by Category" — circular tiles in a horizontally scrolling,
 * scroll-snapping row. Swipe on touch devices, arrow buttons on desktop
 * (disabled at either end). Content comes from data/shop-categories.ts
 * or the `categories` prop.
 */
export function ShopByCategory({
    categories,
    title = 'Shop by Category',
    description = 'Browse our full range across every product line',
}: ShopByCategoryProps) {
    const { containerRef, canScrollPrev, canScrollNext, scrollPrev, scrollNext } = useScrollCarousel<HTMLUListElement>();

     if (!categories.length) return null; 
    return (
        <section aria-labelledby="shop-by-category-heading" className="bg-white">
            <div className="mx-auto max-w-[1400px] px-4 py-12 md:px-10 md:py-14">
                <SectionHeading
                    id="shop-by-category-heading"
                    title={title}
                    description={description}
                    actions={
                        <ScrollControls
                            onPrev={scrollPrev}
                            onNext={scrollNext}
                            canPrev={canScrollPrev}
                            canNext={canScrollNext}
                            label="categories"
                        />
                    }
                />

                <ul
                    ref={containerRef}
                    className="-mx-1 flex snap-x snap-mandatory gap-5 overflow-x-auto px-1 pb-5 pt-1.5 [scrollbar-width:none] sm:gap-7 [&::-webkit-scrollbar]:hidden"
                >
                    {categories.map((category) => (
                        <li key={category.id} className="shrink-0">
                            <CategoryCircle category={category} />
                        </li>
                    ))}
                </ul>
            </div>
        </section>
    );
}
