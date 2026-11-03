import { X } from 'lucide-react';
import { formatPrice } from '@/lib/format';
import type { ShopQuery } from '@/types/shop';
import { facetAttributes } from '@/data/shop-config';

interface ActiveFiltersProps {
    query: ShopQuery;
    onChange: (patch: Partial<ShopQuery>) => void;
    onClearAll: () => void;
}

interface Chip {
    id: string;
    label: string;
    remove: () => void;
}

function priceLabel(min?: number, max?: number): string {
    if (min !== undefined && max !== undefined) return `${formatPrice(min)} – ${formatPrice(max)}`;
    if (min !== undefined) return `From ${formatPrice(min)}`;
    return `Up to ${formatPrice(max ?? 0)}`;
}

/** Removable chips for every applied filter, plus "Clear all". Renders nothing when no filter is applied. */
export function ActiveFilters({ query, onChange, onClearAll }: ActiveFiltersProps) {
    const chips: Chip[] = [];

    query.brands.forEach((brand) =>
        chips.push({ id: `brand-${brand}`, label: brand, remove: () => onChange({ brands: query.brands.filter((b) => b !== brand) }) }),
    );

    for (const attr of facetAttributes) {
        (query.attrs[attr.key] ?? []).forEach((value) =>
            chips.push({
                id: `${attr.key}-${value}`,
                label: `${attr.label}: ${value}`,
                remove: () => onChange({ attrs: { ...query.attrs, [attr.key]: (query.attrs[attr.key] ?? []).filter((v) => v !== value) } }),
            }),
        );
    }

    if (query.minPrice !== undefined || query.maxPrice !== undefined) {
        chips.push({ id: 'price', label: priceLabel(query.minPrice, query.maxPrice), remove: () => onChange({ minPrice: undefined, maxPrice: undefined }) });
    }
    if (query.inStock) chips.push({ id: 'stock', label: 'In stock', remove: () => onChange({ inStock: false }) });
    if (query.onSale) chips.push({ id: 'sale', label: 'On sale', remove: () => onChange({ onSale: false }) });
    if (query.minRating !== undefined) chips.push({ id: 'rating', label: `${query.minRating}.0 stars & up`, remove: () => onChange({ minRating: undefined }) });

    if (chips.length === 0) return null;

    return (
        <ul aria-label="Applied filters" className="flex flex-wrap items-center gap-2">
            {chips.map((chip) => (
                <li key={chip.id}>
                    <button
                        type="button"
                        onClick={chip.remove}
                        aria-label={`Remove filter: ${chip.label}`}
                        className="inline-flex items-center gap-1.5 rounded-full border border-[#325A96]/30 bg-[#EAF0F9] py-1 pl-3 pr-2 text-[12.5px] font-semibold text-[#152B4D] transition-colors hover:border-[#325A96] hover:bg-[#dbe6f6] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]"
                    >
                        {chip.label}
                        <X className="h-3.5 w-3.5" aria-hidden="true" />
                    </button>
                </li>
            ))}
            <li>
                <button
                    type="button"
                    onClick={onClearAll}
                    className="px-2 py-1 text-[12.5px] font-bold text-[#325A96] underline-offset-4 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]"
                >
                    Clear all
                </button>
            </li>
        </ul>
    );
}
