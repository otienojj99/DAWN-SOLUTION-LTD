import type { Category } from '@/types/navigation';
import { CategoryMenu } from './CategoryMenu';

interface CategoryNavigationProps {
    categories: Category[];
}

/**
 * Desktop category bar. Each item with `columns` opens a CategoryMenu on
 * hover (mouse) or focus-within (keyboard) — no JS state needed per item,
 * which keeps this scaling to any number of categories without perf cost.
 * Hidden below `md`; MobileNavigation covers small screens instead.
 */
export function CategoryNavigation({ categories }: CategoryNavigationProps) {
    return (
        <nav aria-label="Product categories" className="hidden bg-[#325A96] md:block">
            <ul className="mx-auto flex max-w-[1400px] px-4 md:px-8">
                {categories.map((category) => (
                    <li key={category.id} className="group relative">
                        <a
                            href={category.href}
                            className={[
                                'block whitespace-nowrap px-4 py-3.5 text-sm font-semibold tracking-wide transition-colors duration-150',
                                category.isPrimary
                                    ? 'bg-[#FAB40A] text-[#152B4D] hover:bg-[#DE9E00]'
                                    : 'text-white hover:bg-[#28497D] hover:text-[#FAB40A] group-focus-within:bg-[#28497D] group-focus-within:text-[#FAB40A]',
                            ].join(' ')}
                        >
                            {category.isPrimary ? '☰ ' : ''}
                            {category.label}
                        </a>

                        {category.columns && (
                            <div className="hidden group-hover:block group-focus-within:block">
                                <CategoryMenu columns={category.columns} />
                            </div>
                        )}
                    </li>
                ))}
            </ul>
        </nav>
    );
}
