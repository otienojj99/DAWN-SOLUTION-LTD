import { ShoppingCart } from 'lucide-react';

interface CartButtonProps {
    count?: number;
    href?: string;
}

/** Cart icon-action for the main header. Mirrors WishlistButton's structure. */
export function CartButton({ count = 0, href = '/cart' }: CartButtonProps) {
    return (
        <a
            href={href}
            aria-label={count > 0 ? `Shopping cart, ${count} items` : 'Shopping cart'}
            className="group relative flex flex-col items-center gap-0.5 px-2 py-1.5 text-[11.5px] font-semibold text-[#2B2B2B] transition-colors duration-200 hover:text-[#FAB40A]"
        >
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
        </a>
    );
}
