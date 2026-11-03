import { ChevronDown, Star } from 'lucide-react';
import { useState } from 'react';
import type { FormEvent, ReactNode } from 'react';
import { ratingOptions } from '@/data/shop-config';
import { formatPrice } from '@/lib/format';
import { ratingCount } from '@/lib/shop-filter';
import type { ShopResults } from '@/lib/shop-filter';
import type { FacetGroup, ShopQuery } from '@/types/shop';

interface ShopFiltersProps {
    query: ShopQuery;
    results: ShopResults;
    onChange: (patch: Partial<ShopQuery>) => void;
}

function toggle(list: string[], value: string): string[] {
    return list.includes(value) ? list.filter((v) => v !== value) : [...list, value];
}

/** Brand, price, availability, rating and spec filters. Every change reports a patch; the parent decides how to apply it. */
export function ShopFilters({ query, results, onChange }: ShopFiltersProps) {
    return (
        <div className="grid">
            <FacetCheckboxes
                group={results.brandFacet}
                onToggle={(value) => onChange({ brands: toggle(query.brands, value) })}
            />

            <PriceFilter query={query} bounds={results.bounds} onChange={onChange} />

            <FilterGroup title="Availability">
                <Check label="In stock only" checked={query.inStock} onChange={(checked) => onChange({ inStock: checked })} />
                <Check label="On sale" checked={query.onSale} onChange={(checked) => onChange({ onSale: checked })} />
            </FilterGroup>

            <FilterGroup title="Customer rating">
                {ratingOptions.map((stars) => {
                    const count = ratingCount(results.inScope, query, stars);
                    const selected = query.minRating === stars;
                    return (
                        <Check
                            key={stars}
                            radio
                            name="rating"
                            checked={selected}
                            disabled={count === 0 && !selected}
                            onChange={() => onChange({ minRating: selected ? undefined : stars })}
                            label={
                                <span className="flex items-center gap-1">
                                    <Star className="h-3.5 w-3.5 fill-[#FAB40A] text-[#FAB40A]" aria-hidden="true" />
                                    {stars}.0 &amp; up
                                </span>
                            }
                            count={count}
                        />
                    );
                })}
            </FilterGroup>

            {results.attributeFacets.map((group) => (
                <FacetCheckboxes
                    key={group.key}
                    group={group}
                    onToggle={(value) => onChange({ attrs: { ...query.attrs, [group.key]: toggle(query.attrs[group.key] ?? [], value) } })}
                />
            ))}
        </div>
    );
}

/* ------------------------------------------------------------------ */

function FilterGroup({ title, children }: { title: string; children: ReactNode }) {
    return (
        <details open className="group border-b border-[#E4E7EC] py-3 last:border-b-0">
            <summary className="flex cursor-pointer list-none items-center justify-between rounded-md px-1 py-1 text-[13px] font-extrabold uppercase tracking-wider text-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96] [&::-webkit-details-marker]:hidden">
                {title}
                <ChevronDown className="h-4 w-4 text-[#5B6470] transition-transform duration-200 group-open:rotate-180 motion-reduce:transition-none" aria-hidden="true" />
            </summary>
            <div className="mt-2 grid gap-0.5">{children}</div>
        </details>
    );
}

function Check({
    label,
    checked,
    onChange,
    count,
    disabled,
    radio,
    name,
}: {
    label: ReactNode;
    checked: boolean;
    onChange: (checked: boolean) => void;
    count?: number;
    disabled?: boolean;
    radio?: boolean;
    name?: string;
}) {
    return (
        <label
            className={`flex items-center gap-2.5 rounded-md px-1 py-1.5 text-[13.5px] ${
                disabled ? 'cursor-not-allowed text-[#B0B7C3]' : 'cursor-pointer text-[#2B2B2B] hover:bg-[#F4F5F7]'
            }`}
        >
            <input
                type={radio ? 'radio' : 'checkbox'}
                name={name}
                checked={checked}
                disabled={disabled}
                // A radio can't be un-ticked by clicking it, so a click on the ticked one is handled here instead.
                onChange={(e) => onChange(e.target.checked)}
                onClick={radio && checked ? () => onChange(false) : undefined}
                className="h-4 w-4 shrink-0 accent-[#325A96]"
            />
            <span className="flex-1">{label}</span>
            {count !== undefined && <span className="text-[11.5px] font-semibold text-[#9AA3AF]">{count}</span>}
        </label>
    );
}

