import { CheckCircle2, Smartphone } from 'lucide-react';
import { useEffect, useId, useMemo, useState } from 'react';
import type { FormEvent } from 'react';
import { TextAreaField, TextField } from '@/components/forms/Field';
import { commerceCopy, NAIROBI_SHIPPING_FEE } from '@/data/commerce';
import { useSubmitStatus } from '@/hooks/useSubmitStatus';
import { calculateTotals } from '@/lib/cart-math';
import { formatPrice } from '@/lib/format';
import { emailError, phoneError } from '@/lib/validation';
import { useAuth } from '@/store/auth';
import { useCart } from '@/store/cart';
import type { CheckoutOrder, DeliveryArea } from '@/types/cart';
import { CartSummary } from './CartSummary';

interface CheckoutPageProps {
    /**
     * Sends the order to your backend (which must recompute prices, VAT and
     * delivery itself) and starts the M-Pesa prompt. Resolve with an order
     * number, or throw an Error(message) to show the problem to the buyer.
     * Without it, placing an order says it is not connected yet.
     */
    onPlaceOrder?: (order: CheckoutOrder) => Promise<{ orderNumber?: string } | void>;
}

type Errors = Partial<Record<'fullName' | 'phone' | 'email' | 'town' | 'address' | 'mpesaPhone', string>>;

