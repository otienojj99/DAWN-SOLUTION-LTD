import type { ShopCategory } from '@/types/category';

interface CategoryCircleProps {
    category: ShopCategory;
}

/**
 * One circular category tile. The whole tile is a single link. Hover
 * lifts the circle, zooms the photo and flips the ring to gold; both
 * effects are disabled under prefers-reduced-motion.
 */
export function CategoryCircle({ category }: CategoryCircleProps) {
    return (
        <a
            href={category.href}
            className="group flex w-[120px] shrink-0 snap-start flex-col items-center text-center focus-visible:outline-none sm:w-[150px]"
        >
            <span className="relative block h-[104px] w-[104px] overflow-hidden rounded-full border-[3px] border-[#EAF0F9] bg-[#F4F5F7] transition-all duration-300 group-hover:-translate-y-1.5 group-hover:border-[#FAB40A] group-hover:shadow-[0_14px_26px_rgba(21,43,77,0.18)] group-focus-visible:border-[#FAB40A] group-focus-visible:ring-2 group-focus-visible:ring-[#325A96] group-focus-visible:ring-offset-2 motion-reduce:transition-none motion-reduce:group-hover:translate-y-0 sm:h-[130px] sm:w-[130px]">
                <img
                    src={category.image}
                    alt={category.imageAlt ?? ''}
                    loading="lazy"
                    className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.12] motion-reduce:transition-none motion-reduce:group-hover:scale-100"
                />
                <span
                    className="absolute inset-0 rounded-full bg-gradient-to-b from-transparent from-55% to-[#152B4D]/55"
                    aria-hidden="true"
                />
                {category.badge && (
                    <span className="absolute right-1.5 top-1.5 rounded-full bg-[#FAB40A] px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wide text-[#152B4D]">
                        {category.badge}
                    </span>
                )}
            </span>

            <span className="mt-3.5 text-[14.5px] font-bold text-[#2B2B2B] transition-colors duration-150 group-hover:text-[#325A96]">
                {category.label}
            </span>
            {category.productCount !== undefined && (
                <span className="mt-0.5 text-[11.5px] text-[#5B6470]">{category.productCount}+ products</span>
            )}
        </a>
    );
}
