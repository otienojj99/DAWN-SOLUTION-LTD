/**
 * Pricing rules and checkout copy in one place. Change a rate or a fee here
 * and the cart drawer, cart page, checkout and the WhatsApp message all follow.
 *
 * IMPORTANT: these numbers drive what the buyer SEES. When an order is
 * placed, your Laravel backend must recalculate prices, VAT and shipping
 * from its own product records and these same rules; never trust the
 * totals or unit prices sent from the browser.
 */

/** Value-added tax. Product prices in the shop are VAT-exclusive; VAT is added in the cart. */
export const VAT_RATE = 0.16;

/** Flat delivery fee within Nairobi, in KES. Outside Nairobi the fee is agreed with the customer. */
export const NAIROBI_SHIPPING_FEE = 1000;

export const commerceCopy = {
    vatLabel: 'VAT (16%)',
    vatNote: 'Prices exclude 16% VAT, which is added in your cart',
    deliveryNote: 'Delivery: KES 1,000 within Nairobi. Outside Nairobi, we confirm the cost with you.',
    deliveryShort: 'Delivery KES 1,000 within Nairobi',
    outsideNairobiNote: 'We will contact you to agree the delivery cost for your location before you pay.',
    paymentMethod: 'M-PESA',
    paymentNote: 'Pay with M-Pesa. A payment prompt is sent to your phone after you place the order.',
    whatsappGreeting: 'Hello Dawn Solution, I would like to order the following:',
    whatsappClosing: 'Please confirm availability and delivery.',
};

export const CART_STORAGE_KEY = 'dsl-cart-v1';
export const WISHLIST_STORAGE_KEY = 'dsl-wishlist-v1';

/** Highest quantity of one line when a product has no stock count. */
export const MAX_LINE_QUANTITY = 99;
