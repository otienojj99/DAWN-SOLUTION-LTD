import { ArrowRight } from 'lucide-react';
import type { PromoOffer } from '@/types/promo';

interface PromoCardProps {
    offer: PromoOffer;
}

/**
 * One promo tile. The whole card is a single link (one tab stop, large
 * click target) — the CTA text is a visual affordance, not a nested link.
 * Grid placement comes from the parent via `offer.size`.
 */
export function PromoCard({ offer }: PromoCardProps) {
    const featured = offer.size === 'featured';

    const placement = featured ? 'sm:col-span-2 lg:col-span-1 lg:col-start-1 lg:row-span-2 lg:row-start-1' : '';
    const height = featured ? 'h-[300px] sm:h-[360px] lg:h-auto' : 'h-[220px] lg:h-auto';

    const tagClasses =
        offer.tagStyle === 'accent'
            ? 'bg-[#FAB40A] text-[#152B4D]'
            : 'border border-white/40 bg-white/15 text-white backdrop-blur-sm';

    return (
        <a
            href={offer.href}
            className={`group relative isolate flex items-end overflow-hidden rounded-2xl bg-[#315f7c] shadow-[0_8px_24px_rgba(21,43,77,0.12)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_16px_34px_rgba(21,43,77,0.22)] motion-reduce:transition-none motion-reduce:hover:translate-y-0 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96] ${placement} ${height}`}
        >
            {offer.image ? (
                <img
                    src={offer.image}
                    alt={offer.imageAlt ?? ''}
                    loading="lazy"
                    className="absolute inset-0 -z-10 h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.06] motion-reduce:transition-none motion-reduce:group-hover:scale-100"
                />
            ) : null}
            <div
                className="absolute inset-0 -z-10 bg-gradient-to-t from-[#152B4D]/95 via-[#152B4D]/35 to-[#152B4D]/5"
                aria-hidden="true"
            />

            <div className={`w-full text-white ${featured ? 'p-6 sm:p-8' : 'p-5 sm:p-6'}`}>
                {offer.tag ? (
                    <span
                        className={`mb-3 inline-block rounded px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-wider ${tagClasses}`}
                    >
                        {offer.tag}
                    </span>
                ) : null}

                <h3 className={`mb-1 font-extrabold leading-tight ${featured ? 'max-w-[320px] text-2xl sm:text-3xl' : 'text-xl'}`}>
                    {offer.title}
                </h3>
                {offer.subtitle ? <p className="mb-3 text-[12.5px] text-white/80">{offer.subtitle}</p> : null}

                <span className="inline-flex items-center gap-1.5 text-[12.5px] font-bold text-[#FAB40A] transition-colors duration-150 group-hover:text-white">
                    {offer.ctaLabel}
                    <ArrowRight
                        className="h-3.5 w-3.5 transition-transform duration-150 group-hover:translate-x-1 motion-reduce:transition-none"
                        aria-hidden="true"
                    />
                </span>
            </div>
        </a>
    );
}
