import { ShoppingBag, Trash2 } from 'lucide-react';
import { useEffect } from 'react';
import { WhatsAppIcon } from '@/components/chat/WhatsAppIcon';
import { commerceCopy } from '@/data/commerce';
import { buildCartWhatsAppMessage } from '@/lib/cart-math';
import { whatsappUrl } from '@/lib/whatsapp';
import { useCart } from '@/store/cart';
import { CartLineItem } from './CartLineItem';
import { CartSummary } from './CartSummary';

/** Full-page cart: the same lines, quantities and totals as the drawer, with more room. */
export function CartPage({ checkoutHref = '/checkout' }: { checkoutHref?: string }) {
    const cart = useCart();

    useEffect(() => {
        document.title = 'Your Cart | Dawn Solution';
    }, []);

    return (
        <div className="mx-auto max-w-[1100px] px-4 pb-16 pt-8 sm:px-6">
            <h1 className="mb-6 text-[26px] font-extrabold text-[#152B4D] md:text-[30px]">Your Cart{cart.itemCount > 0 && ` (${cart.itemCount})`}</h1>

            {cart.lines.length === 0 ? (
                <div className="flex flex-col items-center gap-3 rounded-xl border border-[#E4E7EC] bg-[#F9FAFB] px-6 py-16 text-center">
                    <span className="flex h-16 w-16 items-center justify-center rounded-full bg-[#EAF0F9] text-[#325A96]">
                        <ShoppingBag className="h-7 w-7" aria-hidden="true" />
                    </span>
                    <p className="text-[17px] font-bold text-[#152B4D]">Your cart is empty</p>
                    <p className="text-[14px] text-[#5B6470]">Find something you need and add it to your cart.</p>
                    <a href="/shop" className="mt-2 rounded-lg bg-[#325A96] px-6 py-3 text-[14px] font-bold text-white hover:bg-[#FAB40A] hover:text-[#152B4D]">
                        Continue shopping
                    </a>
                </div>
            ) : (
                <div className="grid gap-8 lg:grid-cols-[minmax(0,1fr)_340px]">
                    <section aria-label="Items in your cart">
                        <ul className="divide-y divide-[#E4E7EC] rounded-xl border border-[#E4E7EC] bg-white px-4 sm:px-5">
                            {cart.lines.map((line) => (
                                <CartLineItem key={line.key} line={line} variant="page" onQuantity={cart.setQuantity} onRemove={cart.remove} />
                            ))}
                        </ul>
                        <div className="mt-4 flex flex-wrap items-center justify-between gap-3">
                            <a href="/shop" className="text-[13.5px] font-bold text-[#325A96] hover:underline">
                                Continue shopping
                            </a>
                            <button type="button" onClick={cart.clear} className="inline-flex items-center gap-1.5 text-[13px] font-semibold text-[#8A93A3] hover:text-[#C0292B]">
                                <Trash2 className="h-4 w-4" aria-hidden="true" />
                                Clear cart
                            </button>
                        </div>
                    </section>

                    <aside aria-label="Order summary" className="h-fit space-y-3.5 rounded-xl border border-[#E4E7EC] bg-[#F9FAFB] p-5 lg:sticky lg:top-4">
                        <h2 className="text-[16px] font-extrabold text-[#152B4D]">Order summary</h2>
                        <CartSummary totals={cart.totals} />
                        <p className="text-[12px] leading-snug text-[#5B6470]">{commerceCopy.deliveryNote}</p>
                        <a href={checkoutHref} className="flex items-center justify-center rounded-lg bg-[#325A96] px-4 py-3 text-[14px] font-bold text-white transition-colors hover:bg-[#FAB40A] hover:text-[#152B4D]">
                            Proceed to checkout
                        </a>
                        <a
                            href={whatsappUrl(buildCartWhatsAppMessage(cart.lines))}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="flex items-center justify-center gap-2 rounded-lg bg-[#1FA855] px-4 py-3 text-[14px] font-bold text-white hover:bg-[#178a45]"
                        >
                            <WhatsAppIcon className="h-5 w-5" />
                            Order on WhatsApp
                            <span className="sr-only">(opens in a new tab)</span>
                        </a>
                    </aside>
                </div>
            )}
        </div>
    );
}
