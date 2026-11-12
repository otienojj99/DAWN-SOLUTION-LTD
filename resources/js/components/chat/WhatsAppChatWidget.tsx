import { Send, X } from 'lucide-react';
import { useEffect, useId, useRef, useState } from 'react';
import type { FormEvent } from 'react';
import { contactInfo } from '@/data/contact';
import { whatsappChat } from '@/data/product-config';
import { whatsappUrl } from '@/lib/whatsapp';
import { WhatsAppIcon } from './WhatsAppIcon';

interface WhatsAppChatWidgetProps {
    /** Controlled open state. Lets other buttons on the page (e.g. "Ask about this product") open the window. */
    open: boolean;
    onOpenChange: (open: boolean) => void;
    /** Text the message box starts with. Re-applied each time the window is opened. */
    initialMessage: string;
    /** Added after the message when sending, e.g. the product page URL. Evaluated at send time. */
    messageSuffix?: () => string;
    /** Company WhatsApp number in international format without "+". Defaults to the one in data/contact.ts. */
    number?: string;
}

/**
 * Small floating WhatsApp chat window, like the WooCommerce chat plugins:
 * a green bubble in the corner opens a compact card with a greeting and a
 * message box. "Start chat" opens WhatsApp (app or web) with that message
 * already written, so the visitor only has to press send there.
 *
 * (A normal wa.me link is the only way to start a WhatsApp chat without the
 * WhatsApp Business API, so the conversation itself always continues in WhatsApp.)
 *
 * Non-modal: the page behind stays usable. Escape closes it and returns
 * focus to the bubble.
 */
export function WhatsAppChatWidget({ open, onOpenChange, initialMessage, messageSuffix, number = contactInfo.whatsapp.number }: WhatsAppChatWidgetProps) {
    const [message, setMessage] = useState(initialMessage);
    const bubbleRef = useRef<HTMLButtonElement>(null);
    const textareaRef = useRef<HTMLTextAreaElement>(null);
    const wasOpen = useRef(false);
    const panelId = useId();

    // Each time the window opens, start from the current suggested message and put the cursor in the box.
    useEffect(() => {
        if (open) {
            setMessage(initialMessage);
            const t = window.setTimeout(() => textareaRef.current?.focus(), 80);
            wasOpen.current = true;
            return () => window.clearTimeout(t);
        }
        if (wasOpen.current) {
            wasOpen.current = false;
            bubbleRef.current?.focus();
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [open]);

    // Escape closes the window.
    useEffect(() => {
        if (!open) return;
        const onKey = (event: KeyboardEvent) => event.key === 'Escape' && onOpenChange(false);
        document.addEventListener('keydown', onKey);
        return () => document.removeEventListener('keydown', onKey);
    }, [open, onOpenChange]);

    function handleSubmit(event: FormEvent) {
        event.preventDefault();
        const text = message.trim();
        if (!text) return;
        const suffix = messageSuffix?.();
        window.open(whatsappUrl(suffix ? `${text}\n\n${suffix}` : text, number), '_blank', 'noopener,noreferrer');
        onOpenChange(false);
    }

    return (
        <div className="fixed bottom-4 right-4 z-[80] flex flex-col items-end gap-3 sm:bottom-6 sm:right-6">
            <div
                id={panelId}
                role="dialog"
                aria-label={`Chat with ${whatsappChat.agentName} on WhatsApp`}
                aria-hidden={!open}
                className={`w-[min(92vw,340px)] origin-bottom-right overflow-hidden rounded-2xl bg-white shadow-[0_18px_50px_rgba(21,43,77,0.3)] transition-[opacity,transform,visibility] duration-200 motion-reduce:transition-none ${
                    open ? 'visible scale-100 opacity-100' : 'invisible pointer-events-none scale-95 opacity-0'
                }`}
            >
                <div className="flex items-center gap-3 bg-[#075E54] px-4 py-3.5 text-white">
                    <span className="relative flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/15">
                        <WhatsAppIcon className="h-6 w-6" />
                        <span className="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-[#075E54] bg-[#4ADE80]" aria-hidden="true" />
                    </span>
                    <div className="min-w-0 flex-1">
                        <p className="truncate text-[14.5px] font-extrabold leading-tight">{whatsappChat.agentName}</p>
                        <p className="truncate text-[12px] text-white/80">{whatsappChat.replyTime}</p>
                    </div>
                    <button
                        type="button"
                        onClick={() => onOpenChange(false)}
                        aria-label="Close chat window"
                        className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white/85 hover:bg-white/15 focus-visible:outline focus-visible:outline-2 focus-visible:outline-white"
                    >
                        <X className="h-4 w-4" aria-hidden="true" />
                    </button>
                </div>

                <div className="bg-[#ECE5DD] px-4 py-4">
                    <p className="max-w-[85%] rounded-xl rounded-tl-sm bg-white px-3.5 py-2.5 text-[13.5px] leading-relaxed text-[#2B2B2B] shadow-sm">{whatsappChat.greeting}</p>
                </div>

                <form onSubmit={handleSubmit} className="grid gap-2.5 bg-white p-3.5">
                    <label htmlFor={`${panelId}-msg`} className="sr-only">
                        Your message
                    </label>
                    <textarea
                        id={`${panelId}-msg`}
                        ref={textareaRef}
                        rows={4}
                        value={message}
                        onChange={(e) => setMessage(e.target.value)}
                        placeholder={whatsappChat.placeholder}
                        className="w-full resize-none rounded-lg border-[1.5px] border-[#D5DAE1] px-3 py-2.5 text-[13.5px] text-[#2B2B2B] outline-none placeholder:text-[#8A93A3] focus:border-[#1FA855] focus:ring-2 focus:ring-[#1FA855]/20"
                    />
                    <button
                        type="submit"
                        disabled={!message.trim()}
                        className="inline-flex items-center justify-center gap-2 rounded-lg bg-[#1FA855] px-4 py-2.5 text-[14px] font-extrabold text-white transition-colors hover:bg-[#188a45] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1FA855] disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <Send className="h-4 w-4" aria-hidden="true" />
                        {whatsappChat.startLabel}
                    </button>
                    <p className="text-center text-[11px] text-[#5B6470]">Opens WhatsApp with your message ready to send.</p>
                </form>
            </div>

            <button
                ref={bubbleRef}
                type="button"
                onClick={() => onOpenChange(!open)}
                aria-expanded={open}
                aria-controls={panelId}
                aria-label={open ? 'Close WhatsApp chat' : whatsappChat.openLabel}
                className="flex h-14 w-14 items-center justify-center rounded-full bg-[#1FA855] text-white shadow-[0_8px_24px_rgba(31,168,85,0.45)] transition-transform duration-200 hover:scale-105 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#1FA855] motion-reduce:transition-none motion-reduce:hover:scale-100"
            >
                {open ? <X className="h-6 w-6" aria-hidden="true" /> : <WhatsAppIcon className="h-7 w-7" />}
            </button>
        </div>
    );
}
