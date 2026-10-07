import { ChevronLeft, ChevronRight } from 'lucide-react';

interface CarouselArrowsProps {
    onPrev: () => void;
    onNext: () => void;
}

/** Prev/next circular arrow buttons, positioned absolutely by the parent. */
export function CarouselArrows({ onPrev, onNext }: CarouselArrowsProps) {
    const base =
        'absolute top-1/2 z-20 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/25 bg-white/10 text-white backdrop-blur-sm transition-colors duration-200 hover:bg-[#FAB40A] hover:text-[#152B4D] hover:border-[#FAB40A] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white';

    return (
        <>
            <button type="button" onClick={onPrev} aria-label="Previous slide" className={`${base} left-5 md:left-6`}>
                <ChevronLeft className="h-5 w-5" aria-hidden="true" />
            </button>
            <button type="button" onClick={onNext} aria-label="Next slide" className={`${base} right-5 md:right-6`}>
                <ChevronRight className="h-5 w-5" aria-hidden="true" />
            </button>
        </>
    );
}