function FacetCheckboxes({ group, onToggle }: { group: FacetGroup; onToggle: (value: string) => void }) {
    if (group.options.length === 0) return null;

    return (
        <FilterGroup title={group.label}>
            {group.options.map((option) => (
                <Check
                    key={option.value}
                    label={option.value}
                    count={option.count}
                    checked={option.selected}
                    disabled={option.count === 0 && !option.selected}
                    onChange={() => onToggle(option.value)}
                />
            ))}
        </FilterGroup>
    );
}

function PriceFilter({ query, bounds, onChange }: { query: ShopQuery; bounds: ShopResults['bounds']; onChange: ShopFiltersProps['onChange'] }) {
    // Typing is local; the filter is applied on Apply / Enter so the grid doesn't jump on every keystroke.
    // Re-keyed when the applied range changes elsewhere (chip removed, Clear all, Back button).
    const appliedKey = `${query.minPrice ?? ''}-${query.maxPrice ?? ''}`;
    return <PriceForm key={appliedKey} query={query} bounds={bounds} onChange={onChange} />;
}

function PriceForm({ query, bounds, onChange }: { query: ShopQuery; bounds: ShopResults['bounds']; onChange: ShopFiltersProps['onChange'] }) {
    const [min, setMin] = useState(query.minPrice?.toString() ?? '');
    const [max, setMax] = useState(query.maxPrice?.toString() ?? '');

    function parse(value: string): number | undefined {
        const n = Number(value);
        return value.trim() !== '' && Number.isFinite(n) && n >= 0 ? Math.round(n) : undefined;
    }

    function handleSubmit(event: FormEvent) {
        event.preventDefault();
        let low = parse(min);
        let high = parse(max);
        // Someone typed the range backwards: swap rather than showing an empty shop.
        if (low !== undefined && high !== undefined && low > high) [low, high] = [high, low];
        onChange({ minPrice: low, maxPrice: high });
    }

    const applied = query.minPrice !== undefined || query.maxPrice !== undefined;
    const inputClasses =
        'w-full min-w-0 rounded-md border-[1.5px] border-[#D5DAE1] bg-white px-2.5 py-2 text-[13px] text-[#2B2B2B] outline-none placeholder:text-[#9AA3AF] focus:border-[#325A96] focus:ring-2 focus:ring-[#325A96]/20';

    return (
        <FilterGroup title="Price (KES)">
            <form onSubmit={handleSubmit} className="grid gap-2 px-1">
                <div className="flex items-center gap-2">
                    <label className="min-w-0 flex-1">
                        <span className="sr-only">Minimum price in shillings</span>
                        <input
                            type="number"
                            inputMode="numeric"
                            min={0}
                            value={min}
                            onChange={(e) => setMin(e.target.value)}
                            placeholder={bounds ? String(bounds.min) : 'Min'}
                            className={inputClasses}
                        />
                    </label>
                    <span className="text-[#9AA3AF]" aria-hidden="true">
                        –
                    </span>
                    <label className="min-w-0 flex-1">
                        <span className="sr-only">Maximum price in shillings</span>
                        <input
                            type="number"
                            inputMode="numeric"
                            min={0}
                            value={max}
                            onChange={(e) => setMax(e.target.value)}
                            placeholder={bounds ? String(bounds.max) : 'Max'}
                            className={inputClasses}
                        />
                    </label>
                </div>
                {bounds && (
                    <p className="text-[11.5px] text-[#5B6470]">
                        Here: {formatPrice(bounds.min)} to {formatPrice(bounds.max)}
                    </p>
                )}
                <div className="flex gap-2">
                    <button
                        type="submit"
                        className="flex-1 rounded-md bg-[#325A96] px-3 py-2 text-[13px] font-bold text-white transition-colors hover:bg-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]"
                    >
                        Apply
                    </button>
                    {applied && (
                        <button
                            type="button"
                            onClick={() => onChange({ minPrice: undefined, maxPrice: undefined })}
                            className="rounded-md border-[1.5px] border-[#D5DAE1] px-3 py-2 text-[13px] font-bold text-[#5B6470] transition-colors hover:border-[#325A96] hover:text-[#325A96] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]"
                        >
                            Reset
                        </button>
                    )}
                </div>
            </form>
        </FilterGroup>
    );
}
