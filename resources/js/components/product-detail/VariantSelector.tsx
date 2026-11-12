import { useId } from 'react';
import { formatPrice } from '@/lib/format';
import type { VariantGroup } from '@/types/product-detail';

interface VariantSelectorProps {
    group: VariantGroup;
    value: string;
    onChange: (value: string) => void;
}

/**
 * One variant group (RAM, storage, colour...) as a radio group. Uses real
 * radio inputs, so arrow-key navigation, grouping and screen-reader
 * announcements are the browser's own; the chips are just styling.
 */
export function VariantSelector({ group, value, onChange }: VariantSelectorProps) {
    const name = useId();
    const labelId = `${name}-label`;
    const selected = group.options.find((o) => o.value === value);

    return (
        <div role="radiogroup" aria-labelledby={labelId}>
            <p id={labelId} className="mb-2 text-[13px] font-bold text-[#2B2B2B]">
                {group.label}: <span className="text-[#325A96]">{selected?.label ?? selected?.value ?? value}</span>
            </p>
            <div className="flex flex-wrap gap-2">
                {group.options.map((option) => {
                    const delta = option.priceDelta ?? 0;
                    return (
                        <label key={option.value} className={option.unavailable ? 'cursor-not-allowed' : 'cursor-pointer'}>
                            <input
                                type="radio"
                                name={name}
                                value={option.value}
                                checked={option.value === value}
                                disabled={option.unavailable}
                                onChange={() => onChange(option.value)}
                                className="peer sr-only"
                            />
                            <span
                                className={`flex items-center gap-2 rounded-lg border-[1.5px] px-3.5 py-2 text-[13px] font-semibold transition-colors peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[#325A96] peer-checked:border-[#325A96] peer-checked:bg-[#EAF0F9] peer-checked:text-[#152B4D] ${
                                    option.unavailable
                                        ? 'border-[#E4E7EC] bg-[#F4F5F7] text-[#B0B7C3] line-through'
                                        : 'border-[#D5DAE1] bg-white text-[#2B2B2B] hover:border-[#325A96]'
                                }`}
                            >
                                {option.swatch && (
                                    <span className="h-3.5 w-3.5 rounded-full border border-black/15" style={{ background: option.swatch }} aria-hidden="true" />
                                )}
                                {option.label ?? option.value}
                                {delta !== 0 && !option.unavailable && (
                                    <span className="text-[11.5px] font-medium text-[#5B6470]">
                                        {delta > 0 ? '+' : '−'}
                                        {formatPrice(Math.abs(delta))}
                                    </span>
                                )}
                                {option.unavailable && <span className="sr-only"> (unavailable)</span>}
                            </span>
                        </label>
                    );
                })}
            </div>
        </div>
    );
}
