import { Minus, Plus } from 'lucide-react';
import { useEffect, useState } from 'react';

interface QuantityStepperProps {
    value: number;
    onChange: (value: number) => void;
    min?: number;
    max?: number;
}

const clamp = (n: number, min: number, max: number) => Math.min(max, Math.max(min, n));

/** − / number / + control. The number can be typed; it is checked against min/max when the field is left or Enter is pressed. */
export function QuantityStepper({ value, onChange, min = 1, max = 99 }: QuantityStepperProps) {
    const [draft, setDraft] = useState(String(value));

    // Keep the field in step when the value changes from outside (the +/- buttons, a reset).
    useEffect(() => setDraft(String(value)), [value]);

    function commit() {
        const parsed = parseInt(draft, 10);
        const next = Number.isFinite(parsed) ? clamp(parsed, min, max) : value;
        setDraft(String(next));
        if (next !== value) onChange(next);
    }

    const button =
        'flex h-11 w-10 items-center justify-center text-[#152B4D] transition-colors hover:bg-[#EAF0F9] disabled:cursor-not-allowed disabled:text-[#B0B7C3] disabled:hover:bg-transparent focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-[#325A96]';

    return (
        <div role="group" aria-label="Quantity" className="inline-flex overflow-hidden rounded-lg border-[1.5px] border-[#D5DAE1] bg-white">
            <button type="button" onClick={() => onChange(clamp(value - 1, min, max))} disabled={value <= min} aria-label="Decrease quantity" className={button}>
                <Minus className="h-4 w-4" aria-hidden="true" />
            </button>
            <input
                type="text"
                inputMode="numeric"
                aria-label="Quantity"
                value={draft}
                onChange={(e) => setDraft(e.target.value.replace(/[^\d]/g, ''))}
                onBlur={commit}
                onKeyDown={(e) => e.key === 'Enter' && (e.preventDefault(), commit())}
                className="h-11 w-12 border-x border-[#D5DAE1] text-center text-[14px] font-bold text-[#152B4D] outline-none focus:bg-[#F4F8FD]"
            />
            <button type="button" onClick={() => onChange(clamp(value + 1, min, max))} disabled={value >= max} aria-label="Increase quantity" className={button}>
                <Plus className="h-4 w-4" aria-hidden="true" />
            </button>
        </div>
    );
}
