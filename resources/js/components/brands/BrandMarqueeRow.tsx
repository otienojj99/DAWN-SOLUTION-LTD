import type { Brand } from '@/types/brand';
import { BrandLogo } from './BrandLogo';

interface BrandMarqueeRowProps {
    brands: Brand[];
    direction?: 'left' | 'right';
    /** Seconds to travel one full loop. Defaults to a pace based on the number of brands. */
    durationSeconds?: number;
}



export function BrandMarqueeRow({brands, direction = 'left', durationSeconds}: BrandMarqueeRowProps){
     const duration = durationSeconds ?? Math.max(24, brands.length * 3);

     return (
        <div
            className="overflow-x-auto [scrollbar-width:none] motion-safe:overflow-hidden motion-safe:[mask-image:linear-gradient(90deg,transparent,#000_8%,#000_92%,transparent)] [&::-webkit-scrollbar]:hidden"
        >
            <ul
                className={`dsl-brand-track flex w-max gap-4 py-2 ${direction === 'right' ? 'dsl-brand-track--right' : ''}`}
                style={{ ['--dsl-brand-duration' as string]: `${duration}s` }}
            >
                {brands.map((brand) => (
                    <li key={brand.id}>
                        <BrandLogo brand={brand} />
                    </li>
                ))}
                {brands.map((brand) => (
                    <li key={`${brand.id}-copy`} aria-hidden="true" className="motion-reduce:hidden">
                        <BrandLogo brand={brand} decorative />
                    </li>
                ))}
            </ul>

            <style>{`
                .dsl-brand-track {
                    animation: dsl-brand-left var(--dsl-brand-duration, 40s) linear infinite;
                }
                .dsl-brand-track--right {
                    animation-name: dsl-brand-right;
                }
                .dsl-brand-track:hover,
                .dsl-brand-track:focus-within {
                    animation-play-state: paused;
                }
                @keyframes dsl-brand-left {
                    from { transform: translateX(0); }
                    to { transform: translateX(-50%); }
                }
                @keyframes dsl-brand-right {
                    from { transform: translateX(-50%); }
                    to { transform: translateX(0); }
                }
                @media (prefers-reduced-motion: reduce) {
                    .dsl-brand-track { animation: none; }
                }
            `}</style>
        </div>
    );
}