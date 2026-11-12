import { useRef } from 'react';
import type { KeyboardEvent } from 'react';
import { Check } from 'lucide-react';
import type { NewReview, ProductDetail } from '@/types/product-detail';
import { ReviewsPanel } from './ReviewsPanel';

export type ProductTabId = 'description' | 'specs' | 'reviews' | 'shipping';

interface ProductTabsProps {
    product: ProductDetail;
    active: ProductTabId;
    onChange: (tab: ProductTabId) => void;
    onSubmitReview?: (review: NewReview) => Promise<void>;
}

/**
 * Description / Specifications / Reviews / Shipping & Warranty, following
 * the WAI-ARIA tabs pattern (roving tabindex, Left/Right/Home/End). The
 * active tab is controlled by the page so that "86 reviews" next to the
 * rating can jump straight to the Reviews tab.
 */
export function ProductTabs({ product, active, onChange, onSubmitReview }: ProductTabsProps) {
    const refs = useRef<Partial<Record<ProductTabId, HTMLButtonElement | null>>>({});
    const reviewCount = product.reviewCount ?? product.reviews.length;

    const tabs: { id: ProductTabId; label: string }[] = [
        { id: 'description', label: 'Description' },
        { id: 'specs', label: 'Specifications' },
        { id: 'reviews', label: `Reviews (${reviewCount})` },
        { id: 'shipping', label: 'Shipping & Warranty' },
    ];

    function handleKeyDown(event: KeyboardEvent<HTMLDivElement>) {
        const current = tabs.findIndex((t) => t.id === active);
        let next = current;
        if (event.key === 'ArrowRight') next = (current + 1) % tabs.length;
        else if (event.key === 'ArrowLeft') next = (current - 1 + tabs.length) % tabs.length;
        else if (event.key === 'Home') next = 0;
        else if (event.key === 'End') next = tabs.length - 1;
        else return;
        event.preventDefault();
        onChange(tabs[next].id);
        refs.current[tabs[next].id]?.focus();
    }

    return (
        <section id="product-tabs" aria-label="Product information" className="scroll-mt-4 rounded-2xl border border-[#E4E7EC] bg-white">
            <div
                role="tablist"
                aria-label="Product information"
                onKeyDown={handleKeyDown}
                className="flex overflow-x-auto border-b border-[#E4E7EC] px-2 [scrollbar-width:none] sm:px-4 [&::-webkit-scrollbar]:hidden"
            >
                {tabs.map((tab) => {
                    const selected = tab.id === active;
                    return (
                        <button
                            key={tab.id}
                            ref={(el) => {
                                refs.current[tab.id] = el;
                            }}
                            id={`pd-tab-${tab.id}`}
                            type="button"
                            role="tab"
                            aria-selected={selected}
                            aria-controls="pd-panel"
                            tabIndex={selected ? 0 : -1}
                            onClick={() => onChange(tab.id)}
                            className={`-mb-px shrink-0 whitespace-nowrap border-b-[3px] px-4 py-4 text-[14px] font-bold transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-[#325A96] ${
                                selected ? 'border-[#FAB40A] text-[#152B4D]' : 'border-transparent text-[#5B6470] hover:text-[#325A96]'
                            }`}
                        >
                            {tab.label}
                        </button>
                    );
                })}
            </div>

            <div id="pd-panel" role="tabpanel" aria-labelledby={`pd-tab-${active}`} tabIndex={0} className="p-5 outline-none focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-[#325A96] sm:p-7">
                {active === 'description' && (
                    <div className="max-w-3xl">
                        <h3 className="mb-2 text-[17px] font-extrabold text-[#152B4D]">Overview</h3>
                        <p className="mb-6 text-[14.5px] leading-relaxed text-[#2B2B2B]">{product.overview}</p>
                        {product.highlights.length > 0 && (
                            <>
                                <h3 className="mb-3 text-[17px] font-extrabold text-[#152B4D]">Key Highlights</h3>
                                <ul className="grid gap-2.5">
                                    {product.highlights.map((item) => (
                                        <li key={item} className="flex items-start gap-2.5 text-[14.5px] leading-relaxed text-[#2B2B2B]">
                                            <Check className="mt-1 h-4 w-4 shrink-0 text-[#1E9E5A]" strokeWidth={3} aria-hidden="true" />
                                            {item}
                                        </li>
                                    ))}
                                </ul>
                            </>
                        )}
                    </div>
                )}

                {active === 'specs' && (
                    <table className="w-full max-w-3xl border-collapse text-left text-[14px]">
                        <caption className="sr-only">Specifications for {product.name}</caption>
                        <tbody>
                            {product.specTable.map((spec) => (
                                <tr key={spec.label} className="border-b border-[#E4E7EC] odd:bg-[#F9FAFB]">
                                    <th scope="row" className="w-2/5 px-4 py-3 font-bold text-[#152B4D]">
                                        {spec.label}
                                    </th>
                                    <td className="px-4 py-3 text-[#2B2B2B]">{spec.value}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}

                {active === 'reviews' && <ReviewsPanel product={product} onSubmitReview={onSubmitReview} />}

                {active === 'shipping' && (
                    <div className="max-w-3xl">
                        {product.shippingInfo.map((section) => (
                            <div key={section.title} className="mb-5 last:mb-0">
                                <h3 className="mb-2 text-[17px] font-extrabold text-[#152B4D]">{section.title}</h3>
                                <p className="text-[14.5px] leading-relaxed text-[#2B2B2B]">{section.body}</p>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </section>
    );
}
