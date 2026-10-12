import { ChevronLeft, ChevronRight } from 'lucide-react';

interface ScrollControlsProps {
    onPrev: () => void;
    onNext: () => void;
    canPrev: boolean;
    canNext: boolean;
    /** Describes what's being scrolled, for screen readers, e.g. "categories". */
    label: string;
}

/** Round prev/next buttons shared by every horizontal carousel section. Disabled at either end. */
export function ScrollControls({ onPrev, onNext, canPrev, canNext, label }: ScrollControlsProps) {
    const base =
        'flex h-9 w-9 items-center justify-center rounded-full border-[1.5px] border-[#325A96] bg-white text-[#325A96] transition-colors duration-200 hover:border-[#FAB40A] hover:bg-[#FAB40A] hover:text-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96] disabled:pointer-events-none disabled:opacity-30';

    return (
        <div className="flex gap-2.5">
            <button type="button" onClick={onPrev} disabled={!canPrev} aria-label={`Scroll ${label} left`} className={base}>
                <ChevronLeft className="h-4 w-4" aria-hidden="true" />
            </button>
            <button type="button" onClick={onNext} disabled={!canNext} aria-label={`Scroll ${label} right`} className={base}>
                <ChevronRight className="h-4 w-4" aria-hidden="true" />
            </button>
        </div>
    );
}
