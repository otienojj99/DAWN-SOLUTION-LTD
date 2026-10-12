import { useCallback, useEffect, useRef, useState } from 'react';

/**
 * Drives a horizontally scrolling (CSS scroll-snap) carousel.
 *
 * Using the browser's native scrolling instead of transform math means
 * touch swipe, trackpad, keyboard scroll and mouse wheel all work for free
 * on mobile, and the prev/next buttons simply nudge `scrollLeft`. The hook
 * only tracks whether there's more content to scroll to in each direction
 * so the buttons can disable themselves at the ends.
 *
 * Attach `containerRef` to the scrollable element.
 */
export function useScrollCarousel<T extends HTMLElement = HTMLDivElement>() {
    const containerRef = useRef<T>(null);
    const [canScrollPrev, setCanScrollPrev] = useState(false);
    const [canScrollNext, setCanScrollNext] = useState(false);

    const update = useCallback(() => {
        const el = containerRef.current;
        if (!el) return;
        const maxScroll = el.scrollWidth - el.clientWidth;
        setCanScrollPrev(el.scrollLeft > 4);
        setCanScrollNext(el.scrollLeft < maxScroll - 4);
    }, []);

    useEffect(() => {
        const el = containerRef.current;
        if (!el) return;

        update();
        el.addEventListener('scroll', update, { passive: true });

        const observer = new ResizeObserver(update);
        observer.observe(el);

        return () => {
            el.removeEventListener('scroll', update);
            observer.disconnect();
        };
    }, [update]);

    /** Scrolls by ~80% of the visible width so a couple of items stay in view for context. */
    const scrollByPage = useCallback((direction: 1 | -1) => {
        const el = containerRef.current;
        if (!el) return;
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        el.scrollBy({
            left: direction * el.clientWidth * 0.8,
            behavior: reduceMotion ? 'auto' : 'smooth',
        });
    }, []);

    /** Call when the carousel's items change (e.g. a tab filter) to reset position and recompute button state. */
    const reset = useCallback(() => {
        const el = containerRef.current;
        if (el) el.scrollTo({ left: 0 });
        requestAnimationFrame(update);
    }, [update]);

    return {
        containerRef,
        canScrollPrev,
        canScrollNext,
        scrollPrev: () => scrollByPage(-1),
        scrollNext: () => scrollByPage(1),
        reset,
    };
}
