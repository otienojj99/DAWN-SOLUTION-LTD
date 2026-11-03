import { ChevronLeft, ChevronRight } from 'lucide-react';
import type { MouseEvent } from 'react';
import { isPlainLeftClick } from '@/lib/links';

interface PaginationProps {
    page: number;
    pageCount: number;
    /** URL for a page, so every page number is a real link (crawlable, openable in a new tab). */
    hrefFor: (page: number) => string;
    onPageChange: (page: number) => void;
}

/** 1 … 4 5 [6] 7 8 … 20: always the first, last and the current page's neighbours. */
function pageItems(page: number, pageCount: number): (number | 'gap')[] {
    const wanted = new Set([1, pageCount, page - 1, page, page + 1]);
    const numbers = [...wanted].filter((n) => n >= 1 && n <= pageCount).sort((a, b) => a - b);
    const items: (number | 'gap')[] = [];
    numbers.forEach((n, i) => {
        if (i > 0 && n - numbers[i - 1] > 1) items.push('gap');
        items.push(n);
    });
    return items;
}

export function Pagination({ page, pageCount, hrefFor, onPageChange }: PaginationProps) {
    if (pageCount <= 1) return null;

    const base =
        'flex h-10 min-w-10 items-center justify-center rounded-lg border-[1.5px] px-3 text-[13.5px] font-bold transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]';

    const go = (target: number) => (event: MouseEvent) => {
        if (isPlainLeftClick(event)) {
            event.preventDefault();
            onPageChange(target);
        }
    };

    return (
        <nav aria-label="Pagination" className="mt-8">
            <ul className="flex flex-wrap items-center justify-center gap-2">
                <li>
                    {page > 1 ? (
                        <a href={hrefFor(page - 1)} onClick={go(page - 1)} rel="prev" aria-label="Previous page" className={`${base} border-[#D5DAE1] bg-white text-[#152B4D] hover:border-[#325A96] hover:text-[#325A96]`}>
                            <ChevronLeft className="h-4 w-4" aria-hidden="true" />
                        </a>
                    ) : (
                        <span aria-hidden="true" className={`${base} cursor-not-allowed border-[#E4E7EC] bg-[#F4F5F7] text-[#B0B7C3]`}>
                            <ChevronLeft className="h-4 w-4" />
                        </span>
                    )}
                </li>

                {pageItems(page, pageCount).map((item, index) =>
                    item === 'gap' ? (
                        <li key={`gap-${index}`} aria-hidden="true" className="px-1 text-[#9AA3AF]">
                            …
                        </li>
                    ) : (
                        <li key={item}>
                            <a
                                href={hrefFor(item)}
                                onClick={go(item)}
                                aria-label={`Page ${item}`}
                                aria-current={item === page ? 'page' : undefined}
                                className={`${base} ${
                                    item === page
                                        ? 'border-[#325A96] bg-[#325A96] text-white'
                                        : 'border-[#D5DAE1] bg-white text-[#152B4D] hover:border-[#325A96] hover:text-[#325A96]'
                                }`}
                            >
                                {item}
                            </a>
                        </li>
                    ),
                )}

                <li>
                    {page < pageCount ? (
                        <a href={hrefFor(page + 1)} onClick={go(page + 1)} rel="next" aria-label="Next page" className={`${base} border-[#D5DAE1] bg-white text-[#152B4D] hover:border-[#325A96] hover:text-[#325A96]`}>
                            <ChevronRight className="h-4 w-4" aria-hidden="true" />
                        </a>
                    ) : (
                        <span aria-hidden="true" className={`${base} cursor-not-allowed border-[#E4E7EC] bg-[#F4F5F7] text-[#B0B7C3]`}>
                            <ChevronRight className="h-4 w-4" />
                        </span>
                    )}
                </li>
            </ul>
        </nav>
    );
}
