import { ChevronDown } from 'lucide-react';
import type { InputHTMLAttributes, ReactNode, SelectHTMLAttributes, TextareaHTMLAttributes } from 'react';

interface FieldProps {
    /** Used for the label's `htmlFor` and to build the error/hint ids. Must be unique on the page. */
    id: string;
    label: string;
    /** Error text. Shown under the field and wired to it with aria-describedby / aria-invalid. */
    error?: string;
    /** Helper text, hidden while an error is showing. */
    hint?: string;
    /** Adds "(optional)" after the label. Fields without it are treated as required. */
    optional?: boolean;
}

const controlClasses = (error?: string) =>
    `w-full rounded-lg border-[1.5px] bg-white px-3.5 py-2.5 text-[14px] text-[#2B2B2B] outline-none transition-colors duration-150 placeholder:text-[#8A93A3] focus:border-[#325A96] focus:ring-2 focus:ring-[#325A96]/20 ${
        error ? 'border-[#C0292B]' : 'border-[#D5DAE1]'
    }`;

function describedBy({ id, error, hint }: Pick<FieldProps, 'id' | 'error' | 'hint'>): string | undefined {
    if (error) return `${id}-error`;
    if (hint) return `${id}-hint`;
    return undefined;
}

function Shell({ id, label, error, hint, optional, children }: FieldProps & { children: ReactNode }) {
    return (
        <div>
            <label htmlFor={id} className="mb-1.5 block text-[12.5px] font-bold text-[#2B2B2B]">
                {label}
                {optional && <span className="ml-1 font-medium text-[#5B6470]">(optional)</span>}
            </label>
            {children}
            {error ? (
                <p id={`${id}-error`} className="mt-1.5 text-[12px] font-semibold text-[#C0292B]">
                    {error}
                </p>
            ) : (
                hint && (
                    <p id={`${id}-hint`} className="mt-1.5 text-[11.5px] text-[#5B6470]">
                        {hint}
                    </p>
                )
            )}
        </div>
    );
}

type TextFieldProps = FieldProps & Omit<InputHTMLAttributes<HTMLInputElement>, 'id' | 'className'>;

/** Labelled text input (text, email, tel...). Pass `name`, `value`, `onChange`, `autoComplete` etc. straight through. */
export function TextField({ id, label, error, hint, optional, ...input }: TextFieldProps) {
    return (
        <Shell id={id} label={label} error={error} hint={hint} optional={optional}>
            <input
                id={id}
                aria-invalid={error ? true : undefined}
                aria-describedby={describedBy({ id, error, hint })}
                className={controlClasses(error)}
                {...input}
            />
        </Shell>
    );
}

type SelectFieldProps = FieldProps &
    Omit<SelectHTMLAttributes<HTMLSelectElement>, 'id' | 'className' | 'children'> & {
        /** Shown (greyed, not selectable) until the person picks something. */
        placeholder: string;
        options: string[];
    };

/** Labelled native <select> with a custom chevron. Native, so mobile keyboards and screen readers get their own picker. */
export function SelectField({ id, label, error, hint, optional, placeholder, options, ...select }: SelectFieldProps) {
    const empty = select.value === '';

    return (
        <Shell id={id} label={label} error={error} hint={hint} optional={optional}>
            <div className="relative">
                <select
                    id={id}
                    aria-invalid={error ? true : undefined}
                    aria-describedby={describedBy({ id, error, hint })}
                    className={`${controlClasses(error)} appearance-none pr-10 ${empty ? 'text-[#8A93A3]' : ''}`}
                    {...select}
                >
                    <option value="" disabled>
                        {placeholder}
                    </option>
                    {options.map((option) => (
                        <option key={option} value={option} className="text-[#2B2B2B]">
                            {option}
                        </option>
                    ))}
                </select>
                <ChevronDown
                    className="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#5B6470]"
                    aria-hidden="true"
                />
            </div>
        </Shell>
    );
}

type TextAreaFieldProps = FieldProps & Omit<TextareaHTMLAttributes<HTMLTextAreaElement>, 'id' | 'className'>;

/** Labelled multi-line input. */
export function TextAreaField({ id, label, error, hint, optional, ...textarea }: TextAreaFieldProps) {
    return (
        <Shell id={id} label={label} error={error} hint={hint} optional={optional}>
            <textarea
                id={id}
                rows={4}
                aria-invalid={error ? true : undefined}
                aria-describedby={describedBy({ id, error, hint })}
                className={`${controlClasses(error)} resize-y`}
                {...textarea}
            />
        </Shell>
    );
}
