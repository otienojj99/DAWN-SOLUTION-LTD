import { Check } from 'lucide-react';
import { useEffect, useRef } from 'react';

interface SuccessMessageProps {
    title: string;
    message: string;
    /** Label for the button that returns to the form, e.g. "Send another request". */
    actionLabel: string;
    onAction: () => void;
    /** `dark` for use on the navy panel, `light` for the grey one. */
    tone: 'dark' | 'light';
}

export function SuccessMessage ({title, message, actionLabel, onAction, tone}: SuccessMessageProps){
    const titleRef = useRef<HTMLHeadingElement>(null);

    useEffect(() => {
         titleRef.current?.focus();
    }, []);

    const dark = tone === 'dark';



    return (
        <div role="status" className="py-2">
            <span
                className={`mb-4 flex h-12 w-12 items-center justify-center rounded-full ${
                    dark ? 'bg-[#FAB40A]/20 text-[#FAB40A]' : 'bg-[#1E9E5A]/15 text-[#1E9E5A]'
                }`}
            >
                <Check className="h-6 w-6" strokeWidth={2.5} aria-hidden="true" />
            </span>
            <h3 ref={titleRef} tabIndex={-1} className={`mb-2 text-xl font-extrabold outline-none ${dark ? 'text-white' : 'text-[#152B4D]'}`}>
                {title}
            </h3>
            <p className={`mb-5 max-w-[380px] text-[13.5px] leading-relaxed ${dark ? 'text-white/75' : 'text-[#5B6470]'}`}>{message}</p>
            <button
                type="button"
                onClick={onAction}
                className={`text-[13px] font-bold underline-offset-4 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 ${
                    dark ? 'text-[#FAB40A] focus-visible:outline-white' : 'text-[#325A96] focus-visible:outline-[#325A96]'
                }`}
            >
                {actionLabel}
            </button>
        </div>
    );
}