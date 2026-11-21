import { Trash2 } from 'lucide-react';
import { QuantityStepper } from '@/components/product-detail/QuantityStepper';
import { formatPrice } from '@/lib/format';
import { lineTotal } from '@/lib/cart-math';
import type { CartLine } from '@/types/cart';

interface CartLineItemProps {
    line: CartLine;
    onQuantity: (key: string, quantity: number) => void;
    onRemove: (key: string) => void;
    /** Called when a link inside the line is followed (the drawer closes itself). */
    onNavigate?: () => void;
    /** 'page' gives the larger layout used on the cart page. */
    variant?: 'drawer' | 'page';
}

/** One row of the cart: picture, name and options, quantity −/+ and the line total, which updates as the quantity changes. */
export function CartLineItem({ line, onQuantity, onRemove, onNavigate, variant = 'drawer' }: CartLineItemProps) {
    const href = `/products/${line.slug}`;
    const big = variant === 'page';
    return (
        <li className={`flex gap-3 ${big ? 'py-5' : 'py-4'}`}>
            <a href={href} onClick={onNavigate} className={`shrink-0 overflow-hidden rounded-lg bg-[#F4F5F7] ${big ? 'h-24 w-24' : 'h-[72px] w-[72px]'}`} tabIndex={-1} aria-hidden="true">
                <img src={line.image} alt="" className="h-full w-full object-cover" loading="lazy" />
            </a>
            <div className="min-w-0 flex-1">
                <div className="flex items-start justify-between gap-2">
                    <div className="min-w-0">
                        <p className="text-[10.5px] font-bold uppercase tracking-wide text-[#325A96]">{line.brand}</p>
                        <a href={href} onClick={onNavigate} className="line-clamp-2 text-[13.5px] font-bold leading-snug text-[#2B2B2B] hover:text-[#325A96] focus-visible:underline focus-visible:outline-none">
                            {line.name}
                        </a>
                        {line.variantSummary && <p className="mt-0.5 text-[11.5px] text-[#5B6470]">{line.variantSummary}</p>}
                    </div>
                    <button
                        type="button"
                        onClick={() => onRemove(line.key)}
                        aria-label={`Remove ${line.name} from cart`}
                        className="-mr-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-[#8A93A3] transition-colors hover:bg-[#FDECEC] hover:text-[#C0292B] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#325A96]"
                    >
                        <Trash2 className="h-4 w-4" aria-hidden="true" />
                    </button>
                </div>
                <div className="mt-2 flex flex-wrap items-center justify-between gap-x-3 gap-y-2">
                    <QuantityStepper value={line.quantity} max={line.maxQuantity ?? 99} onChange={(q) => onQuantity(line.key, q)} />
                    <div className="text-right">
                        <p className="text-[14.5px] font-extrabold text-[#152B4D]" data-testid="line-total">{formatPrice(lineTotal(line))}</p>
                        {line.quantity > 1 && <p className="text-[11px] text-[#5B6470]">{formatPrice(line.unitPrice)} each</p>}
                    </div>
                </div>
                {line.maxQuantity !== undefined && line.quantity >= line.maxQuantity && <p className="mt-1 text-[11px] font-semibold text-[#B45309]">Maximum available: {line.maxQuantity}</p>}
            </div>
        </li>
    );
}
