import { ChevronDown } from 'lucide-react';
import { sortOptions } from '@/data/shop-config';
import type { SortKey } from '@/types/shop';

interface SortSelectProps {
    value: SortKey;
    onChange: (sort: SortKey) => void;
}

/** Native select so phones show their own picker and screen readers announce it for free. */
export function SortSelect({ value, onChange }: SortSelectProps) {
    return (
        <div className="flex items-center gap-2">
            <label htmlFor="shop-sort" className="whitespace-nowrap text-[13px] font-semibold text-[#5B6470]">
                Sort by
            </label>
            <div className="relative">
                <select
                    id="shop-sort"
                    value={value}
                    onChange={(e) => onChange(e.target.value as SortKey)}
                    className="appearance-none rounded-lg border-[1.5px] border-[#D5DAE1] bg-white py-2 pl-3 pr-9 text-[13.5px] font-semibold text-[#152B4D] outline-none focus:border-[#325A96] focus:ring-2 focus:ring-[#325A96]/20"
                >
                    {sortOptions.map((option) => (
                        <option key={option.key} value={option.key}>
                            {option.label}
                        </option>
                    ))}
                </select>
                <ChevronDown className="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#5B6470]" aria-hidden="true" />
            </div>
        </div>
    );
}
