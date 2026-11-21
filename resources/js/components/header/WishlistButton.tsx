import { Heart, ShoppingCart, X } from 'lucide-react';
import { HoverPopover } from '@/components/common/HoverPopover';
import { formatPrice } from '@/lib/format';
import { useOptionalCart } from '@/store/cart';
import { useOptionalWishlist } from '@/store/wishlist';
import type { NewCartLine } from '@/types/cart';

interface WishlistButtonProps {
    /** Link in the panel footer to a full wishlist page. */
    href?: string;
}

const trigger =
    'group relative flex flex-col items-center gap-0.5 px-2 py-1.5 text-[11.5px] font-semibold text-[#2B2B2B] transition-colors duration-200 hover:text-[#FAB40A] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#325A96]';

/** Wishlist icon with a count badge. Hover (or tap) shows what is on the wishlist, or a short empty message. */
export function WishlistButton({ href = '/wishlist' }: WishlistButtonProps) {
    const wishlist = useOptionalWishlist();
    const cart = useOptionalCart();
    const items = wishlist?.items ?? [];
    const count = items.length;

    return (
        <HoverPopover
            label={count > 0 ? `Wishlist, ${count} ${count === 1 ? 'item' : 'items'}` : 'Wishlist'}
            triggerClassName={trigger}
            trigger={
                <>
                    <span className="relative">
                        <Heart className="h-5 w-5 text-[#325A96] transition-colors duration-200 group-hover:text-[#FAB40A]" strokeWidth={2} aria-hidden="true" />
                        {count > 0 && (
                            <span className="absolute -right-2 -top-2 flex h-4 min-w-4 items-center justify-center rounded-full bg-[#FAB40A] px-1 text-[10px] font-extrabold leading-none text-[#152B4D]" aria-hidden="true">
                                {count > 99 ? '99+' : count}
                            </span>
                        )}
                    </span>
                    <span className="hidden sm:inline">Wishlist</span>
                </>
            }
        >
            {(close) => (
                <div>
                    <div className="border-b border-[#E4E7EC] px-4 py-3 text-[14px] font-extrabold text-[#152B4D]">My Wishlist{count > 0 && ` (${count})`}</div>
                    {count === 0 ? (
                        <p className="px-4 py-8 text-center text-[13.5px] text-[#5B6470]">You have no items on your wishlist.</p>
                    ) : (
                        <ul className="max-h-[340px] divide-y divide-[#E4E7EC] overflow-y-auto">
                            {items.map((item) => (
                                <li key={item.productId} className="flex gap-3 px-4 py-3">
                                    <a href={`/products/${item.slug}`} onClick={close} tabIndex={-1} aria-hidden="true" className="h-14 w-14 shrink-0 overflow-hidden rounded-md bg-[#F4F5F7]">
                                        <img src={item.image} alt="" className="h-full w-full object-cover" loading="lazy" />
                                    </a>
                                    <div className="min-w-0 flex-1">
                                        <a href={`/products/${item.slug}`} onClick={close} className="line-clamp-2 text-[13px] font-bold leading-snug text-[#2B2B2B] hover:text-[#325A96]">
                                            {item.name}
                                        </a>
                                        <p className="mt-0.5 text-[13px] font-extrabold text-[#152B4D]">{formatPrice(item.price)}</p>
                                        <button
                                            type="button"
                                            disabled={!cart || item.inStock === false}
                                            onClick={() => {
                                                const line: NewCartLine = { productId: item.productId, slug: item.slug, name: item.name, brand: item.brand, image: item.image, unitPrice: item.price };
                                                cart?.add(line, 1);
                                            }}
                                            className="mt-1.5 inline-flex items-center gap-1.5 rounded-md bg-[#325A96] px-2.5 py-1.5 text-[12px] font-bold text-white hover:bg-[#FAB40A] hover:text-[#152B4D] disabled:cursor-not-allowed disabled:bg-[#C8CED8]"
                                        >
                                            <ShoppingCart className="h-3.5 w-3.5" aria-hidden="true" />
                                            {item.inStock === false ? 'Out of stock' : 'Add to cart'}
                                        </button>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => wishlist?.remove(item.productId)}
                                        aria-label={`Remove ${item.name} from wishlist`}
                                        className="flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-[#8A93A3] hover:bg-[#FDECEC] hover:text-[#C0292B]"
                                    >
                                        <X className="h-4 w-4" aria-hidden="true" />
                                    </button>
                                </li>
                            ))}
                        </ul>
                    )}
                    {count > 0 && (
                        <a href={href} onClick={close} className="block border-t border-[#E4E7EC] bg-[#F9FAFB] px-4 py-3 text-center text-[13px] font-bold text-[#325A96] hover:text-[#152B4D]">
                            View full wishlist
                        </a>
                    )}
                </div>
            )}
        </HoverPopover>
    );
}
