import { X } from 'lucide-react';
import { useEffect, useRef } from 'react';
import type { KeyboardEvent, ReactNode } from 'react';

interface FilterDrawerProps {
    open: boolean;
    onClose: () => void;
    /** Text for the footer button on mobile, e.g. "Show 14 products". */
    applyLabel: string;
    children: ReactNode;
}

const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), select, textarea, summary, [tabindex]:not([tabindex="-1"])';

/**
 * The shop sidebar. On large screens it is simply a sticky column; below
 * `lg` it becomes a slide-in drawer opened from the "Filters" button. It is
 * the SAME element at every size (so the filters exist once in the DOM, with
 * unique ids), only restyled:
 *  - closed on mobile: moved off-screen and `invisible`, so it can't be tabbed into
 *  - open on mobile: behaves as a modal dialog: focus moves in and is trapped,
 *    Escape closes it, the page behind can't scroll
 */
export function FilterDrawer({ open, onClose, applyLabel, children }: FilterDrawerProps) {
    const ref = useRef<HTMLElement>(null);

    useEffect(() => {
        if (!open) return;
        const previouslyFocused = document.activeElement as HTMLElement | null;
        const previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';

        // The drawer is `invisible` until its slide-in transition starts, and an invisible element can't take
        // focus, so wait a moment before moving focus into it.
        const focusTimer = window.setTimeout(() => ref.current?.focus(), 60);

        // Escape closes it from anywhere, even if focus hasn't made it inside yet.
        const onEscape = (event: globalThis.KeyboardEvent) => {
            if (event.key === 'Escape') onClose();
        };
        document.addEventListener('keydown', onEscape);

        return () => {
            window.clearTimeout(focusTimer);
            document.removeEventListener('keydown', onEscape);
            document.body.style.overflow = previousOverflow;
            previouslyFocused?.focus?.();
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [open]);

    function handleKeyDown(event: KeyboardEvent<HTMLElement>) {
        if (!open || event.key !== 'Tab' || !ref.current) return;

        const items = [...ref.current.querySelectorAll<HTMLElement>(FOCUSABLE)].filter((el) => el.offsetParent !== null);
        if (items.length === 0) return;
        const first = items[0];
        const last = items[items.length - 1];
        if (event.shiftKey && (document.activeElement === first || document.activeElement === ref.current)) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }

    return (
        <>
            {/* Backdrop, mobile only */}
            <div
                aria-hidden="true"
                onClick={onClose}
                className={`fixed inset-0 z-[90] bg-[#152B4D]/60 transition-opacity duration-300 motion-reduce:transition-none lg:hidden ${
                    open ? 'opacity-100' : 'pointer-events-none opacity-0'
                }`}
            />

            <aside
                ref={ref}
                tabIndex={-1}
                role={open ? 'dialog' : undefined}
                aria-modal={open ? true : undefined}
                aria-label="Categories and filters"
                onKeyDown={handleKeyDown}
                className={`fixed inset-y-0 left-0 z-[91] flex w-[320px] max-w-[88vw] flex-col bg-white shadow-2xl outline-none transition-[transform,visibility] duration-300 motion-reduce:transition-none lg:static lg:z-auto lg:w-auto lg:max-w-none lg:translate-x-0 lg:bg-transparent lg:shadow-none lg:visible ${
                    open ? 'visible translate-x-0' : 'invisible -translate-x-full'
                }`}
            >
                <div className="flex items-center justify-between border-b border-[#E4E7EC] px-4 py-3 lg:hidden">
                    <p className="text-[15px] font-extrabold text-[#152B4D]">Categories &amp; Filters</p>
                    <button
                        type="button"
                        onClick={onClose}
                        aria-label="Close filters"
                        className="flex h-10 w-10 items-center justify-center rounded-md text-[#152B4D] hover:bg-[#F4F5F7] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#325A96]"
                    >
                        <X className="h-5 w-5" aria-hidden="true" />
                    </button>
                </div>

                <div className="flex-1 overflow-y-auto px-4 py-4 lg:overflow-visible lg:rounded-xl lg:border lg:border-[#E4E7EC] lg:bg-white lg:p-4">{children}</div>

                <div className="border-t border-[#E4E7EC] p-4 lg:hidden">
                    <button
                        type="button"
                        onClick={onClose}
                        className="w-full rounded-lg bg-[#325A96] px-4 py-3 text-[14px] font-extrabold text-white hover:bg-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]"
                    >
                        {applyLabel}
                    </button>
                </div>
            </aside>
        </>
    );
}
