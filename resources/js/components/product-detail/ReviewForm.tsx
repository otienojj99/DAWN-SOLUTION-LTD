import { useState } from 'react';
import type { FormEvent } from 'react';
import { TextAreaField, TextField } from '@/components/forms';
import type { NewReview } from '@/types/product-detail';
import { StarRatingInput } from './StarRatingInput';

interface ReviewFormProps {
    productId: string;
    /**
     * Sends the review to your backend. Throw an Error with a readable
     * message to show it to the visitor. New reviews normally wait for
     * moderation, so the success message says so.
     */
    onSubmitReview?: (review: NewReview) => Promise<void>;
    onDone?: () => void;
}

interface Errors {
    rating?: string;
    name?: string;
    text?: string;
}

const MIN_TEXT = 10;
const MAX_TEXT = 1000;

export function ReviewForm({ productId, onSubmitReview, onDone }: ReviewFormProps) {
    const [rating, setRating] = useState(0);
    const [name, setName] = useState('');
    const [text, setText] = useState('');
    const [errors, setErrors] = useState<Errors>({});
    const [state, setState] = useState<{ status: 'idle' | 'sending' | 'done' | 'error'; message?: string }>({ status: 'idle' });

    function validate(): Errors {
        const found: Errors = {};
        if (rating < 1) found.rating = 'Please choose a star rating.';
        if (name.trim().length < 2) found.name = 'Please enter your name.';
        if (text.trim().length < MIN_TEXT) found.text = `Please write at least ${MIN_TEXT} characters.`;
        return found;
    }

    async function handleSubmit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        if (state.status === 'sending') return;

        const found = validate();
        setErrors(found);
        const first = (['rating', 'name', 'text'] as const).find((k) => found[k]);
        if (first) {
            const form = event.currentTarget;
            (first === 'rating' ? form.querySelector<HTMLElement>('input[type=radio]') : form.querySelector<HTMLElement>(`[name="${first}"]`))?.focus();
            return;
        }

        setState({ status: 'sending' });
        try {
            if (!onSubmitReview) throw new Error('Reviews are not connected yet.');
            await onSubmitReview({ productId, rating, name: name.trim(), text: text.trim() });
            setState({ status: 'done' });
        } catch (error) {
            setState({ status: 'error', message: error instanceof Error ? error.message : 'Could not send your review. Please try again.' });
        }
    }

    if (state.status === 'done') {
        return (
            <div role="status" className="rounded-xl border border-[#1E9E5A]/30 bg-[#1E9E5A]/10 p-5">
                <p className="mb-1 text-[15px] font-extrabold text-[#152B4D]">Thank you for your review</p>
                <p className="mb-3 text-[13.5px] text-[#5B6470]">It will appear on this page once our team has checked it.</p>
                <button type="button" onClick={onDone} className="text-[13px] font-bold text-[#325A96] underline-offset-4 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#325A96]">
                    Close
                </button>
            </div>
        );
    }

    return (
        <form onSubmit={handleSubmit} noValidate aria-label="Write a review" className="grid gap-4 rounded-xl border border-[#E4E7EC] bg-[#F9FAFB] p-5">
            <div>
                <p className="mb-2 text-[12.5px] font-bold text-[#2B2B2B]">Your rating</p>
                <StarRatingInput
                    value={rating}
                    onChange={(n) => {
                        setRating(n);
                        if (errors.rating) setErrors((e) => ({ ...e, rating: undefined }));
                    }}
                    error={errors.rating}
                />
            </div>

            <TextField
                id="review-name"
                name="name"
                label="Your name"
                autoComplete="name"
                value={name}
                onChange={(e) => {
                    setName(e.target.value);
                    if (errors.name) setErrors((er) => ({ ...er, name: undefined }));
                }}
                error={errors.name}
            />
            <TextAreaField
                id="review-text"
                name="text"
                label="Your review"
                placeholder="Share your experience with this product..."
                maxLength={MAX_TEXT}
                value={text}
                onChange={(e) => {
                    setText(e.target.value);
                    if (errors.text) setErrors((er) => ({ ...er, text: undefined }));
                }}
                error={errors.text}
            />

            {state.status === 'error' && (
                <p role="alert" className="rounded-lg border border-[#C0292B]/30 bg-[#C0292B]/10 px-3.5 py-2.5 text-[13px] font-semibold text-[#C0292B]">
                    {state.message}
                </p>
            )}

            <div className="flex flex-wrap items-center gap-3">
                <button
                    type="submit"
                    disabled={state.status === 'sending'}
                    className="rounded-lg bg-[#325A96] px-6 py-2.5 text-[14px] font-extrabold text-white transition-colors hover:bg-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96] disabled:cursor-wait disabled:opacity-70"
                >
                    {state.status === 'sending' ? 'Sending...' : 'Submit Review'}
                </button>
                <button type="button" onClick={onDone} className="text-[13px] font-bold text-[#5B6470] hover:text-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]">
                    Cancel
                </button>
            </div>
        </form>
    );
}
