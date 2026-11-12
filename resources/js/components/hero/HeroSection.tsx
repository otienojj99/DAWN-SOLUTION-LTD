import { useEffect, useRef, useState, type FocusEvent } from 'react';
import { heroSlides } from '@/data/hero-slides';
import { usePrefersReducedMotion } from '@/hooks/usePrefersReducedMotion';
import type { Product } from '@/types/product';
import { HeroSlide } from './HeroSlide';
import { CarouselArrows } from './CarouselArrows';
import { CarouselDots } from './CarouselDots';

const AUTOPLAY_MS = 6000;

interface HeroSectionProps {
    products: Product[];
    slides?: typeof heroSlides;
}

/**
 * Hero carousel. Owns the active-slide index and autoplay timer; each
 * HeroSlide is purely presentational. Autoplay:
 *  - pauses on hover and on keyboard focus anywhere inside the hero
 *  - is skipped entirely when the user prefers reduced motion
 *  - resets its timer whenever the person navigates manually (arrow/dot),
 *    so a manual click doesn't get immediately overridden by the next tick
 */
export function HeroSection({ products, slides = heroSlides }: HeroSectionProps) {
    const featuredSlides = slides.slice(0, products.length);
    const [activeIndex, setActiveIndex] = useState(0);
    const [paused, setPaused] = useState(false);
    const resetSignalRef = useRef(0);
    const [resetSignal, setResetSignal] = useState(0);
    const prefersReducedMotion = usePrefersReducedMotion();
    const shouldAutoplay = !paused && !prefersReducedMotion && featuredSlides.length > 1;

    useEffect(() => {
        if (!shouldAutoplay) return;
        const id = setInterval(() => {
            setActiveIndex((i) => (i + 1) % featuredSlides.length);
        }, AUTOPLAY_MS);
        return () => clearInterval(id);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [shouldAutoplay, resetSignal, featuredSlides.length]);

    function goTo(index: number) {
        setActiveIndex(index);
        resetSignalRef.current += 1;
        setResetSignal(resetSignalRef.current);
    }

    function next() {
        goTo((activeIndex + 1) % featuredSlides.length);
    }

    function prev() {
        goTo((activeIndex - 1 + featuredSlides.length) % featuredSlides.length);
    }

    function handleBlur(event: FocusEvent<HTMLDivElement>) {
        // Only resume autoplay once focus has actually left the whole hero,
        // not just moved from one internal link to the next.
        if (!event.currentTarget.contains(event.relatedTarget as Node | null)) {
            setPaused(false);
        }
    }

    if (featuredSlides.length === 0) return null;

    return (
        <section
            className="relative isolate h-[560px] overflow-hidden bg-gradient-to-br from-[#152B4D] via-[#1D3A64] to-[#325A96] md:h-[600px]"
            role="region"
            aria-roledescription="carousel"
            aria-label="Featured products"
            onMouseEnter={() => setPaused(true)}
            onMouseLeave={() => setPaused(false)}
            onFocus={() => setPaused(true)}
            onBlur={handleBlur}
        >
            {/* Decorative depth — soft glow + diagonal accent, purely visual */}
            <div
                className="pointer-events-none absolute -right-24 -top-40 h-[620px] w-[620px] rounded-full bg-[#4A78C4]/25 blur-3xl"
                aria-hidden="true"
            />
            <div
                className="pointer-events-none absolute right-[22%] top-0 h-full w-[220px] -skew-x-[12deg] bg-[#FAB40A]/[0.06]"
                aria-hidden="true"
            />
            <div
                className="pointer-events-none absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-black/15 to-transparent"
                aria-hidden="true"
            />

            <div className="relative h-full w-full">
                {featuredSlides.map((slide, index) => (
                    <HeroSlide key={slide.id} slide={slide} product={products[index]} active={index === activeIndex} />
                ))}
            </div>

            {featuredSlides.length > 1 && (
                <>
                    <CarouselArrows onPrev={prev} onNext={next} />
                    <CarouselDots
                        count={slides.length}
                        activeIndex={activeIndex}
                        onSelect={goTo}
                        autoplayDuration={AUTOPLAY_MS}
                        paused={!shouldAutoplay}
                    />
                </>
            )}
        </section>
    );
}
