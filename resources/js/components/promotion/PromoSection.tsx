import { ArrowRight } from 'lucide-react';
import { promoSection } from '@/data/promo-offers';
import type { PromoOffer } from '@/types/promo';
import { PromoCard } from './PromoCard';

interface PromoSectionProps {
    offers: PromoOffer[];
    title?: string;
    description?: string;
    viewAllLabel?: string;
    viewAllHref?: string;
}

/**
 * "Today's Best Offers" — one featured card spanning two rows beside a
 * 2x2 grid of standard cards on desktop; two columns on tablet (featured
 * full-width); a single stacked column on mobile.
 */
export function PromoSection({
    offers,
    title = promoSection.title,
    description = promoSection.description,
    viewAllLabel = promoSection.viewAllLabel,
    viewAllHref = '/shop/deals/best',
}: PromoSectionProps) {
    if (!offers.length) return null; 
    return (
        <section aria-labelledby="promo-heading" className="bg-[#F4F5F7]">
            <div className="mx-auto max-w-[1400px] px-4 py-10 md:px-10 md:py-12">
                <div className="mb-5 flex items-end justify-between gap-4">
                    <div>
                        <h2 id="promo-heading" className="text-xl font-extrabold text-[#152B4D] md:text-2xl">
                            {title}
                        </h2>
                        <p className="mt-1 text-[13.5px] text-[#5B6470]">{description}</p>
                    </div>
                    <a
                        href={viewAllHref}
                        className="group inline-flex shrink-0 items-center gap-1 text-[13.5px] font-bold text-[#325A96] transition-colors duration-150 hover:text-[#DE9E00]"
                    >
                        {viewAllLabel}
                        <ArrowRight className="h-4 w-4 transition-transform duration-150 group-hover:translate-x-0.5" aria-hidden="true" />
                    </a>
                </div>

                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:h-[460px] lg:grid-cols-[1.4fr_1fr_1fr] lg:grid-rows-2 lg:gap-[18px]">
                    {offers.map((offer) => (
                        <PromoCard key={offer.id} offer={offer} />
                    ))}
                </div>
            </div>
        </section>
    );
}
