import { ChevronRight, X } from 'lucide-react';
import { useEffect, useState } from 'react';
import type { Category } from '@/types/navigation';
import { utilityLinks } from '@/data/utility-links';
import { promotionalLinks } from '@/data/promotional-links';

interface MobileNavigationProps {
    categories: Category[];
    open: boolean;
    onClose: () => void;
}

function AccordionCategory({ category }: { category: Category }) {
    const [expanded, setExpanded] = useState(false);
    const hasColumns = Boolean(category.columns && category.columns.length > 0);
    const panelId = `mobile-cat-${category.id}`;

    if (!hasColumns) {
        return (
            <a
                href={category.href}
                className="block border-b border-[#E4E7EC] px-4 py-3.5 text-sm font-bold text-[#2B2B2B]"
            >
                {category.label}
            </a>
        );
    }

    return (
        <div className="border-b border-[#E4E7EC]">
            <button
                type="button"
                onClick={() => setExpanded((v) => !v)}
                aria-expanded={expanded}
                aria-controls={panelId}
                className="flex w-full items-center justify-between px-4 py-3.5 text-left text-sm font-bold text-[#2B2B2B]"
            >
                {category.label}
                <ChevronRight
                    className={`h-4 w-4 text-[#325A96] transition-transform duration-200 ${expanded ? 'rotate-90' : ''}`}
                    aria-hidden="true"
                />
            </button>

            <div
                id={panelId}
                className="grid overflow-hidden bg-[#F4F5F7] transition-[grid-template-rows] duration-250 ease-out"
                style={{ gridTemplateRows: expanded ? '1fr' : '0fr' }}
            >
                <div className="min-h-0 overflow-hidden">
                    {category.columns!.map((column) => (
                        <div key={column.heading} className="px-4 pt-3">
                            <h4 className="mb-1.5 mt-2 text-[11px] font-bold uppercase tracking-wide text-[#5B6470]">
                                {column.heading}
                            </h4>
                            <ul>
                                {column.links.map((link) => (
                                    <li key={link.href}>
                                        <a href={link.href} className="block py-2 text-[13.5px] text-[#2B2B2B] active:text-[#DE9E00]">
                                            {link.label}
                                        </a>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                    <div className="h-3" />
                </div>
            </div>
        </div>
    );
}

/**
 * Slide-in mobile drawer. Reuses the exact same `categories` data as the
 * desktop CategoryNavigation — only the presentation differs (accordion vs
 * hover mega menu), so the two never drift out of sync.
 */
export function MobileNavigation({ categories, open, onClose }: MobileNavigationProps) {
    // Lock body scroll while the drawer is open.
    useEffect(() => {
        if (open) {
            const original = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
            return () => {
                document.body.style.overflow = original;
            };
        }
    }, [open]);

    // Close on Escape.
    useEffect(() => {
        if (!open) return;
        function handleKeyDown(event: KeyboardEvent) {
            if (event.key === 'Escape') onClose();
        }
        document.addEventListener('keydown', handleKeyDown);
        return () => document.removeEventListener('keydown', handleKeyDown);
    }, [open, onClose]);

    return (
        <div className={`fixed inset-0 z-[100] md:hidden ${open ? '' : 'pointer-events-none'}`} aria-hidden={!open}>
            {/* Overlay */}
            <div
                onClick={onClose}
                className={`absolute inset-0 bg-[#152B4D]/45 transition-opacity duration-250 motion-reduce:transition-none ${
                    open ? 'opacity-100' : 'opacity-0'
                }`}
            />

            {/* Drawer */}
            <nav
                aria-label="Mobile navigation"
                className={`absolute left-0 top-0 h-full w-[85%] max-w-[340px] overflow-y-auto bg-white shadow-[8px_0_30px_rgba(0,0,0,0.2)] transition-transform duration-300 ease-out motion-reduce:transition-none ${
                    open ? 'translate-x-0' : '-translate-x-full'
                }`}
            >
                <div className="flex items-center justify-between bg-[#325A96] px-4 py-4">
                    <span className="text-base font-extrabold text-white">☰ All Categories</span>
                    <button
                        type="button"
                        onClick={onClose}
                        aria-label="Close menu"
                        className="flex h-9 w-9 items-center justify-center rounded-full text-white hover:bg-white/10"
                    >
                        <X className="h-5 w-5" aria-hidden="true" />
                    </button>
                </div>

                <div>
                    {categories.map((category) => (
                        <AccordionCategory key={category.id} category={category} />
                    ))}
                </div>

                <div className="border-t border-[#E4E7EC] px-4 py-3">
                    <h4 className="mb-1 mt-2 text-[11px] font-bold uppercase tracking-wide text-[#5B6470]">Shop For</h4>
                    <ul className="grid grid-cols-2 gap-1">
                        {utilityLinks.map((link) => (
                            <li key={link.href}>
                                <a href={link.href} className="block py-1.5 text-[13px] text-[#2B2B2B]">
                                    {link.label}
                                </a>
                            </li>
                        ))}
                    </ul>
                </div>

                <div className="border-t border-[#E4E7EC] px-4 py-3">
                    <h4 className="mb-1 mt-2 text-[11px] font-bold uppercase tracking-wide text-[#5B6470]">Offers</h4>
                    <ul className="grid grid-cols-2 gap-1">
                        {promotionalLinks.map((link) => (
                            <li key={link.href}>
                                <a href={link.href} className="block py-1.5 text-[13px] font-semibold text-[#DE9E00]">
                                    {link.label}
                                </a>
                            </li>
                        ))}
                    </ul>
                </div>

                <div className="flex gap-2.5 border-t border-[#E4E7EC] p-4">
                    <a href="/login" className="flex-1 rounded-md border-2 border-[#325A96] py-2.5 text-center text-[12.5px] font-bold text-[#325A96]">
                        Sign In
                    </a>
                    <a href="/register" className="flex-1 rounded-md bg-[#FAB40A] py-2.5 text-center text-[12.5px] font-bold text-[#152B4D]">
                        Register
                    </a>
                </div>
            </nav>
        </div>
    );
}
