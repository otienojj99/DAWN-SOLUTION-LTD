import { Maximize2, ShoppingBag, X } from 'lucide-react';
import { useRef } from 'react';
import { WhatsAppIcon } from '@/components/chat/WhatsAppIcon';
import { commerceCopy } from '@/data/commerce';
import { useDialogFocus } from '@/hooks/useDialogFocus';
import { buildCartWhatsAppMessage } from '@/lib/cart-math';
import { whatsappUrl } from '@/lib/whatsapp';
import { useCart } from '@/store/cart';
import { CartLineItem } from './CartLineItem';
import { CartSummary } from './CartSummary';

interface CartDrawerProps {
    cartHref?: string;
    checkoutHref?: string;
}

/**
 * Side drawer that lists the cart. Opens from the header cart icon. The
 * expand icon goes to the full cart page. Modal: focus stays inside,
 * Escape and the backdrop close it.
 */
export function CartDrawer({ cartHref = '/cart', checkoutHref = '/checkout' }: CartDrawerProps) {
    const cart = useCart();
    const panelRef = useRef<HTMLDivElement>(null);
    useDialogFocus(panelRef, cart.drawerOpen, cart.closeDrawer);

    if (!cart.drawerOpen) return null;
    const empty = cart.lines.length === 0;

    return (
        <div className="fixed inset-0 z-[80]">
            <style>{`
                @keyframes dsl-drawer-in { from { transform: translateX(100%); } to { transform: none; } }
                @keyframes dsl-fade-in { from { opacity: 0; } to { opacity: 1; } }
                .dsl-drawer { animation: dsl-drawer-in 260ms cubic-bezier(.2,.8,.2,1); }
                .dsl-backdrop { animation: dsl-fade-in 200ms ease-out; }
                @media (prefers-reduced-motion: reduce) { .dsl-drawer, .dsl-backdrop { animation: none; } }
            `}</style>
            <div className="dsl-backdrop absolute inset-0 bg-[#152B4D]/50" onClick={cart.closeDrawer} aria-hidden="true" />
            <div
                ref={panelRef}
                role="dialog"
                aria-modal="true"
                aria-label={`Shopping cart, ${cart.itemCount} ${cart.itemCount === 1 ? 'item' : 'items'}`}
                className="dsl-drawer absolute right-0 top-0 flex h-full w-full max-w-[420px] flex-col bg-white shadow-[-12px_0_40px_rgba(21,43,77,0.25)]"
            >
                <div className="flex items-center justify-between gap-2 border-b border-[#E4E7EC] px-5 py-4">
                    <h2 className="text-[17px] font-extrabold text-[#152B4D]">Your Cart ({cart.itemCount})</h2>
                    <div className="flex items-center gap-1">
                        <a
                            href={cartHref}
                            onClick={cart.closeDrawer}
                            aria-label="Open cart in full page"
                            title="Open cart in full page"
                            className="flex h-9 w-9 items-center justify-center rounded-md text-[#325A96] transition-colors hover:bg-[#EAF0F9] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#325A96]"
                        >
                            <Maximize2 className="h-[18px] w-[18px]" aria-hidden="true" />
                        </a>
                        <button
                            type="button"
                            onClick={cart.closeDrawer}
                            data-autofocus
                            aria-label="Close cart"
                            className="flex h-9 w-9 items-center justify-center rounded-md text-[#2B2B2B] transition-colors hover:bg-[#F4F5F7] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#325A96]"
                        >
                            <X className="h-5 w-5" aria-hidden="true" />
                        </button>
                    </div>
                </div>

                {empty ? (
                    <div className="flex flex-1 flex-col items-center justify-center gap-3 px-8 text-center">
                        <span className="flex h-16 w-16 items-center justify-center rounded-full bg-[#EAF0F9] text-[#325A96]">
                            <ShoppingBag className="h-7 w-7" aria-hidden="true" />
                        </span>
                        <p className="text-[15px] font-bold text-[#152B4D]">Your cart is empty</p>
                        <p className="text-[13px] text-[#5B6470]">Add a product and it will show up here.</p>
                        <a href="/shop" onClick={cart.closeDrawer} className="mt-1 rounded-lg bg-[#325A96] px-5 py-2.5 text-[13px] font-bold text-white hover:bg-[#FAB40A] hover:text-[#152B4D]">
                            Continue shopping
                        </a>
                    </div>
                ) : (
                    <>
                        <ul className="flex-1 divide-y divide-[#E4E7EC] overflow-y-auto px-5">
                            {cart.lines.map((line) => (
                                <CartLineItem key={line.key} line={line} onQuantity={cart.setQuantity} onRemove={cart.remove} onNavigate={cart.closeDrawer} />
                            ))}
                        </ul>
                        <div className="space-y-3 border-t border-[#E4E7EC] bg-[#F9FAFB] px-5 py-4">
                            <CartSummary totals={cart.totals} />
                            <p className="text-[11.5px] leading-snug text-[#5B6470]">{commerceCopy.deliveryNote}</p>
                            <a href={checkoutHref} onClick={cart.closeDrawer} className="flex items-center justify-center rounded-lg bg-[#325A96] px-4 py-3 text-[14px] font-bold text-white transition-colors hover:bg-[#FAB40A] hover:text-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]">
                                Checkout
                            </a>
                            <a
                                href={whatsappUrl(buildCartWhatsAppMessage(cart.lines))}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="flex items-center justify-center gap-2 rounded-lg bg-[#1FA855] px-4 py-3 text-[14px] font-bold text-white transition-colors hover:bg-[#178a45] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1FA855]"
                            >
                                <WhatsAppIcon className="h-5 w-5" />
                                Order on WhatsApp
                                <span className="sr-only">(opens in a new tab)</span>
                            </a>
                        </div>
                    </>
                )}
            </div>
        </div>
    );
}