/** Checkout: contact, delivery area (Nairobi KES 1,000 or outside, agreed with the client), M-Pesa payment and a live summary. */
export function CheckoutPage({ onPlaceOrder }: CheckoutPageProps) {
    const cart = useCart();
    const { user } = useAuth();
    const uid = useId();
    const { status, message, submit, isSubmitting } = useSubmitStatus();

    const [fullName, setFullName] = useState('');
    const [phone, setPhone] = useState('');
    const [email, setEmail] = useState('');
    const [area, setArea] = useState<DeliveryArea>('nairobi');
    const [town, setTown] = useState('');
    const [address, setAddress] = useState('');
    const [notes, setNotes] = useState('');
    const [mpesaPhone, setMpesaPhone] = useState('');
    const [sameAsPhone, setSameAsPhone] = useState(true);
    const [errors, setErrors] = useState<Errors>({});
    const [orderNumber, setOrderNumber] = useState<string | undefined>();
    const [placedTotal, setPlacedTotal] = useState<ReturnType<typeof calculateTotals> | null>(null);

    // Fill in what we know about a signed-in customer, without overwriting what they have typed.
    useEffect(() => {
        if (!user) return;
        setFullName((v) => v || user.name);
        setEmail((v) => v || user.email);
    }, [user]);

    useEffect(() => {
        document.title = 'Checkout | Dawn Solution';
    }, []);

    const totals = useMemo(() => calculateTotals(cart.lines, area), [cart.lines, area]);

    function validate(): Errors {
        const next: Errors = {};
        if (!fullName.trim()) next.fullName = 'Enter your full name.';
        next.phone = phoneError(phone);
        next.email = emailError(email);
        if (!town.trim()) next.town = area === 'nairobi' ? 'Enter your area, e.g. Westlands.' : 'Enter your town.';
        if (!address.trim()) next.address = 'Enter where we should deliver.';
        if (!sameAsPhone) next.mpesaPhone = phoneError(mpesaPhone);
        return Object.fromEntries(Object.entries(next).filter(([, v]) => v)) as Errors;
    }

    async function onSubmit(e: FormEvent) {
        e.preventDefault();
        if (isSubmitting || cart.lines.length === 0) return;
        const found = validate();
        setErrors(found);
        if (Object.keys(found).length) {
            document.getElementById(`${uid}-form`)?.querySelector<HTMLElement>('[aria-invalid="true"]')?.focus();
            return;
        }
        await submit(async () => {
            // Never pretend an order went through.
            if (!onPlaceOrder) throw new Error('Placing orders is not connected yet. Please order on WhatsApp instead.');
            const order: CheckoutOrder = {
                customer: { fullName: fullName.trim(), phone: phone.trim(), email: email.trim() },
                delivery: { area, address: address.trim(), town: town.trim(), notes: notes.trim() },
                mpesaPhone: (sameAsPhone ? phone : mpesaPhone).trim(),
                lines: cart.lines,
                totals,
            };
            const result = await onPlaceOrder(order);
            setOrderNumber(result?.orderNumber);
            setPlacedTotal(totals);
            cart.clear();
        });
    }

    if (status === 'success') {
        return (
            <div className="mx-auto max-w-[640px] px-4 py-16 text-center sm:px-6">
                <CheckCircle2 className="mx-auto mb-4 h-14 w-14 text-[#1E9E5A]" aria-hidden="true" />
                <h1 className="mb-2 text-[26px] font-extrabold text-[#152B4D]">Order placed</h1>
                {orderNumber && <p className="mb-2 text-[15px] font-bold text-[#325A96]">Order {orderNumber}</p>}
                <p role="status" className="mb-6 text-[14.5px] text-[#5B6470]">
                    {area === 'nairobi' ? 'Check your phone for the M-Pesa prompt to complete payment.' : `Thank you. ${commerceCopy.outsideNairobiNote}`}
                    {placedTotal && ` Total: ${formatPrice(placedTotal.total)}${placedTotal.shippingStatus === 'to-confirm' ? ' plus delivery' : ''}.`}
                </p>
                <a href="/shop" className="inline-block rounded-lg bg-[#325A96] px-6 py-3 text-[14px] font-bold text-white hover:bg-[#FAB40A] hover:text-[#152B4D]">
                    Continue shopping
                </a>
            </div>
        );
    }

    if (cart.lines.length === 0) {
        return (
            <div className="mx-auto max-w-[640px] px-4 py-16 text-center sm:px-6">
                <h1 className="mb-2 text-[26px] font-extrabold text-[#152B4D]">Your cart is empty</h1>
                <p className="mb-6 text-[14.5px] text-[#5B6470]">Add something to your cart before checking out.</p>
                <a href="/shop" className="inline-block rounded-lg bg-[#325A96] px-6 py-3 text-[14px] font-bold text-white hover:bg-[#FAB40A] hover:text-[#152B4D]">
                    Browse the shop
                </a>
            </div>
        );
    }

    const radio = (value: DeliveryArea, title: string, detail: string, price: string) => (
        <label key={value} className={`flex cursor-pointer items-start gap-3 rounded-lg border-[1.5px] p-3.5 transition-colors has-[:focus-visible]:outline has-[:focus-visible]:outline-2 has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-[#325A96] ${area === value ? 'border-[#325A96] bg-[#EAF0F9]' : 'border-[#D5DAE1] hover:border-[#325A96]/50'}`}>
            <input type="radio" name={`${uid}-area`} value={value} checked={area === value} onChange={() => setArea(value)} className="mt-1 h-4 w-4 accent-[#325A96]" />
            <span className="flex-1">
                <span className="block text-[14px] font-bold text-[#152B4D]">{title}</span>
                <span className="block text-[12.5px] text-[#5B6470]">{detail}</span>
            </span>
            <span className="text-[13.5px] font-extrabold text-[#152B4D]">{price}</span>
        </label>
    );

    return (
        <div className="mx-auto max-w-[1100px] px-4 pb-16 pt-8 sm:px-6">
            <h1 className="mb-6 text-[26px] font-extrabold text-[#152B4D] md:text-[30px]">Checkout</h1>

            <form id={`${uid}-form`} onSubmit={onSubmit} noValidate className="grid gap-8 lg:grid-cols-[minmax(0,1fr)_360px]">
                <div className="space-y-8">
                    <section aria-labelledby={`${uid}-contact`} className="space-y-4">
                        <h2 id={`${uid}-contact`} className="text-[17px] font-extrabold text-[#152B4D]">1. Your details</h2>
                        <TextField id={`${uid}-name`} label="Full name" autoComplete="name" value={fullName} onChange={(e) => setFullName(e.target.value)} error={errors.fullName} />
                        <div className="grid gap-4 sm:grid-cols-2">
                            <TextField id={`${uid}-phone`} label="Phone" type="tel" autoComplete="tel" value={phone} onChange={(e) => setPhone(e.target.value)} error={errors.phone} />
                            <TextField id={`${uid}-email`} label="Email" type="email" autoComplete="email" value={email} onChange={(e) => setEmail(e.target.value)} error={errors.email} />
                        </div>
                    </section>

                    <fieldset className="space-y-3">
                        <legend className="mb-3 text-[17px] font-extrabold text-[#152B4D]">2. Delivery</legend>
                        {radio('nairobi', 'Within Nairobi', 'Delivered in 1–2 business days', formatPrice(NAIROBI_SHIPPING_FEE))}
                        {radio('outside', 'Outside Nairobi', commerceCopy.outsideNairobiNote, 'To be confirmed')}
                        <div className="grid gap-4 pt-2 sm:grid-cols-2">
                            <TextField id={`${uid}-town`} label={area === 'nairobi' ? 'Area / estate' : 'Town'} autoComplete="address-level2" value={town} onChange={(e) => setTown(e.target.value)} error={errors.town} />
                            <TextField id={`${uid}-address`} label="Street, building, landmark" autoComplete="street-address" value={address} onChange={(e) => setAddress(e.target.value)} error={errors.address} />
                        </div>
                        <TextAreaField id={`${uid}-notes`} label="Delivery notes" optional rows={3} value={notes} onChange={(e) => setNotes(e.target.value)} />
                    </fieldset>

                    <section aria-labelledby={`${uid}-pay`} className="space-y-3">
                        <h2 id={`${uid}-pay`} className="text-[17px] font-extrabold text-[#152B4D]">3. Payment</h2>
                        <div className="flex items-center gap-3 rounded-lg border-[1.5px] border-[#1E9E5A] bg-[#EEF9F3] p-3.5">
                            <Smartphone className="h-6 w-6 text-[#1E9E5A]" aria-hidden="true" />
                            <div>
                                <p className="text-[14px] font-extrabold text-[#152B4D]">{commerceCopy.paymentMethod}</p>
                                <p className="text-[12.5px] text-[#5B6470]">{commerceCopy.paymentNote}</p>
                            </div>
                        </div>
                        <label className="flex items-center gap-2 text-[13.5px] text-[#2B2B2B]">
                            <input type="checkbox" checked={sameAsPhone} onChange={(e) => setSameAsPhone(e.target.checked)} className="h-4 w-4 accent-[#325A96]" />
                            Pay with the phone number above
                        </label>
                        {!sameAsPhone && <TextField id={`${uid}-mpesa`} label="M-Pesa phone number" type="tel" value={mpesaPhone} onChange={(e) => setMpesaPhone(e.target.value)} error={errors.mpesaPhone} />}
                    </section>
                </div>

                <aside aria-label="Order summary" className="h-fit space-y-4 rounded-xl border border-[#E4E7EC] bg-[#F9FAFB] p-5 lg:sticky lg:top-4">
                    <h2 className="text-[16px] font-extrabold text-[#152B4D]">Order summary</h2>
                    <ul className="divide-y divide-[#E4E7EC]">
                        {cart.lines.map((line) => (
                            <li key={line.key} className="flex gap-3 py-2.5">
                                <img src={line.image} alt="" className="h-12 w-12 shrink-0 rounded-md bg-white object-cover" />
                                <div className="min-w-0 flex-1">
                                    <p className="line-clamp-2 text-[12.5px] font-bold leading-snug text-[#2B2B2B]">{line.name}</p>
                                    <p className="text-[11.5px] text-[#5B6470]">Qty {line.quantity}</p>
                                </div>
                                <p className="text-[13px] font-bold text-[#152B4D]">{formatPrice(line.unitPrice * line.quantity)}</p>
                            </li>
                        ))}
                    </ul>
                    <CartSummary totals={totals} showDelivery />
                    {status === 'error' && message && (
                        <p role="alert" className="rounded-md bg-[#FDECEC] px-3 py-2 text-[12.5px] font-semibold text-[#C0292B]">
                            {message}
                        </p>
                    )}
                    <button type="submit" disabled={isSubmitting} className="flex w-full items-center justify-center rounded-lg bg-[#325A96] px-4 py-3 text-[14px] font-bold text-white transition-colors hover:bg-[#FAB40A] hover:text-[#152B4D] disabled:cursor-wait disabled:opacity-70">
                        {isSubmitting ? 'Placing order…' : 'Place order & pay with M-Pesa'}
                    </button>
                    <p className="text-center text-[11.5px] text-[#5B6470]">{commerceCopy.vatNote}</p>
                </aside>
            </form>
        </div>
    );
}
