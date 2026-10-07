import type { CategoryColumn } from '@/types/navigation';

interface CategoryMenuProps {
    columns: CategoryColumn[];
}


export function CategoryMenu( {columns}: CategoryMenuProps){
     return (
        <div
            role="menu"
            className="absolute left-0 top-full z-50 flex min-w-[640px] gap-9 rounded-b-lg border-t-[3px] border-[#FAB40A] bg-white px-9 py-7 shadow-[0_16px_32px_rgba(30,58,102,0.18)]"
        >
            {columns.map((column) => (
                <div key={column.heading} className="min-w-[150px]">
                    <h3 className="mb-3 border-b-2 border-[#E4E7EC] pb-2 text-[13px] font-bold uppercase tracking-wide text-[#325A96]">
                        {column.heading}
                    </h3>
                    <ul className="space-y-2.5">
                        {column.links.map((link) => (
                            <li key={link.href}>
                                <a
                                    href={link.href}
                                    role="menuitem"
                                    className="group flex items-center gap-1.5 text-[13.5px] text-[#2B2B2B] transition-colors duration-150 hover:font-semibold hover:text-[#DE9E00]"
                                >
                                    {link.label}
                                    {link.badge && (
                                        <span className="rounded bg-[#FAB40A] px-1.5 py-px text-[9px] font-extrabold uppercase tracking-wide text-[#152B4D]">
                                            {link.badge}
                                        </span>
                                    )}
                                </a>
                            </li>
                        ))}
                    </ul>
                </div>
            ))}
        </div>
    );
}