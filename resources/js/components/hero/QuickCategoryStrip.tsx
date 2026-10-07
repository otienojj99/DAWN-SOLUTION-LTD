import { quickCategories } from '@/data/quick-categories';

/**
 * Icon-strip row directly under the hero — gives immediate navigation
 * without needing to scroll, read, or open the main category menu.
 */
export function QuickCategoryStrip() {
    return (
        <nav aria-label="Quick category links" className="relative z-10 border-b border-[#E4E7EC] bg-white shadow-[0_4px_12px_rgba(0,0,0,0.04)]">
            <ul className="mx-auto flex max-w-[1400px] justify-center">
                {quickCategories.map((category, index) => {
                    const Icon = category.icon;
                    return (
                        <li key={category.id} className="flex-1">
                            <a
                                href={category.href}
                                className={`group flex flex-col items-center gap-2 px-3 py-5 transition-colors duration-150 hover:bg-[#F4F5F7] ${
                                    index !== quickCategories.length - 1 ? 'border-r border-[#EEEEEE]' : ''
                                }`}
                            >
                                <span className="flex h-11 w-11 items-center justify-center rounded-full bg-[#EAF0F9] text-[#325A96] transition-colors duration-150 group-hover:bg-[#FAB40A] group-hover:text-[#152B4D]">
                                    <Icon className="h-5 w-5" strokeWidth={2} aria-hidden="true" />
                                </span>
                                <span className="text-[12.5px] font-bold text-[#2B2B2B]">{category.label}</span>
                            </a>
                        </li>
                    );
                })}
            </ul>
        </nav>
    );
}
