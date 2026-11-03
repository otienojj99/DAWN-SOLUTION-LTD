/** Formats a price for display, e.g. formatPrice(98000) → "KES 98,000". */
export function formatPrice(amount: number, currency = 'KES'): string {
    return `${currency} ${amount.toLocaleString('en-KE')}`;
}

/** Percentage saved between an old and current price, rounded. Returns null when there is no discount. */
export function discountPercent(price: number, oldPrice?: number): number | null {
    if (!oldPrice || oldPrice <= price) return null;
    return Math.round(((oldPrice - price) / oldPrice) * 100);
}
