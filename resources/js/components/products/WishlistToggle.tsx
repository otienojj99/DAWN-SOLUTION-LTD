import { Heart } from 'lucide-react';

interface WishlistToggleProps {
    active: boolean;
    onToggle: () => void;
    productName: string;
}

/** Heart button that adds/removes a product from the wishlist. `aria-pressed` conveys the toggle state. */
export function WishlistToggle({ active, onToggle, productName }: WishlistToggleProps) {
    return (
        <button
            type="button"
            onClick={onToggle}
            aria-pressed={active}
            aria-label={active ? `Remove ${productName} from wishlist` : `Add ${productName} to wishlist`}
            className="flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-[#2B2B2B] shadow-sm transition-colors duration-200 hover:bg-[#FAB40A] hover:text-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]"
        >
            <Heart
                className={`h-4 w-4 transition-colors duration-150 ${active ? 'fill-[#E14B4B] text-[#E14B4B]' : ''}`}
                aria-hidden="true"
            />
        </button>
    );
}
