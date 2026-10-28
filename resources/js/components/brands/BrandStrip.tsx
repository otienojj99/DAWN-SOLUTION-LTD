import { ArrowRight } from 'lucide-react';
// import { brands as defaultBrands } from '@/data/brands';
import type { Brand } from '@/types/brand';
import { BrandMarqueeRow } from './BrandMarqueeRow';

interface BrandStripProps {
    brands: Brand[];
    eyebrow?: string;
    title?: string;
    description?: string;
}


const brandSection = {
    eyebrow: 'Authorized Reseller',
    title: 'Trusted Global Brands',
    description: "We stock genuine products from the world's leading technology manufacturers",
    allBrandsLabel: 'Browse all brands',
    allBrandsHref: '/shop/brands',
};

/**
 * "Trusted Global Brands" — the brand list is split into two rows that
 * scroll in opposite directions, which keeps ~30 brands readable without a
 * crowded single row. Content comes from data/brands.ts or the `brands` prop.
 */
export function BrandStrip({
    brands,
    eyebrow = brandSection.eyebrow,
    title = brandSection.title,
    description = brandSection.description,
}: BrandStripProps) {

    if (!brands.length) return null;


    const middle = Math.ceil(brands.length / 2);
    const firstRow = brands.slice(0, middle);
    const secondRow = brands.slice(middle);

    return (
        <section
            aria-labelledby="brand-strip-heading"
            className="border-y border-[#E4E7EC] bg-[#F4F5F7] py-12 md:py-14"
        >
            <div className="mx-auto mb-9 max-w-xl px-4 text-center">
                <span className="mb-3 inline-block rounded-full bg-[#FAB40A]/15 px-3.5 py-1.5 text-[11.5px] font-extrabold uppercase tracking-[0.08em] text-[#8A6100]">
                    {eyebrow}
                </span>
                <h2 id="brand-strip-heading" className="mb-2 text-xl font-extrabold text-[#152B4D] md:text-2xl">
                    {title}
                </h2>
                <p className="text-[13.5px] text-[#5B6470]">{description}</p>
            </div>

            <div className="space-y-4">
                <BrandMarqueeRow brands={firstRow} direction="left" />
                {secondRow.length > 0 && <BrandMarqueeRow brands={secondRow} direction="right" />}
            </div>

            <p className="mt-8 text-center text-[12.5px] text-[#5B6470]">
                Want a specific brand?{' '}
                <a
                    href={brandSection.allBrandsHref}
                    className="group inline-flex items-center gap-1 font-bold text-[#325A96] transition-colors duration-150 hover:text-[#DE9E00]"
                >
                    {brandSection.allBrandsLabel}
                    <ArrowRight className="h-3.5 w-3.5 transition-transform duration-150 group-hover:translate-x-0.5" aria-hidden="true" />
                </a>
            </p>
        </section>
    );
}
