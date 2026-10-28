import { useState } from 'react';
import type { ChangeEvent, FormEvent } from 'react';
import { quoteCopy } from '@/data/cta';
import { SelectField, TextAreaField, TextField } from '@/components/forms';
import { useSubmitStatus } from '@/hooks/useSubmitStatus';
import { QUOTE_DETAILS_MAX, validateQuote } from '@/lib/validation';
import type { FieldErrors, QuoteRequest } from '@/types/forms';
import { SuccessMessage } from './SuccessMessage';



interface QuoteFormProps {
    /** Sends the request to your backend. Throw an Error with a readable message to show it to the visitor. */
    onRequestQuote?: (request: QuoteRequest) => Promise<void>;
}

const EMPTY: QuoteRequest = { fullName: '', phone: '', email: '', company: '', interest: '', details: '' };


export function QuoteForm ({onRequestQuote}: QuoteFormProps){
    const [values, setValues] = useState<QuoteRequest>(EMPTY);
    const [errors, setErrors] = useState<FieldErrors<QuoteRequest>>({});
    const { status, message, submit, reset, isSubmitting } = useSubmitStatus();

     const handleChange = (event: ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
        const name = event.target.name as keyof QuoteRequest;
        const next = { ...values, [name]: event.target.value };

        setValues(next);

        if (errors[name]){
            setErrors((current) => ({...current, [name]: validateQuote(next)[name] }));
        }


     };

     const handleSubmit = (event: FormEvent<HTMLFormElement>) =>{
         event.preventDefault();
        if (isSubmitting) return;

        const found = validateQuote(values);
        setErrors(found);

        const firstInvalid = Object.keys(found)[0];
        if (firstInvalid) {
            event.currentTarget.querySelector<HTMLElement>(`[name="${firstInvalid}"]`)?.focus();
            return;
        }

        const trimmed: QuoteRequest = {
            fullName: values.fullName.trim(),
            phone: values.phone.trim(),
            email: values.email.trim(),
            company: values.company.trim(),
            interest: values.interest,
            details: values.details.trim(),
        };

         void submit(async () => {
            if (!onRequestQuote) throw new Error('Quotation requests are not connected yet.');
            await onRequestQuote(trimmed);
        });
     }


     const handleReset = () => {
        setValues(EMPTY);
        setErrors({});
        reset();
     }

     if (status === 'success'){
        return (
            <SuccessMessage
                tone="light"
                title={quoteCopy.successTitle}
                message={quoteCopy.successMessage}
                actionLabel="Send another request"
                onAction={handleReset}
            />
        );
     }


      const remaining = QUOTE_DETAILS_MAX - values.details.length;


       return (
        <form onSubmit={handleSubmit} noValidate className="grid gap-4">
            <div className="grid gap-4 sm:grid-cols-2">
                <TextField id="quote-name" name="fullName" label="Full name" autoComplete="name" value={values.fullName} onChange={handleChange} error={errors.fullName} />
                <TextField id="quote-company" name="company" label="Company" optional autoComplete="organization" value={values.company} onChange={handleChange} />
                <TextField id="quote-phone" name="phone" type="tel" inputMode="tel" label="Phone" autoComplete="tel" placeholder="0712 345 678" value={values.phone} onChange={handleChange} error={errors.phone} />
                <TextField id="quote-email" name="email" type="email" inputMode="email" label="Email" autoComplete="email" placeholder="name@company.com" value={values.email} onChange={handleChange} error={errors.email} />
            </div>

            <SelectField
                id="quote-interest"
                name="interest"
                label="I need a quotation for"
                placeholder={quoteCopy.interestPlaceholder}
                options={quoteCopy.interestOptions}
                value={values.interest}
                onChange={handleChange}
                error={errors.interest}
            />

            <TextAreaField
                id="quote-details"
                name="details"
                label="Details"
                optional
                placeholder={quoteCopy.detailsPlaceholder}
                value={values.details}
                onChange={handleChange}
                error={errors.details}
                hint={remaining <= 200 ? `${remaining} characters left` : undefined}
            />

            {status === 'error' && message && (
                <p role="alert" className="rounded-lg border border-[#C0292B]/30 bg-[#C0292B]/10 px-3.5 py-2.5 text-[13px] font-semibold text-[#C0292B]">
                    {message}
                </p>
            )}

            <button
                type="submit"
                disabled={isSubmitting}
                className="inline-flex items-center justify-center gap-2 rounded-lg bg-[#325A96] px-6 py-3 text-[14px] font-extrabold text-white transition-colors hover:bg-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96] disabled:cursor-wait disabled:opacity-70"
            >
                {isSubmitting && (
                    <span className="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white motion-reduce:animate-none" aria-hidden="true" />
                )}
                {isSubmitting ? 'Sending...' : quoteCopy.buttonLabel}
            </button>
        </form>
    );

     
}