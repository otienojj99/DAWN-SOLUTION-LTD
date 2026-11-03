import { ChevronDown } from 'lucide-react';
import { useEffect, useState } from 'react';
import { isPlainLeftClick } from '@/lib/links';
import type { SidebarLink, SidebarSection } from '@/lib/shop-category';

interface CategorySidebarProps {
    sections: SidebarSection[];
    /** Called with the category path on a plain click; other clicks (new tab...) behave as normal links. */
    onNavigate: (path: string[]) => void;
}

/**
 * Category list for the shop sidebar. What it contains is decided by
 * `buildSidebar()` (lib/shop-category.ts): only series for three-level
 * categories, dropdowns for two-level ones. This component only draws it.
 */
export function CategorySidebar({ sections, onNavigate }: CategorySidebarProps) {
    return (
        <nav aria-label="Categories" className="grid gap-1">
            {sections.map((section) => (
                <Section key={section.id} section={section} onNavigate={onNavigate} />
            ))}
        </nav>
    );
}

function Section({ section, onNavigate }: { section: SidebarSection; onNavigate: (path: string[]) => void }) {
    const [open, setOpen] = useState(section.open);
    const panelId = `cat-${section.id}`;

    // Navigating into a section opens it; leaving never force-closes what the visitor opened.
    useEffect(() => {
        if (section.open) setOpen(true);
    }, [section.open]);

    const body = (
        <div id={panelId} hidden={section.collapsible && !open} className={section.collapsible ? 'pb-2 pl-1 pt-1' : ''}>
            {section.viewAll && (
                <ul className="mb-1">
                    <LinkItem link={section.viewAll} onNavigate={onNavigate} strong />
                </ul>
            )}
            {section.groups.map((group, index) => (
                <div key={group.heading ?? index} className={index > 0 ? 'mt-3' : ''}>
                    {group.heading && (
                        <p className="mb-1 px-2.5 text-[11px] font-extrabold uppercase tracking-wider text-[#9AA3AF]">{group.heading}</p>
                    )}
                    <ul>
                        {group.links.map((link) => (
                            <LinkItem key={link.href} link={link} onNavigate={onNavigate} />
                        ))}
                    </ul>
                </div>
            ))}
        </div>
    );

    if (!section.collapsible) {
        return (
            <div>
                <h2 className="mb-2 px-2.5 text-[13px] font-extrabold uppercase tracking-wider text-[#152B4D]">{section.title}</h2>
                {body}
            </div>
        );
    }

    return (
        <div className="border-b border-[#E4E7EC] last:border-b-0">
            <h2>
                <button
                    type="button"
                    aria-expanded={open}
                    aria-controls={panelId}
                    onClick={() => setOpen((v) => !v)}
                    className="flex w-full items-center justify-between rounded-md px-2.5 py-3 text-left text-[14px] font-extrabold text-[#152B4D] transition-colors hover:bg-[#F4F5F7] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#325A96]"
                >
                    {section.title}
                    <ChevronDown
                        className={`h-4 w-4 text-[#5B6470] transition-transform duration-200 motion-reduce:transition-none ${open ? 'rotate-180' : ''}`}
                        aria-hidden="true"
                    />
                </button>
            </h2>
            {body}
        </div>
    );
}

function LinkItem({ link, onNavigate, strong = false }: { link: SidebarLink; onNavigate: (path: string[]) => void; strong?: boolean }) {
    return (
        <li>
            <a
                href={link.href}
                aria-current={link.active ? 'page' : undefined}
                onClick={(event) => {
                    if (isPlainLeftClick(event)) {
                        event.preventDefault();
                        onNavigate(link.path);
                    }
                }}
                className={`flex items-center justify-between gap-2 rounded-md border-l-[3px] px-2.5 py-2 text-[13.5px] transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#325A96] ${
                    link.active
                        ? 'border-[#FAB40A] bg-[#EAF0F9] font-bold text-[#152B4D]'
                        : `border-transparent hover:bg-[#F4F5F7] hover:text-[#325A96] ${strong ? 'font-bold text-[#325A96]' : 'text-[#2B2B2B]'}`
                }`}
            >
                <span>{link.label}</span>
                <span className="text-[11.5px] font-semibold text-[#9AA3AF]">{link.count}</span>
            </a>
        </li>
    );
}
