import { SearchX } from 'lucide-react';
import { ProductCard } from '@/components/products';
import type { Product } from '@/types/product';
import type { ShopProduct } from '@/types/shop';

interface ProductGridProps {
    products: ShopProduct[];
    onAddToCart?: (product: Product) => void;
    onToggleWishlist?: (product: Product, wishlisted: boolean) => void;
    /** Shown in the empty state. Omit when there is nothing to clear. */
    onClearFilters?: () => void;
}

export function ProductGrid({ products, onAddToCart, onToggleWishlist, onClearFilters }: ProductGridProps) {
    if (products.length === 0) {
        return (
            <div className="flex flex-col items-center rounded-xl border border-dashed border-[#D5DAE1] bg-white px-6 py-16 text-center">
                <span className="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[#EAF0F9] text-[#325A96]">
                    <SearchX className="h-7 w-7" aria-hidden="true" />
                </span>
                <h2 className="mb-1.5 text-lg font-extrabold text-[#152B4D]">No products match these filters</h2>
                <p className="mb-5 max-w-sm text-[13.5px] text-[#5B6470]">Try removing a filter or widening the price range to see more of what we have.</p>
                {onClearFilters && (
                    <button
                        type="button"
                        onClick={onClearFilters}
                        className="rounded-lg bg-[#325A96] px-5 py-2.5 text-[13.5px] font-bold text-white transition-colors hover:bg-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]"
                    >
                        Clear all filters
                    </button>
                )}
            </div>
        );
    }

    return (
        <ul className="grid grid-cols-2 gap-3 md:gap-4 xl:grid-cols-3">
            {products.map((product) => (
                <li key={product.id} className="flex">
                    <ProductCard product={product} onAddToCart={onAddToCart} onToggleWishlist={onToggleWishlist} className="w-full" />
                </li>
            ))}
        </ul>
    );
}
