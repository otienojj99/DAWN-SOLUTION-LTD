interface CarouselDotsProps {
    count: number;
    activeIndex: number;
    onSelect: (index: number) => void;
    /** Autoplay interval in ms — the active dot's fill animates over this duration. */
    autoplayDuration: number;
    /** When true, pauses the fill animation (hover/focus) and disables autoplay. */
    paused: boolean;
}

/**
 * Progress-style carousel indicators: each is a thin bar, and the active
 * one visibly fills over the autoplay duration rather than just sitting
 * there as a static dot — gives the person a sense of pacing and makes it
 * obvious the carousel is about to advance, a small but deliberate "premium"
 * touch over a plain dot row.
 */
export function CarouselDots({ count, activeIndex, onSelect, autoplayDuration, paused }: CarouselDotsProps) {
    return (
        <div className="absolute bottom-6 left-1/2 z-20 flex -translate-x-1/2 gap-2.5" role="tablist" aria-label="Slides">
            {Array.from({ length: count }).map((_, index) => {
                const isActive = index === activeIndex;
                return (
                    <button
                        key={index}
                        type="button"
                        role="tab"
                        aria-selected={isActive}
                        aria-label={`Go to slide ${index + 1} of ${count}`}
                        onClick={() => onSelect(index)}
                        className="group relative h-1.5 w-9 overflow-hidden rounded-full bg-white/25 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    >
                        {isActive && (
                            <span
                                key={`${activeIndex}-${paused}`} // remount to restart the fill animation on every slide change
                                className="absolute inset-y-0 left-0 block bg-[#FAB40A] motion-reduce:w-full"
                                style={{
                                    animation: paused ? 'none' : `dsl-hero-progress ${autoplayDuration}ms linear forwards`,
                                    width: paused ? '100%' : undefined,
                                }}
                            />
                        )}
                        {!isActive && <span className="absolute inset-y-0 left-0 w-0 bg-white/70 transition-[width] duration-200 group-hover:w-full" />}
                    </button>
                );
            })}

            <style>{`
                @keyframes dsl-hero-progress {
                    from { width: 0%; }
                    to { width: 100%; }
                }
            `}</style>
        </div>
    );
}
