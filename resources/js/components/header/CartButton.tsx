import { ShoppingCart } from 'lucide-react';
import { useOptionalCart } from '@/store/cart';

interface CartButtonProps {
    count?: number;
    href?: string;
}

/** Cart icon-action for the main header. Mirrors WishlistButton's structure. */
export function CartButton({ count: fallbackCount = 0, href = '/cart' }: CartButtonProps) {
    const cart = useOptionalCart();
    const count = cart?.itemCount ?? fallbackCount;
    const label = count > 0 ? `Shopping cart, ${count} ${count === 1 ? 'item' : 'items'}` : 'Shopping cart';
    const contents = (
        <>
            <span className="relative">
                <ShoppingCart
                    className="h-5 w-5 text-[#325A96] transition-colors duration-200 group-hover:text-[#FAB40A]"
                    strokeWidth={2}
                    aria-hidden="true"
                />
                {count > 0 && (
                    <span
                        className="absolute -right-2 -top-2 flex h-4 min-w-4 items-center justify-center rounded-full bg-[#FAB40A] px-1 text-[10px] font-extrabold leading-none text-[#152B4D]"
                        aria-hidden="true"
                    >
                        {count > 99 ? '99+' : count}
                    </span>
                )}
            </span>
            <span className="hidden sm:inline">Cart</span>
        </>
    );
    const className = 'group relative flex flex-col items-center gap-0.5 px-2 py-1.5 text-[11.5px] font-semibold text-[#2B2B2B] transition-colors duration-200 hover:text-[#FAB40A] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#325A96]';

    if (cart) {
        return (
            <button
                type="button"
                onClick={cart.openDrawer}
                aria-label={label}
                aria-haspopup="dialog"
                aria-expanded={cart.drawerOpen}
                className={className}
            >
                {contents}
            </button>
        );
    }

    return (
        <a
            href={href}
            aria-label={label}
            className={className}
        >
            {contents}
        </a>
    );
}
