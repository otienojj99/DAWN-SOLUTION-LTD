import { commerceCopy } from '@/data/commerce';
import { formatPrice } from '@/lib/format';
import type { CartTotals } from '@/types/cart';

interface CartSummaryProps {
    totals: CartTotals;
    /** Show the delivery row (checkout). The cart drawer and cart page show a note instead. */
    showDelivery?: boolean;
}

/** Subtotal, VAT and total rows. Used by the drawer, cart page and checkout so the numbers are always worked out one way. */
export function CartSummary({ totals, showDelivery = false }: CartSummaryProps) {
    const row = 'flex items-baseline justify-between gap-3 text-[13.5px]';
    const withDelivery = showDelivery && totals.shippingStatus === 'included';
    const beforeDelivery = totals.subtotal + totals.vat;
    return (
        <dl className="space-y-1.5">
            <div className={row}>
                <dt className="text-[#5B6470]">Subtotal</dt>
                <dd className="font-semibold text-[#2B2B2B]" data-testid="subtotal">{formatPrice(totals.subtotal)}</dd>
            </div>
            <div className={row}>
                <dt className="text-[#5B6470]">{commerceCopy.vatLabel}</dt>
                <dd className="font-semibold text-[#2B2B2B]" data-testid="vat">{formatPrice(totals.vat)}</dd>
            </div>
            {showDelivery && (
                <div className={row}>
                    <dt className="text-[#5B6470]">Delivery</dt>
                    <dd className="font-semibold text-[#2B2B2B]" data-testid="shipping">
                        {totals.shippingStatus === 'included' ? formatPrice(totals.shipping) : totals.shippingStatus === 'to-confirm' ? 'To be confirmed' : 'Choose area'}
                    </dd>
                </div>
            )}
            <div className="flex items-baseline justify-between gap-3 border-t border-[#E4E7EC] pt-2.5">
                <dt className="text-[14px] font-extrabold text-[#152B4D]">{withDelivery ? 'Total' : 'Total (VAT incl.)'}</dt>
                <dd className="text-[18px] font-extrabold text-[#152B4D]" data-testid="total">{formatPrice(showDelivery ? totals.total : beforeDelivery)}</dd>
            </div>
            {showDelivery && totals.shippingStatus === 'to-confirm' && <p className="text-[11.5px] text-[#5B6470]">Total shown excludes delivery. {commerceCopy.outsideNairobiNote}</p>}
        </dl>
    );
}
