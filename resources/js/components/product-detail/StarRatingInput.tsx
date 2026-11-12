import { Star } from 'lucide-react';
import { useId, useState } from 'react';

interface StarRatingInputProps {
    /** 0 = nothing chosen yet. */
    value: number;
    onChange: (rating: number) => void;
    /** Error text; marks the group invalid and is announced with it. */
    error?: string;
}

const WORDS = ['', 'Poor', 'Fair', 'Good', 'Very good', 'Excellent'];

/**
 * Clickable 1 to 5 star rating. Built on real radio inputs: arrow keys move
 * between stars, a screen reader announces "3 stars, radio button, 3 of 5",
 * and the stars are just the visual. Hovering previews the rating before
 * you click it.
 */
export function StarRatingInput({ value, onChange, error }: StarRatingInputProps) {
    const name = useId();
    const [hover, setHover] = useState(0);
    const shown = hover || value;

    return (
        <div>
            <div
                role="radiogroup"
                aria-label="Your rating"
                aria-invalid={error ? true : undefined}
                aria-describedby={error ? `${name}-error` : undefined}
                onMouseLeave={() => setHover(0)}
                className="flex items-center gap-1"
            >
                {[1, 2, 3, 4, 5].map((n) => (
                    <label
                        key={n}
                        onMouseEnter={() => setHover(n)}
                        className="cursor-pointer rounded-md p-0.5 focus-within:outline focus-within:outline-2 focus-within:outline-offset-1 focus-within:outline-[#325A96]"
                    >
                        <input
                            type="radio"
                            name={name}
                            value={n}
                            checked={value === n}
                            onChange={() => onChange(n)}
                            aria-label={`${n} star${n === 1 ? '' : 's'}, ${WORDS[n]}`}
                            className="sr-only"
                        />
                        <Star
                            aria-hidden="true"
                            strokeWidth={1.8}
                            className={`h-8 w-8 transition-transform duration-150 hover:scale-110 motion-reduce:transition-none ${
                                n <= shown ? 'fill-[#FAB40A] text-[#FAB40A]' : 'fill-transparent text-[#C8CED8]'
                            }`}
                        />
                    </label>
                ))}
                <span className="ml-2 min-w-[5.5rem] text-[13px] font-bold text-[#152B4D]" aria-hidden="true">
                    {shown ? WORDS[shown] : 'Select a rating'}
                </span>
            </div>
            {error && (
                <p id={`${name}-error`} className="mt-1.5 text-[12px] font-semibold text-[#C0292B]">
                    {error}
                </p>
            )}
        </div>
    );
}
