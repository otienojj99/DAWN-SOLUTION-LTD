import { ArrowRight, Eye } from 'lucide-react';
import type { HeroSlide as HeroSlideData } from '@/types/hero';
import type { Product } from '@/types/product';
import { productHref } from '@/data/products';

interface HeroSlideProps {
    slide: HeroSlideData;
    product: Product;
    active: boolean;
}

function formatPrice(amount: number, currency = 'KES'): string {
    return `${currency} ${amount.toLocaleString('en-KE')}`;
}

/**
 * Renders one hero slide's content. Purely presentational — HeroSection
 * owns which slide is active and the autoplay/arrow/dot logic; this
 * component just lays out a given (slide, product) pair elegantly.
 *
 * The product shown here is real, resolved data (see data/products.ts) —
 * both the "View Product" link and the image/price come from the same
 * object, so they can never fall out of sync.
 */
export function HeroSlide({ slide, product, active }: HeroSlideProps) {
    const href = productHref(product);
    const discountPct = product.oldPrice
        ? Math.round(((product.oldPrice - product.price) / product.oldPrice) * 100)
        : null;

    return (
        <div
            className={`absolute inset-0 mx-auto flex max-w-[1400px] items-center px-6 transition-opacity duration-700 ease-out motion-reduce:transition-none md:px-14 ${
                active ? 'opacity-100' : 'pointer-events-none opacity-0'
            }`}
            aria-hidden={!active}
            role="group"
            aria-roledescription="slide"
            aria-label={product.name}
        >
            {/* ===== Text column ===== */}
            <div className="relative z-[3] max-w-[540px] text-white">
                <span className="mb-5 inline-flex items-center gap-2 rounded-full border border-[#FAB40A]/50 bg-[#FAB40A]/10 px-3.5 py-1.5 text-[11px] font-bold uppercase tracking-[0.08em] text-[#FAB40A]">
                    <span className="h-1.5 w-1.5 rounded-full bg-[#FAB40A]" aria-hidden="true" />
                    {slide.eyebrow}
                </span>

                <h1 className="mb-4 text-[2.75rem] font-extrabold leading-[1.08] tracking-tight md:text-[3.25rem]">
                    {slide.headline}
                    <br />
                    <span className="text-[#FAB40A]">{slide.headlineHighlight}</span>
                </h1>

                <p className="mb-7 max-w-[440px] text-[15.5px] leading-relaxed text-white/75">{slide.description}</p>

                <dl className="mb-8 flex flex-wrap gap-x-7 gap-y-3">
                    {product.specs.slice(0, 3).map((spec) => (
                        <div key={spec.label} className="border-l border-white/20 pl-4 first:border-l-0 first:pl-0">
                            <dd className="text-lg font-extrabold leading-none">{spec.value}</dd>
                            <dt className="mt-1.5 text-[10.5px] font-medium uppercase tracking-wide text-white/50">{spec.label}</dt>
                        </div>
                    ))}
                </dl>

                <div className="flex flex-wrap items-center gap-4">
                    <a
                        href={slide.ctaHref}
                        className="inline-flex items-center gap-2 rounded-lg bg-[#FAB40A] px-7 py-3.5 text-[14.5px] font-bold text-[#152B4D] shadow-[0_10px_24px_rgba(250,180,10,0.3)] transition-transform duration-150 hover:-translate-y-0.5 hover:bg-[#DE9E00]"
                    >
                        {slide.ctaLabel}
                    </a>
                    <a
                        href={href}
                        className="group inline-flex items-center gap-1.5 border-b-2 border-white/35 pb-0.5 text-[14px] font-semibold text-white transition-colors duration-150 hover:border-[#FAB40A] hover:text-[#FAB40A]"
                    >
                        View Product
                        <ArrowRight className="h-4 w-4 transition-transform duration-150 group-hover:translate-x-0.5" aria-hidden="true" />
                    </a>
                </div>
            </div>

            {/* ===== Image column ===== */}
            <div className="relative ml-auto hidden h-full flex-1 items-center justify-center md:flex">
                <a href={href} aria-label={`View ${product.name}`} className="group relative block">
                    <img
                        src={product.image}
                        alt={product.name}
                        className="max-h-[420px] w-full max-w-[560px] object-contain drop-shadow-[0_30px_40px_rgba(0,0,0,0.45)] transition-transform duration-500 ease-out group-hover:scale-[1.03]"
                    />

                    {/* Hover reveal — visual affordance that the image itself is clickable */}
                    <span className="absolute inset-0 flex items-center justify-center rounded-2xl bg-[#152B4D]/0 opacity-0 transition-all duration-200 group-hover:bg-[#152B4D]/10 group-hover:opacity-100">
                        <span className="flex h-14 w-14 items-center justify-center rounded-full bg-white/95 text-[#152B4D] shadow-xl">
                            <Eye className="h-6 w-6" aria-hidden="true" />
                        </span>
                    </span>
                </a>

                {/* Glass price card */}
                <a
                    href={href}
                    className="absolute bottom-10 right-2 rounded-xl border border-white/20 bg-white/10 px-5 py-3.5 text-white backdrop-blur-md transition-colors duration-150 hover:bg-white/15 md:right-6"
                >
                    <div className="flex items-center gap-2">
                        <span className="text-[10px] font-semibold uppercase tracking-wide text-white/60">
                            {discountPct ? `Save ${discountPct}%` : 'Starting from'}
                        </span>
                        {discountPct && (
                            <span className="rounded bg-[#E14B4B] px-1.5 py-px text-[9px] font-extrabold uppercase text-white">Sale</span>
                        )}
                    </div>
                    <div className="mt-0.5 flex items-baseline gap-2">
                        <span className="text-xl font-extrabold text-[#FAB40A]">{formatPrice(product.price, product.currency)}</span>
                        {product.oldPrice && (
                            <span className="text-[12px] text-white/45 line-through">{formatPrice(product.oldPrice, product.currency)}</span>
                        )}
                    </div>
                </a>
            </div>
        </div>
    );
}
