import { ChevronRight } from 'lucide-react';
import { isPlainLeftClick } from '@/lib/links';
import type { Crumb } from '@/lib/shop-category';

interface ShopBreadcrumbProps {
    crumbs: Crumb[];
    /** Called for shop-internal crumbs on a plain click, with the crumb's href. Return nothing to let the browser navigate. */
    onNavigate?: (href: string) => void;
}

/** Home / Shop / Laptops / Dell Laptops / Latitude Series. The last item is the current page and not a link. */
export function ShopBreadcrumb({ crumbs, onNavigate }: ShopBreadcrumbProps) {
    return (
        <nav aria-label="Breadcrumb">
            <ol className="flex flex-wrap items-center gap-x-1.5 gap-y-1 text-[12.5px] text-[#5B6470]">
                {crumbs.map((crumb, index) => {
                    const isLast = index === crumbs.length - 1;
                    return (
                        <li key={crumb.href} className="flex items-center gap-1.5">
                            {index > 0 && <ChevronRight className="h-3.5 w-3.5 shrink-0 text-[#9AA3AF]" aria-hidden="true" />}
                            {isLast ? (
                                <span aria-current="page" className="font-bold text-[#152B4D]">
                                    {crumb.label}
                                </span>
                            ) : (
                                <a
                                    href={crumb.href}
                                    onClick={(event) => {
                                        if (onNavigate && crumb.href.startsWith('/shop') && isPlainLeftClick(event)) {
                                            event.preventDefault();
                                            onNavigate(crumb.href);
                                        }
                                    }}
                                    className="font-medium transition-colors hover:text-[#325A96] hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]"
                                >
                                    {crumb.label}
                                </a>
                            )}
                        </li>
                    );
                })}
            </ol>
        </nav>
    );
}
