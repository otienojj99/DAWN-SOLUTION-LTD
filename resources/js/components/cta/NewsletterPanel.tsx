import { Check, Mail } from 'lucide-react';
import { useRef, useState } from 'react';
import type { FormEvent } from 'react';
import { newsletterCopy } from '@/data/cta';
import { useSubmitStatus } from '@/hooks/useSubmitStatus';
import { validateNewsletter } from '@/lib/validation';
import { SuccessMessage } from './SuccessMessage';


interface NewsletterPanelProps {
    onSubscribe?: (email: string) => Promise<void>;
}

export function NewsletterPanel({ onSubscribe }: NewsletterPanelProps) {
    const [email, setEmail] = useState('');
    const [error, setError] = useState<string | undefined>();
    const inputRef = useRef<HTMLInputElement>(null);
    const { status, message, submit, reset, isSubmitting } = useSubmitStatus();

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        if (isSubmitting) return;

        const errors = validateNewsletter({ email });

        setError(errors.email);
        if (errors.email) {
            inputRef.current?.focus();
            return;
        }

        void submit(async () => {
            if (!onSubscribe) throw new Error('Newsletter signup is not connected yet.');
            await onSubscribe(email.trim());
        });
    };

    const handleReset = () => {
        setEmail('');
        setError(undefined);
        reset();
    };

    const shownError = error ?? (status === 'error' ? message ?? undefined : undefined);

    return (
        <section aria-labelledby="newsletter-title" className="bg-gradient-to-br from-[#152B4D] to-[#325A96] p-7 text-white sm:p-10">
            <span className="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#FAB40A] text-[#152B4D]">
                <Mail className="h-6 w-6" aria-hidden="true" />
            </span>

            {status === 'success' ? (
                <SuccessMessage
                    tone="dark"
                    title={newsletterCopy.successTitle}
                    message={newsletterCopy.successMessage}
                    actionLabel="Use a different email"
                    onAction={handleReset}
                />
            ) : (
                <>
                    <h2 id="newsletter-title" className="mb-2 text-2xl font-extrabold leading-tight sm:text-[28px]">
                        {newsletterCopy.title}
                    </h2>
                    <p className="mb-6 max-w-[440px] text-[14px] leading-relaxed text-white/80">{newsletterCopy.description}</p>

                    <form onSubmit={handleSubmit} noValidate>
                        <label htmlFor="newsletter-email" className="sr-only">
                            {newsletterCopy.inputLabel}
                        </label>
                        <div className="flex flex-col gap-2.5 sm:flex-row">
                            <input
                                ref={inputRef}
                                id="newsletter-email"
                                name="email"
                                type="email"
                                inputMode="email"
                                autoComplete="email"
                                value={email}
                                onChange={(e) => {
                                    setEmail(e.target.value);
                                    if (error) setError(undefined);
                                }}
                                placeholder={newsletterCopy.placeholder}
                                aria-invalid={shownError ? true : undefined}
                                aria-describedby={shownError ? 'newsletter-error' : 'newsletter-consent'}
                                className="min-w-0 flex-1 rounded-lg border-[1.5px] border-transparent bg-white px-4 py-3 text-[14px] text-[#2B2B2B] outline-none placeholder:text-[#8A93A3] focus:border-[#FAB40A] focus:ring-2 focus:ring-[#FAB40A]/40"
                            />
                            <button
                                type="submit"
                                disabled={isSubmitting}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-[#FAB40A] px-6 py-3 text-[14px] font-extrabold text-[#152B4D] transition-colors hover:bg-[#ffc533] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white disabled:cursor-wait disabled:opacity-70"
                            >
                                {isSubmitting && (
                                    <span
                                        className="h-4 w-4 animate-spin rounded-full border-2 border-[#152B4D]/30 border-t-[#152B4D] motion-reduce:animate-none"
                                        aria-hidden="true"
                                    />
                                )}
                                {isSubmitting ? 'Subscribing...' : newsletterCopy.buttonLabel}
                            </button>
                        </div>

                        {shownError && (
                            <p id="newsletter-error" role="alert" className="mt-2.5 text-[13px] font-semibold text-[#FFB4B4]">
                                {shownError}
                            </p>
                        )}
                    </form>

                    <p id="newsletter-consent" className="mt-3 text-[11.5px] text-white/60">
                        {newsletterCopy.consentNote}
                    </p>

                    <ul className="mt-6 flex flex-wrap gap-x-5 gap-y-2">
                        {newsletterCopy.perks.map((perk) => (
                            <li key={perk} className="flex items-center gap-1.5 text-[12.5px] font-semibold text-white/85">
                                <Check className="h-4 w-4 text-[#FAB40A]" strokeWidth={2.5} aria-hidden="true" />
                                {perk}
                            </li>
                        ))}
                    </ul>
                </>
            )}
        </section>
    );
}