import { Check, ShoppingCart } from 'lucide-react';
import { useState } from 'react';
import { WhatsAppIcon } from '@/components/chat';
import { productCopy, productTrust } from '@/data/product-config';
import { maxQuantity } from '@/lib/product-detail';
import type { ProductDetail, PurchaseItem, VariantSelection } from '@/types/product-detail';
import { QuantityStepper } from './QuantityStepper';
import { VariantSelector } from './VariantSelector';

interface PurchasePanelProps {
    product: ProductDetail;
    selection: VariantSelection;
    onSelectionChange: (groupId: string, value: string) => void;
    quantity: number;
    onQuantityChange: (quantity: number) => void;
    /** Builds what is sent to the cart / checkout from the current choices. */
    buildItem: () => PurchaseItem;
    onAddToCart?: (item: PurchaseItem) => void | Promise<void>;
    onOrderNow?: (item: PurchaseItem) => void | Promise<void>;
    onAskOnWhatsApp: () => void;
}

type Notice = { kind: 'success' | 'error'; text: string } | null;

/** Variants, quantity, the buy buttons, WhatsApp, payment note, trust points and tags. */
export function PurchasePanel({ product, selection, onSelectionChange, quantity, onQuantityChange, buildItem, onAddToCart, onOrderNow, onAskOnWhatsApp }: PurchasePanelProps) {
    const [notice, setNotice] = useState<Notice>(null);
    const [busy, setBusy] = useState<'cart' | 'order' | null>(null);

    const outOfStock = product.inStock === false;
    const lowStock = product.stockCount !== undefined && product.stockCount <= 5;

    async function run(kind: 'cart' | 'order', handler: PurchasePanelProps['onAddToCart'], success: string) {
        if (busy) return;
        // Never pretend it worked: without a handler the buyer is told the shop isn't wired up yet.
        if (!handler) {
            setNotice({ kind: 'error', text: kind === 'cart' ? 'The cart is not connected yet.' : 'Checkout is not connected yet.' });
            return;
        }
        setBusy(kind);
        try {
            await handler(buildItem());
            setNotice({ kind: 'success', text: success });
        } catch (error) {
            setNotice({ kind: 'error', text: error instanceof Error ? error.message : 'Something went wrong. Please try again.' });
        } finally {
            setBusy(null);
        }
    }

    return (
        <div className="grid gap-5">
            {product.variants.map((group) => (
                <VariantSelector key={group.id} group={group} value={selection[group.id]} onChange={(value) => onSelectionChange(group.id, value)} />
            ))}

            <div className="flex flex-wrap items-center gap-x-5 gap-y-3">
                <QuantityStepper value={quantity} onChange={onQuantityChange} max={maxQuantity(product)} />
                <p className={`flex items-center gap-2 text-[13px] font-bold ${outOfStock ? 'text-[#C0292B]' : lowStock ? 'text-[#C0292B]' : 'text-[#1E9E5A]'}`}>
                    <span className={`h-2 w-2 rounded-full ${outOfStock || lowStock ? 'bg-[#C0292B]' : 'bg-[#1E9E5A]'}`} aria-hidden="true" />
                    {outOfStock ? 'Out of stock' : lowStock ? `Only ${product.stockCount} left` : `In Stock — ${productCopy.shipsIn}`}
                </p>
            </div>

            <div className="grid gap-3 sm:grid-cols-2">
                <button
                    type="button"
                    disabled={outOfStock || busy !== null}
                    onClick={() => run('order', onOrderNow, 'Taking you to checkout.')}
                    className="rounded-lg bg-[#FAB40A] px-6 py-3.5 text-[15px] font-extrabold text-[#152B4D] transition-colors hover:bg-[#e6a409] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#152B4D] disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {busy === 'order' ? 'Please wait...' : 'Order Now'}
                </button>
                <button
                    type="button"
                    disabled={outOfStock || busy !== null}
                    onClick={() => run('cart', onAddToCart, 'Added to your cart.')}
                    className="inline-flex items-center justify-center gap-2 rounded-lg border-[1.5px] border-[#325A96] px-6 py-3.5 text-[15px] font-extrabold text-[#325A96] transition-colors hover:bg-[#325A96] hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96] disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <ShoppingCart className="h-[18px] w-[18px]" aria-hidden="true" />
                    {busy === 'cart' ? 'Adding...' : 'Add to Cart'}
                </button>
            </div>

            <p role="status" aria-live="polite" className={notice ? `text-[13px] font-semibold ${notice.kind === 'success' ? 'text-[#1E9E5A]' : 'text-[#C0292B]'}` : 'sr-only'}>
                {notice?.text}
            </p>

            <button
                type="button"
                onClick={onAskOnWhatsApp}
                className="inline-flex items-center justify-center gap-2.5 rounded-lg bg-[#1FA855] px-6 py-3.5 text-[15px] font-extrabold text-white transition-colors hover:bg-[#188a45] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1FA855]"
            >
                <WhatsAppIcon className="h-5 w-5" />
                Ask About This Product on WhatsApp
            </button>

            <div className="flex items-start gap-3 rounded-xl bg-[#F4F5F7] p-3.5">
                <span className="shrink-0 rounded-md bg-[#1FA855] px-2.5 py-1 text-[11.5px] font-extrabold tracking-wide text-white">{productCopy.paymentMethod}</span>
                <p className="text-[12.5px] leading-relaxed text-[#5B6470]">{productCopy.paymentNote}</p>
            </div>

            <ul className="grid grid-cols-2 gap-x-4 gap-y-2.5">
                {productTrust.map(({ id, label, icon: Icon }) => (
                    <li key={id} className="flex items-center gap-2 text-[13px] font-semibold text-[#2B2B2B]">
                        <Icon className="h-4 w-4 shrink-0 text-[#1E9E5A]" aria-hidden="true" />
                        {label}
                    </li>
                ))}
            </ul>

            {product.tags.length > 0 && (
                <ul aria-label="Tags" className="flex flex-wrap gap-2 border-t border-[#E4E7EC] pt-4">
                    {product.tags.map((tag) => (
                        <li key={tag} className="inline-flex items-center gap-1 rounded-full bg-[#EAF0F9] px-3 py-1 text-[12px] font-semibold text-[#325A96]">
                            <Check className="h-3 w-3" strokeWidth={3} aria-hidden="true" />
                            {tag}
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
}
