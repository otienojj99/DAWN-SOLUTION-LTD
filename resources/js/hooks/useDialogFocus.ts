import { useEffect } from 'react';
import type { RefObject } from 'react';

const FOCUSABLE = 'a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])';

/**
 * Modal dialog behaviour: focus moves in when it opens, Tab stays inside,
 * Escape closes, page scroll is locked, and focus returns to what opened it.
 */
export function useDialogFocus(ref: RefObject<HTMLElement | null>, open: boolean, onClose: () => void) {
    useEffect(() => {
        if (!open) return;
        const node = ref.current;
        const previous = document.activeElement as HTMLElement | null;
        const overflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        const first = node?.querySelector<HTMLElement>('[data-autofocus]') ?? node?.querySelector<HTMLElement>(FOCUSABLE);
        first?.focus();

        const onKey = (e: KeyboardEvent) => {
            if (e.key === 'Escape') {
                e.stopPropagation();
                onClose();
                return;
            }
            if (e.key !== 'Tab' || !node) return;
            const items = Array.from(node.querySelectorAll<HTMLElement>(FOCUSABLE)).filter((el) => el.offsetParent !== null);
            if (items.length === 0) return;
            const firstEl = items[0];
            const lastEl = items[items.length - 1];
            if (e.shiftKey && document.activeElement === firstEl) {
                e.preventDefault();
                lastEl.focus();
            } else if (!e.shiftKey && document.activeElement === lastEl) {
                e.preventDefault();
                firstEl.focus();
            }
        };
        document.addEventListener('keydown', onKey);
        return () => {
            document.removeEventListener('keydown', onKey);
            document.body.style.overflow = overflow;
            previous?.focus?.();
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [open]);
}
