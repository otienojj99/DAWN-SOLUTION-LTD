export function formatPrice(amount: number, currency: 'KES' | 'USD' = 'KES'): string {
    return `${currency} ${amount.toLocaleString('en-KE')}`;
}


export function discountPercent(price: number, oldPrice?: number): number | null {
    if (!oldPrice || oldPrice <= price) return null;
    return Math.round(((oldPrice - price) / oldPrice) * 100);
}