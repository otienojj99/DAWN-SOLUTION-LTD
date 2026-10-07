import type { UtilityLink } from '@/types/navigation';

interface UtilityLinksProps {
    links: UtilityLink[];
    /** Right-side promo links render a divider between items; left-side don't need it. */
    divided?: boolean;
}


export function UtilityLinks({ links, divided = false }: UtilityLinksProps) {
     return (
        <ul className="flex items-center overflow-x-auto scrollbar-none">
            {links.map((link, index) => (
                <li
                    key={link.href}
                    className={divided && index !== links.length - 1 ? 'border-r border-white/15' : ''}
                >
                    <a
                        href={link.href}
                        className={[
                            'relative inline-block whitespace-nowrap px-3.5 py-2 text-xs font-medium transition-colors duration-200',
                            link.emphasis ? 'text-[#FAB40A]' : 'text-white/85 hover:text-[#FAB40A]',
                            // underline-on-hover indicator
                            'after:absolute after:bottom-1 after:left-3.5 after:right-3.5 after:h-px after:origin-left',
                            'after:scale-x-0 after:bg-[#FAB40A] after:transition-transform after:duration-200',
                            'hover:after:scale-x-100 motion-reduce:after:transition-none',
                        ].join(' ')}
                    >
                        {link.emphasis && (
                            <span className="mr-1.5 inline-block h-1.5 w-1.5 rounded-full bg-[#FAB40A] align-middle" aria-hidden="true" />
                        )}
                        {link.label}
                    </a>
                </li>
            ))}
        </ul>
    );
}