import { ArrowRight } from 'lucide-react';
import { resolveIcon } from '@/lib/icons';
import type { UseCase } from '@/types/collection';

interface UseCaseCardProps {
    useCase: UseCase;
}

/**
 * One "Shop by Use Case" banner. The whole card is a single link (one tab
 * stop, big click target); the CTA text is a visual affordance, not a
 * nested link. Hover lifts the card and zooms the photo, both disabled
 * under prefers-reduced-motion.
 */
export function UseCaseCard({ useCase }: UseCaseCardProps) {
    const Icon = resolveIcon(useCase.icon);

    return (
        <a
            href={useCase.href}
            className="group relative isolate flex h-[340px] items-end overflow-hidden rounded-2xl shadow-[0_8px_24px_rgba(21,43,77,0.1)] transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_20px_36px_rgba(21,43,77,0.22)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96] motion-reduce:transition-none motion-reduce:hover:translate-y-0 sm:h-[380px]"
        >
            {useCase.image ? (
                    <img
                        src={useCase.image}
                        alt={useCase.imageAlt ?? useCase.title}
                        loading="lazy"
                        className="absolute inset-0 -z-10 h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.08] motion-reduce:transition-none motion-reduce:group-hover:scale-100"
                    />
                ) : (
                    <div className="absolute inset-0 -z-10 bg-primary" aria-hidden="true" />
            )}
            <div
                className="absolute inset-0 -z-10 bg-gradient-to-b from-[#152B4D]/15 via-[#152B4D]/55 to-[#152B4D]/95 transition-opacity duration-300 group-hover:opacity-95"
                aria-hidden="true"
            />

            {useCase.itemCount !== undefined && (
                <span className="absolute right-4 top-4 rounded-full border border-white/30 bg-white/15 px-3 py-1 text-[11px] font-bold text-white backdrop-blur-md">
                    {useCase.itemCount}+ items
                </span>
            )}

            <div className="w-full p-6 text-white">
                <span className="mb-3.5 flex h-10 w-10 items-center justify-center rounded-[10px] border border-[#FAB40A]/50 bg-[#FAB40A]/20 text-[#FAB40A]">
                    <Icon className="h-5 w-5" strokeWidth={2} aria-hidden="true" />
                </span>

                <h3 className="mb-1.5 text-xl font-extrabold leading-tight">{useCase.title}</h3>
                <p className="mb-3.5 max-w-[230px] text-[12.5px] leading-relaxed text-white/80">{useCase.description}</p>

                <span className="inline-flex items-center gap-1.5 border-b-[1.5px] border-transparent pb-0.5 text-[12.5px] font-bold text-[#FAB40A] transition-colors duration-200 group-hover:border-[#FAB40A]">
                    {useCase.ctaLabel}
                    <ArrowRight
                        className="h-3.5 w-3.5 transition-transform duration-150 group-hover:translate-x-1 motion-reduce:transition-none"
                        aria-hidden="true"
                    />
                </span>
            </div>
        </a>
    );
}
