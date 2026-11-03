import type { FieldErrors, NewsletterSignup, QuoteRequest } from '@/types/forms';

const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

/**
 * These checks catch typos before a request is sent; they are not a
 * substitute for validating again on the server (Laravel Form Requests),
 * which must be the source of truth.
 */

export function emailError(value: string): string | undefined {
    const email = value.trim();
    if (!email) return 'Enter your email address.';
    if (!EMAIL_PATTERN.test(email)) return 'Enter a valid email address, like name@company.com.';
    return undefined;
}

/** Accepts local (0712 345 678) and international (+254 712 345 678) numbers; ignores spaces, dashes, dots and brackets. */
export function phoneError(value: string): string | undefined {
    const phone = value.trim();
    if (!phone) return 'Enter a phone number we can reach you on.';
    const digits = phone.replace(/[\s\-().]/g, '').replace(/^\+/, '');
    if (!/^\d{9,15}$/.test(digits)) return 'Enter a valid phone number, like 0712 345 678 or +254 712 345 678.';
    return undefined;
}

export function validateNewsletter(values: NewsletterSignup): FieldErrors<NewsletterSignup> {
    const errors: FieldErrors<NewsletterSignup> = {};
    const email = emailError(values.email);
    if (email) errors.email = email;
    return errors;
}

export const QUOTE_DETAILS_MAX = 1000;

/** Keys are added in the same order the fields appear on screen, so the first key is the first field to focus. */
export function validateQuote(values: QuoteRequest): FieldErrors<QuoteRequest> {
    const errors: FieldErrors<QuoteRequest> = {};

    if (!values.fullName.trim()) errors.fullName = 'Enter your full name.';

    const phone = phoneError(values.phone);
    if (phone) errors.phone = phone;

    const email = emailError(values.email);
    if (email) errors.email = email;

    if (!values.interest) errors.interest = 'Choose what you need a quotation for.';

    if (values.details.length > QUOTE_DETAILS_MAX) {
        errors.details = `Keep this under ${QUOTE_DETAILS_MAX.toLocaleString('en-KE')} characters.`;
    }

    return errors;
}
