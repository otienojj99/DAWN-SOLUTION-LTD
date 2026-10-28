/** Where a form is in its lifecycle. */
export type SubmitStatus = 'idle' | 'submitting' | 'success' | 'error';

/** One error message per field name, only for the fields that have a problem. */
export type FieldErrors<T> = Partial<Record<keyof T, string>>;

export interface NewsletterSignup {
    email: string;
}

/** What the "Request a Quotation" form sends. Field names match what the backend should validate. */
export interface QuoteRequest {
    fullName: string;
    phone: string;
    email: string;
    /** Optional: organisation the quote is for. */
    company: string;
    /** What they want quoted, one of the `quoteCopy.interestOptions` values. */
    interest: string;
    /** Optional free text: quantity, budget, timeline. */
    details: string;
}
