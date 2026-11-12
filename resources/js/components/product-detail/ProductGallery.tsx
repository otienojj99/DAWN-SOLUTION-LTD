import { ChevronLeft, ChevronRight } from 'lucide-react';
import { useState } from 'react';
import type { MouseEvent } from 'react';
import type { GalleryImage } from '@/types/product-detail';

interface ProductGalleryProps {
    images: GalleryImage[];
}

/**
 * Main image with a thumbnail strip (a column beside it on large screens, a
 * row under it on phones). Hovering the main image with a mouse zooms in
 * on the point under the cursor; touch devices and keyboard users use the
 * thumbnails and the previous/next buttons instead.
 */
export function ProductGallery({ images }: ProductGalleryProps) {
    const [index, setIndex] = useState(0);
    const [zoomOrigin, setZoomOrigin] = useState<string | null>(null);

    if (images.length === 0) return null;
    const current = images[index];
    const many = images.length > 1;

    const go = (next: number) => setIndex((next + images.length) % images.length);

    function handleMove(event: MouseEvent<HTMLDivElement>) {
        const box = event.currentTarget.getBoundingClientRect();
        setZoomOrigin(`${((event.clientX - box.left) / box.width) * 100}% ${((event.clientY - box.top) / box.height) * 100}%`);
    }

    return (
        <div className="flex flex-col-reverse gap-3 lg:flex-row lg:gap-4">
            {many && (
                <ul aria-label="Product images" className="flex gap-2.5 overflow-x-auto pb-1 [scrollbar-width:none] lg:max-h-[520px] lg:flex-col lg:overflow-y-auto lg:overflow-x-visible lg:pb-0 [&::-webkit-scrollbar]:hidden">
                    {images.map((image, i) => (
                        <li key={image.src} className="shrink-0">
                            <button
                                type="button"
                                onClick={() => setIndex(i)}
                                aria-label={`Show image ${i + 1} of ${images.length}: ${image.alt}`}
                                aria-current={i === index ? 'true' : undefined}
                                className={`block h-16 w-16 overflow-hidden rounded-lg border-2 bg-white transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96] sm:h-[72px] sm:w-[72px] ${
                                    i === index ? 'border-[#325A96]' : 'border-[#E4E7EC] hover:border-[#9AA3AF]'
                                }`}
                            >
                                <img src={image.thumb ?? image.src} alt="" loading={i === 0 ? 'eager' : 'lazy'} className="h-full w-full object-cover" />
                            </button>
                        </li>
                    ))}
                </ul>
            )}

            <div
                onMouseMove={handleMove}
                onMouseLeave={() => setZoomOrigin(null)}
                className="group relative aspect-square min-w-0 flex-1 overflow-hidden rounded-2xl border border-[#E4E7EC] bg-white [@media(hover:hover)]:cursor-zoom-in"
            >
                <img
                    src={current.src}
                    alt={current.alt}
                    style={zoomOrigin ? { transformOrigin: zoomOrigin, transform: 'scale(1.8)' } : undefined}
                    className="h-full w-full object-cover transition-transform duration-200 motion-reduce:transition-none"
                />

                {many && (
                    <>
                        <button
                            type="button"
                            onClick={() => go(index - 1)}
                            aria-label="Previous image"
                            className="absolute left-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-[#152B4D] shadow-md transition hover:bg-[#FAB40A] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]"
                        >
                            <ChevronLeft className="h-5 w-5" aria-hidden="true" />
                        </button>
                        <button
                            type="button"
                            onClick={() => go(index + 1)}
                            aria-label="Next image"
                            className="absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-[#152B4D] shadow-md transition hover:bg-[#FAB40A] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]"
                        >
                            <ChevronRight className="h-5 w-5" aria-hidden="true" />
                        </button>
                        <p className="absolute bottom-3 right-3 rounded-full bg-[#152B4D]/75 px-2.5 py-1 text-[11.5px] font-bold text-white" aria-live="polite">
                            {index + 1} / {images.length}
                        </p>
                    </>
                )}
            </div>
        </div>
    );
}
