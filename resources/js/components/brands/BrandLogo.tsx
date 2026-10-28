import { useEffect, useRef, useState } from 'react';
import type { Brand } from '@/types/brand';

interface BrandLogoProps {
    brand: Brand;
    /** True for the duplicated copy in the marquee: removed from the tab order and hidden from screen readers. */
    decorative?: boolean;
}



export function BrandLogo({ brand, decorative = false }: BrandLogoProps){
    const [failed, setFailed] = useState(!brand.logo);
    const imgRef = useRef<HTMLImageElement>(null);

    // With server rendering, an image can fail before React attaches `onError`,
    // so the event never fires. On mount, check whether it already finished
    // loading with nothing in it, and fall back to the text name if so.
    useEffect(() => {
        const img = imgRef.current;
        if (img && img.complete && img.naturalWidth === 0) setFailed(true);
    }, []);

    return (
        <a
            href={brand.href}
            tabIndex={decorative ? -1 : undefined}
            aria-label={decorative ? undefined : `Shop ${brand.name}`}
            className="group flex h-16 min-w-[140px] shrink-0 items-center justify-center rounded-[10px] border-[1.5px] border-[#E4E7EC] bg-white px-6 transition-all duration-200 hover:-translate-y-0.5 hover:border-[#FAB40A] hover:shadow-[0_8px_18px_rgba(21,43,77,0.1)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96] motion-reduce:transition-none motion-reduce:hover:translate-y-0"
        >
           {failed || !brand.logo ? (
    <span className="whitespace-nowrap text-[17px] font-extrabold tracking-wide text-[#9AA3AF] transition-colors duration-200 group-hover:text-[#152B4D]">
        {brand.name}
    </span>
        ) : (
            <img
                ref={imgRef}
                src={brand.logo}
                alt={decorative ? '' : brand.name}
                loading="lazy"
                onError={() => setFailed(true)}
                className="h-8 w-auto max-w-[110px] object-contain opacity-60 grayscale transition-all duration-200 group-hover:opacity-100 group-hover:grayscale-0"
            />
        )}
        </a>
    );
}