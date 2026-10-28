import { ClipboardList, Phone } from 'lucide-react';
import { contactInfo } from '@/data/contact';
import { quoteCopy } from '@/data/cta';
import type { QuoteRequest } from '@/types/forms';
import { QuoteForm } from './QuoteForm';

interface QuotePanelProps {
    onRequestQuote?: (request: QuoteRequest) => Promise<void>;
}

/** Right (light) half of the section: business quotation request. */
export function QuotePanel({ onRequestQuote }: QuotePanelProps) {
    const phone = contactInfo.phones[0];

    return (
        <section aria-labelledby="quote-title" className="bg-[#F1F4F8] p-7 sm:p-10">
            <span className="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#325A96] text-white">
                <ClipboardList className="h-6 w-6" aria-hidden="true" />
            </span>
            <h2 id="quote-title" className="mb-2 text-2xl font-extrabold leading-tight text-[#152B4D] sm:text-[28px]">
                {quoteCopy.title}
            </h2>
            <p className="mb-6 max-w-[480px] text-[14px] leading-relaxed text-[#5B6470]">{quoteCopy.description}</p>

            <QuoteForm onRequestQuote={onRequestQuote} />

            {phone && (
                <p className="mt-5 flex flex-wrap items-center gap-x-2 text-[13px] text-[#5B6470]">
                    <span>{quoteCopy.callPrompt}</span>
                    <a
                        href={`tel:${phone.dial}`}
                        className="inline-flex items-center gap-1.5 font-bold text-[#325A96] underline-offset-4 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#325A96]"
                    >
                        <Phone className="h-3.5 w-3.5" aria-hidden="true" />
                        {quoteCopy.callLabel}: {phone.display}
                    </a>
                </p>
            )}
        </section>
    );
}
