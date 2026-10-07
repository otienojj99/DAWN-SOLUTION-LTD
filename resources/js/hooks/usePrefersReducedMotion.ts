import { useEffect, useState } from 'react';

/**
 * Tracks the `prefers-reduced-motion` media query. Used by HeroSection to
 * decide whether to autoplay the carousel at all — CSS-level
 * `motion-reduce:` variants are enough to kill transition *animations*, but
 * autoplay itself (content changing every N seconds without user action) is
 * a behavior, not a CSS transition, so it needs a JS-level check.
 */
export function usePrefersReducedMotion(): boolean {
    const [prefersReduced, setPrefersReduced] = useState(false);

    useEffect(() => {
        const query = window.matchMedia('(prefers-reduced-motion: reduce)');
        setPrefersReduced(query.matches);

        function handleChange(event: MediaQueryListEvent) {
            setPrefersReduced(event.matches);
        }

        query.addEventListener('change', handleChange);
        return () => query.removeEventListener('change', handleChange);
    }, []);

    return prefersReduced;
}
