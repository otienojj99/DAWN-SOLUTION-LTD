import type { QuoteRequest } from '@/types/forms';
import { NewsletterPanel } from './NewsletterPanel';
import { QuotePanel } from './QuotePanel';

interface NewsletterQuoteSectionProps {
    onSubscribe?: (email: string) => Promise<void>;
    onRequestQuote?: (request: QuoteRequest) => Promise<void>;
}

/** Newsletter signup + business quotation request, side by side on desktop and stacked on mobile. */
export function NewsletterQuoteSection({ onSubscribe, onRequestQuote }: NewsletterQuoteSectionProps) {
    return (
        <div className="mx-auto max-w-[1280px] px-4 py-10 sm:px-6 sm:py-14">
            <div className="grid overflow-hidden rounded-2xl shadow-[0_8px_30px_rgba(21,43,77,0.12)] lg:grid-cols-2">
                <NewsletterPanel onSubscribe={onSubscribe} />
                <QuotePanel onRequestQuote={onRequestQuote} />
            </div>
        </div>
    );
}
