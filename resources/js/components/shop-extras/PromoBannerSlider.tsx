import { ArrowRight, Pause, Play } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import type { FocusEvent, TouchEvent } from 'react';
import { CarouselArrows } from '@/components/hero/CarouselArrows';
import { CarouselDots } from '@/components/hero/CarouselDots';
import { resolveBannerGradient } from '@/lib/banner-gradients';
import { resolveBannerIcon } from '@/lib/banner-icons';
// import { shopBanners } from '@/data/shop-banners';
import { usePrefersReducedMotion } from '@/hooks/usePrefersReducedMotion';
import type { ShopBanner } from '@/types/banner';

const AUTOPLAY_MS = 7000;
const SWIPE_PX = 50;

interface PromoBannerSliderProps {
    banners: ShopBanner[];
}

/**
 * Promo slider for bundles, discounts, promotions and seasonal sales.
 *
 * Autoplay is a courtesy, not a trap: it pauses on hover and while anything
 * inside has keyboard focus, never starts when the visitor prefers reduced
 * motion, and a visible Pause button lets anyone stop it for good.
 * Slides that are off-screen are hidden from assistive tech and from Tab.
 */
export function PromoBannerSlider({ banners }: PromoBannerSliderProps) {
    const [index, setIndex] = useState(0);
    const [hovering, setHovering] = useState(false);
    const [focusWithin, setFocusWithin] = useState(false);
    const [userPaused, setUserPaused] = useState(false);
    const [tick, setTick] = useState(0); // bumps on manual navigation so the timer restarts
    const touchStartX = useRef<number | null>(null);
    const prefersReducedMotion = usePrefersReducedMotion();

    const count = banners.length;
    const paused = hovering || focusWithin || userPaused;
    const autoplay = !paused && !prefersReducedMotion && count > 1;

    useEffect(() => {
        if (!autoplay) return;
        const id = setInterval(() => setIndex((i) => (i + 1) % count), AUTOPLAY_MS);
        return () => clearInterval(id);
    }, [autoplay, count, tick]);

    if (count === 0) return null;

    function goTo(next: number) {
        setIndex((next + count) % count);
        setTick((t) => t + 1);
    }

    function handleBlur(event: FocusEvent<HTMLElement>) {
        if (!event.currentTarget.contains(event.relatedTarget as Node | null)) setFocusWithin(false);
    }

    function handleTouchEnd(event: TouchEvent) {
        if (touchStartX.current === null) return;
        const delta = event.changedTouches[0].clientX - touchStartX.current;
        touchStartX.current = null;
        if (Math.abs(delta) >= SWIPE_PX) goTo(index + (delta < 0 ? 1 : -1));
    }

    return (
        <section
            aria-roledescription="carousel"
            aria-label="Offers and promotions"
            onMouseEnter={() => setHovering(true)}
            onMouseLeave={() => setHovering(false)}
            onFocus={() => setFocusWithin(true)}
            onBlur={handleBlur}
            onTouchStart={(e) => (touchStartX.current = e.touches[0].clientX)}
            onTouchEnd={handleTouchEnd}
            className="relative isolate overflow-hidden rounded-2xl shadow-[0_10px_30px_rgba(21,43,77,0.15)] m-6 lg:my-14 "
        >
            <div
                className="flex transition-transform duration-700 ease-[cubic-bezier(.22,1,.36,1)] motion-reduce:transition-none"
                style={{ transform: `translateX(-${index * 100}%)` }}
                aria-live={autoplay ? 'off' : 'polite'}
            >
                {banners.map((banner, i) => (
                    <Slide key={banner.id} banner={banner} active={i === index} position={i + 1} total={count} />
                ))}
            </div>

            {count > 1 && (
                <>
                    <div className="hidden md:contents">
                        <CarouselArrows onPrev={() => goTo(index - 1)} onNext={() => goTo(index + 1)} />
                    </div>
                    <CarouselDots count={count} activeIndex={index} onSelect={goTo} autoplayDuration={AUTOPLAY_MS} paused={!autoplay} />
                    <button
                        type="button"
                        onClick={() => setUserPaused((v) => !v)}
                        aria-label={userPaused ? 'Play slideshow' : 'Pause slideshow'}
                        className="absolute bottom-3.5 right-4 z-20 flex h-8 w-8 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur-sm transition-colors hover:bg-[#FAB40A] hover:text-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    >
                        {userPaused ? <Play className="h-3.5 w-3.5" aria-hidden="true" /> : <Pause className="h-3.5 w-3.5" aria-hidden="true" />}
                    </button>
                </>
            )}
        </section>
    );
}

function Slide({ banner, active, position, total }: { banner: ShopBanner; active: boolean; position: number; total: number }) {
    const Icon = resolveBannerIcon(banner.icon);
    const gradient = resolveBannerGradient(banner.gradientKey);

    return (
        <div
            role="group"
            aria-roledescription="slide"
            aria-label={`${position} of ${total}: ${banner.title}`}
            aria-hidden={!active}
            className={`relative flex min-h-[340px] w-full shrink-0 items-center bg-gradient-to-br md:min-h-[320px] ${gradient}`}
        >
            {/* Photo: a faint backdrop on phones, a masked right-hand panel from md up. */}
            {banner.image ? (
                <img
                    src={banner.image}
                    alt={banner.imageAlt ?? banner.title}
                    loading="lazy"
                    className="absolute inset-y-0 right-0 h-full w-full object-cover opacity-20 md:w-[55%] md:opacity-100 [-webkit-mask-image:linear-gradient(to_right,transparent,black_45%)] [mask-image:linear-gradient(to_right,transparent,black_45%)]"
                />
            ) : null}
            <div className="pointer-events-none absolute -left-16 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl" aria-hidden="true" />

            <div className="relative z-10 max-w-[560px] px-6 pb-14 pt-8 sm:px-10 md:px-20 md:pb-10">
                <span className="mb-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-[12px] font-extrabold uppercase tracking-wider text-white backdrop-blur-sm">
                    <Icon className="h-3.5 w-3.5 text-[#FAB40A]" aria-hidden="true" />
                    {banner.tag}
                </span>
                <h2 className="mb-3 text-[26px] font-extrabold leading-tight text-white sm:text-[30px] md:text-[34px]">{banner.title}</h2>
                <p className="mb-5 max-w-[460px] text-[14.5px] leading-relaxed text-white/85">{banner.description}</p>
                <div className="flex flex-wrap items-center gap-x-5 gap-y-3">
                    <a
                        href={banner.href}
                        tabIndex={active ? 0 : -1}
                        className="group inline-flex items-center gap-2 rounded-lg bg-[#FAB40A] px-5 py-3 text-[14px] font-extrabold text-[#152B4D] transition-colors hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    >
                        {banner.ctaLabel}
                        <ArrowRight className="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1 motion-reduce:transition-none" aria-hidden="true" />
                    </a>
                    <span className="text-[15px] font-extrabold text-[#FAB40A]">{banner.highlight}</span>
                </div>
            </div>
        </div>
    );
}
