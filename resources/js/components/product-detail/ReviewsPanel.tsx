import { BadgeCheck, PenLine, Star } from 'lucide-react';
import { useId, useState } from 'react';
import { breakdownBars, breakdownFromReviews, formatReviewDate, initials } from '@/lib/product-detail';
import type { NewReview, ProductDetail } from '@/types/product-detail';
import { ReviewForm } from './ReviewForm';

interface ReviewsPanelProps {
    product: ProductDetail;
    onSubmitReview?: (review: NewReview) => Promise<void>;
}

const INITIAL_VISIBLE = 3;

function Stars({ rating, className = 'h-4 w-4' }: { rating: number; className?: string }) {
    return (
        <span className="flex gap-0.5" aria-hidden="true">
            {[1, 2, 3, 4, 5].map((n) => (
                <Star key={n} className={`${className} ${n <= Math.round(rating) ? 'fill-[#FAB40A] text-[#FAB40A]' : 'fill-transparent text-[#D5DAE1]'}`} strokeWidth={2} />
            ))}
        </span>
    );
}

/** Rating summary and bars, the review list (first three, then "show all"), and the "Write a Review" form with clickable stars. */
export function ReviewsPanel({ product, onSubmitReview }: ReviewsPanelProps) {
    const [showAll, setShowAll] = useState(false);
    const [writing, setWriting] = useState(false);
    const formId = useId();

    const { reviews } = product;
    const total = product.reviewCount ?? reviews.length;
    const rating = product.rating ?? 0;
    const bars = breakdownBars(product.ratingBreakdown ?? breakdownFromReviews(reviews));
    const visible = showAll ? reviews : reviews.slice(0, INITIAL_VISIBLE);

    return (
        <div>
            <div className="mb-6 grid gap-6 rounded-xl bg-[#F4F5F7] p-5 sm:grid-cols-[auto_1fr] sm:items-center sm:gap-10 lg:grid-cols-[auto_1fr_auto]">
                <div className="text-center sm:text-left">
                    <p className="text-[44px] font-extrabold leading-none text-[#152B4D]">{total > 0 ? rating.toFixed(1) : '–'}</p>
                    <div className="my-2 flex justify-center sm:justify-start">
                        <Stars rating={rating} className="h-[18px] w-[18px]" />
                    </div>
                    <p className="text-[12.5px] text-[#5B6470]">{total > 0 ? `Based on ${total} review${total === 1 ? '' : 's'}` : 'No reviews yet'}</p>
                </div>

                {total > 0 && (
                    <ul aria-label="Reviews by star rating" className="grid gap-1.5">
                        {bars.map((bar) => (
                            <li key={bar.stars} className="flex items-center gap-3 text-[12.5px]">
                                <span className="w-9 shrink-0 font-semibold text-[#2B2B2B]">{bar.stars} star</span>
                                <span className="h-2 flex-1 overflow-hidden rounded-full bg-[#DDE2EA]" aria-hidden="true">
                                    <span className="block h-full rounded-full bg-[#FAB40A]" style={{ width: `${bar.percent}%` }} />
                                </span>
                                <span className="w-7 shrink-0 text-right text-[#5B6470]">
                                    <span className="sr-only">{bar.count} reviews</span>
                                    <span aria-hidden="true">{bar.count}</span>
                                </span>
                            </li>
                        ))}
                    </ul>
                )}

                <button
                    type="button"
                    aria-expanded={writing}
                    aria-controls={formId}
                    onClick={() => setWriting((v) => !v)}
                    className="inline-flex items-center justify-center gap-2 rounded-lg border-[1.5px] border-[#325A96] bg-white px-5 py-2.5 text-[13.5px] font-extrabold text-[#325A96] transition-colors hover:bg-[#325A96] hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96] sm:col-span-2 lg:col-span-1"
                >
                    <PenLine className="h-4 w-4" aria-hidden="true" />
                    Write a Review
                </button>
            </div>

            <div id={formId} hidden={!writing} className="mb-6">
                {writing && <ReviewForm productId={product.id} onSubmitReview={onSubmitReview} onDone={() => setWriting(false)} />}
            </div>

            {reviews.length > 0 ? (
                <>
                    <ul className="divide-y divide-[#E4E7EC]">
                        {visible.map((review) => (
                            <li key={review.id} className="py-5 first:pt-0">
                                <div className="mb-2 flex flex-wrap items-center justify-between gap-2">
                                    <div className="flex items-center gap-3">
                                        <span className="flex h-10 w-10 items-center justify-center rounded-full bg-[#325A96] text-[13px] font-extrabold text-white" aria-hidden="true">
                                            {initials(review.author)}
                                        </span>
                                        <div>
                                            <p className="text-[14px] font-bold text-[#152B4D]">{review.author}</p>
                                            {review.verified && (
                                                <p className="flex items-center gap-1 text-[12px] font-semibold text-[#1E9E5A]">
                                                    <BadgeCheck className="h-3.5 w-3.5" aria-hidden="true" />
                                                    Verified Purchase
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                    <time dateTime={review.date} className="text-[12.5px] text-[#5B6470]">
                                        {formatReviewDate(review.date)}
                                    </time>
                                </div>
                                <div role="img" aria-label={`Rated ${review.rating} out of 5`} className="mb-1.5">
                                    <Stars rating={review.rating} />
                                </div>
                                <p className="text-[14px] leading-relaxed text-[#2B2B2B]">{review.text}</p>
                                {review.adminReply && (
        <div className="mt-3 rounded-lg border-l-4 border-[#325A96] bg-[#EAF0F9] px-4 py-3">
            <p className="mb-1 text-[12px] font-extrabold uppercase tracking-wide text-[#325A96]">
                Reply from Dawn Solutions
            </p>
            <p className="text-[13.5px] leading-relaxed text-[#2B2B2B]">{review.adminReply}</p>
            {review.adminRepliedAt && (
                <p className="mt-1.5 text-[11.5px] text-[#5B6470]">
                    {formatReviewDate(review.adminRepliedAt)}
                </p>
            )}
        </div>
)}
                            </li>
                        ))}
                    </ul>

                    {reviews.length > INITIAL_VISIBLE && (
                        <button
                            type="button"
                            aria-expanded={showAll}
                            onClick={() => setShowAll((v) => !v)}
                            className="mt-2 text-[13.5px] font-bold text-[#325A96] underline-offset-4 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#325A96]"
                        >
                            {showAll ? 'Show fewer reviews' : `Show all ${reviews.length} reviews`}
                        </button>
                    )}
                </>
            ) : (
                <p className="py-6 text-center text-[14px] text-[#5B6470]">No reviews yet. Be the first to share your experience.</p>
            )}
        </div>
    );
}
