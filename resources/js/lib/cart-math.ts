import { commerceCopy, MAX_LINE_QUANTITY, NAIROBI_SHIPPING_FEE, VAT_RATE } from '@/data/commerce';
import { formatPrice } from '@/lib/format';
import type { CartLine, CartTotals, DeliveryArea, NewCartLine } from '@/types/cart';

/** Pure money and cart rules. No React, no storage, so they are easy to test and to mirror in PHP. */

export function lineKeyFor(line: Pick<NewCartLine, 'productId' | 'sku'>): string {
    return line.sku ?? line.productId;
}

export function clampQuantity(quantity: number, max?: number): number {
    const upper = max !== undefined && max > 0 ? Math.min(max, MAX_LINE_QUANTITY) : MAX_LINE_QUANTITY;
    if (!Number.isFinite(quantity)) return 1;
    return Math.min(upper, Math.max(1, Math.floor(quantity)));
}

export function lineTotal(line: Pick<CartLine, 'unitPrice' | 'quantity'>): number {
    return line.unitPrice * line.quantity;
}

/**
 * Subtotal, VAT, delivery and total.
 *
 *  - VAT is 16% of the whole subtotal, rounded once to the nearest shilling
 *    (not per line), so the numbers on screen always add up.
 *  - `area` undefined (cart drawer and cart page): no delivery line yet.
 *  - 'nairobi': flat KES 1,000 is added.
 *  - 'outside': no fee is added; it is agreed with the customer first.
 *  - An empty cart has no delivery fee.
 */
export function calculateTotals(lines: CartLine[], area?: DeliveryArea): CartTotals {
    const itemCount = lines.reduce((sum, line) => sum + line.quantity, 0);
    const subtotal = lines.reduce((sum, line) => sum + lineTotal(line), 0);
    const vat = Math.round(subtotal * VAT_RATE);

    let shipping = 0;
    let shippingStatus: CartTotals['shippingStatus'] = 'not-chosen';
    if (itemCount > 0 && area === 'nairobi') {
        shipping = NAIROBI_SHIPPING_FEE;
        shippingStatus = 'included';
    } else if (itemCount > 0 && area === 'outside') {
        shippingStatus = 'to-confirm';
    }

    return { itemCount, subtotal, vat, shipping, shippingStatus, total: subtotal + vat + shipping };
}

/** Message the buyer sends on WhatsApp from the cart: the list of items and the total with VAT. */
export function buildCartWhatsAppMessage(lines: CartLine[], area?: DeliveryArea): string {
    const totals = calculateTotals(lines, area);
    const items = lines.map((line, index) => {
        const options = line.variantSummary ? ` (${line.variantSummary})` : '';
        return `${index + 1}. ${line.name}${options} x${line.quantity} - ${formatPrice(lineTotal(line))}`;
    });

    const delivery =
        totals.shippingStatus === 'included'
            ? `Delivery (Nairobi): ${formatPrice(totals.shipping)}`
            : totals.shippingStatus === 'to-confirm'
              ? 'Delivery: to be confirmed'
              : commerceCopy.deliveryNote;

    return [
        commerceCopy.whatsappGreeting,
        '',
        ...items,
        '',
        `Subtotal: ${formatPrice(totals.subtotal)}`,
        `${commerceCopy.vatLabel}: ${formatPrice(totals.vat)}`,
        `Total (VAT included): ${formatPrice(totals.subtotal + totals.vat)}`,
        delivery,
        ...(totals.shippingStatus === 'included' ? [`Total with delivery: ${formatPrice(totals.total)}`] : []),
        '',
        commerceCopy.whatsappClosing,
    ].join('\n');
}

/** Checks anything read back from localStorage before trusting it, so stale or edited data can't break the page. */
export function sanitizeLines(raw: unknown): CartLine[] {
    if (!Array.isArray(raw)) return [];
    const lines: CartLine[] = [];
    for (const item of raw) {
        if (!item || typeof item !== 'object') continue;
        const l = item as Partial<CartLine>;
        const ok =
            typeof l.key === 'string' &&
            typeof l.productId === 'string' &&
            typeof l.slug === 'string' &&
            typeof l.name === 'string' &&
            typeof l.image === 'string' &&
            typeof l.unitPrice === 'number' &&
            Number.isFinite(l.unitPrice) &&
            l.unitPrice >= 0 &&
            typeof l.quantity === 'number';
        if (!ok) continue;
        lines.push({
            key: l.key!,
            productId: l.productId!,
            slug: l.slug!,
            name: l.name!,
            brand: typeof l.brand === 'string' ? l.brand : '',
            image: l.image!,
            unitPrice: l.unitPrice!,
            quantity: clampQuantity(l.quantity!, l.maxQuantity),
            variantSummary: typeof l.variantSummary === 'string' ? l.variantSummary : undefined,
            sku: typeof l.sku === 'string' ? l.sku : undefined,
            maxQuantity: typeof l.maxQuantity === 'number' ? l.maxQuantity : undefined,
        });
    }
    return lines;
}

/* ---------------- Reducer ---------------- */

export type CartAction =
    | { type: 'hydrate'; lines: CartLine[] }
    | { type: 'add'; line: NewCartLine; quantity: number }
    | { type: 'setQuantity'; key: string; quantity: number }
    | { type: 'remove'; key: string }
    | { type: 'clear' };

export function cartReducer(lines: CartLine[], action: CartAction): CartLine[] {
    switch (action.type) {
        case 'hydrate':
            return action.lines;
        case 'add': {
            const key = lineKeyFor(action.line);
            const existing = lines.find((l) => l.key === key);
            if (existing) {
                // Re-adding refreshes the display data and the price, then adds to the quantity.
                return lines.map((l) => (l.key === key ? { ...l, ...action.line, key, quantity: clampQuantity(l.quantity + action.quantity, action.line.maxQuantity) } : l));
            }
            return [...lines, { ...action.line, key, quantity: clampQuantity(action.quantity, action.line.maxQuantity) }];
        }
        case 'setQuantity':
            return lines.map((l) => (l.key === action.key ? { ...l, quantity: clampQuantity(action.quantity, l.maxQuantity) } : l));
        case 'remove':
            return lines.filter((l) => l.key !== action.key);
        case 'clear':
            return [];
    }
}
