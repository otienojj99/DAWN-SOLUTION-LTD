import { Star } from 'lucide-react';

interface RatingStarsProps {
    rating: number;
    reviewCount?: number;
}

/**
 * Five-star rating row. Stars are decorative (aria-hidden); the accessible
 * name is a single sentence on the wrapper, so screen readers hear
 * "Rated 4.8 out of 5, 94 reviews" instead of five separate stars.
 */
export function RatingStars({ rating, reviewCount }: RatingStarsProps) {
    const label = `Rated ${rating.toFixed(1)} out of 5${reviewCount !== undefined ? `, ${reviewCount} reviews` : ''}`;

    return (
        <div className="flex items-center gap-1.5" role="img" aria-label={label}>
            <span className="flex gap-0.5" aria-hidden="true">
                {[1, 2, 3, 4, 5].map((n) => (
                    <Star
                        key={n}
                        className={`h-3.5 w-3.5 ${n <= Math.round(rating) ? 'fill-[#FAB40A] text-[#FAB40A]' : 'fill-transparent text-[#D5DAE1]'}`}
                        strokeWidth={2}
                    />
                ))}
            </span>
            <span className="text-[11.5px] text-[#5B6470]" aria-hidden="true">
                {rating.toFixed(1)}
                {reviewCount !== undefined && ` (${reviewCount})`}
            </span>
        </div>
    );
}
