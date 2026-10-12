import { ShoppingCart } from 'lucide-react';
import { useState } from 'react';
import { productHref } from '@/data/products';
import { discountPercent, formatPrice } from '@/lib/format';
import type { Product } from '@/types/product';
import { ProductBadge } from './ProductBadge';
import { RatingStars } from './RatingStars';
import { WishlistToggle } from './WishlistToggle';

interface ProductCardProps {
    product: Product;
    /** Wire to your cart store / Inertia post. Receives the product being added. */
    onAddToCart?: (product: Product) => void;
    /** Called when the heart is toggled; receives the product and the new state. */
    onToggleWishlist?: (product: Product, wishlisted: boolean) => void;
    className?: string;
}

/**
 * One product tile. The image and the name link to the product page; the
 * wishlist heart and "Add to Cart" are real buttons (not nested inside the
 * link), so keyboard and screen-reader users get distinct, correctly
 * labelled controls. The card itself is presentational — cart and
 * wishlist side effects are reported upward through callbacks.
 */
export function ProductCard({ product, onAddToCart, onToggleWishlist, className = '' }: ProductCardProps) {
    const [wishlisted, setWishlisted] = useState(false);
    const href = productHref(product);
    const savePercent = discountPercent(product.price, product.oldPrice);
    const lowStock = product.stockCount !== undefined && product.stockCount <= 5;
    const outOfStock = product.inStock === false;
    const currency = product.currency as 'KES' | 'USD' | undefined;

    function handleWishlist() {
        const next = !wishlisted;
        setWishlisted(next);
        onToggleWishlist?.(product, next);
    }

    return (
        <article
            className={`group flex flex-col overflow-hidden rounded-xl border border-[#E4E7EC] bg-white transition-all duration-300 hover:-translate-y-1 hover:border-transparent hover:shadow-[0_14px_30px_rgba(21,43,77,0.15)] motion-reduce:transition-none motion-reduce:hover:translate-y-0 ${className}`}
        >
            <div className="relative h-44 overflow-hidden bg-[#F4F5F7]">
                <a href={href} tabIndex={-1} aria-hidden="true" className="block h-full">
                    <img
                        src={product.image}
                        alt=""
                        loading="lazy"
                        className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.07] motion-reduce:transition-none motion-reduce:group-hover:scale-100"
                    />
                </a>

                {product.badge && (
                    <span className="absolute left-2.5 top-2.5">
                        <ProductBadge badge={product.badge} discountPercent={savePercent} />
                    </span>
                )}
                <span className="absolute right-2.5 top-2.5">
                    <WishlistToggle active={wishlisted} onToggle={handleWishlist} productName={product.name} />
                </span>
            </div>

            <div className="flex flex-1 flex-col p-4">
                <p className="mb-1 text-[10.5px] font-bold uppercase tracking-wide text-[#325A96]">{product.brand}</p>

                <h3 className="mb-2.5 min-h-[2.6rem] text-sm font-bold leading-snug text-[#2B2B2B]">
                    <a href={href} className="transition-colors duration-150 hover:text-[#325A96] focus-visible:outline-none focus-visible:underline">
                        {product.name}
                    </a>
                </h3>

                {product.rating !== undefined && (
                    <div className="mb-3">
                        <RatingStars rating={product.rating} reviewCount={product.reviewCount} />
                    </div>
                )}

                <div className="mb-1 flex flex-wrap items-baseline gap-x-2">
                    <span className="text-[17px] font-extrabold text-[#152B4D]">{formatPrice(product.price, currency)}</span>
                    {product.oldPrice && (
                        <span className="text-[12px] text-[#9AA3AF] line-through">{formatPrice(product.oldPrice, currency)}</span>
                    )}
                </div>

                {product.oldPrice && savePercent && (
                    <p className="text-[11px] font-bold text-[#1E9E5A]">
                        You save {formatPrice(product.oldPrice - product.price, currency)}
                    </p>
                )}

                <p
                    className={`mb-3.5 mt-1.5 text-[11px] ${
                        outOfStock ? 'font-bold text-[#E14B4B]' : lowStock ? 'font-bold text-[#E14B4B]' : 'text-[#5B6470]'
                    }`}
                >
                    {outOfStock ? 'Out of stock' : lowStock ? `Only ${product.stockCount} left` : 'In stock'}
                </p>

                <button
                    type="button"
                    disabled={outOfStock}
                    onClick={() => onAddToCart?.(product)}
                    className="mt-auto flex items-center justify-center gap-2 rounded-lg bg-[#325A96] px-3 py-2.5 text-[13px] font-bold text-white transition-colors duration-200 hover:bg-[#FAB40A] hover:text-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96] disabled:cursor-not-allowed disabled:bg-[#C8CED8] disabled:text-white"
                >
                    <ShoppingCart className="h-4 w-4" aria-hidden="true" />
                    {outOfStock ? 'Unavailable' : 'Add to Cart'}
                </button>
            </div>
        </article>
    );
}
