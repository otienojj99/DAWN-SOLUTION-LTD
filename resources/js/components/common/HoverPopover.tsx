import { useCallback, useEffect, useId, useRef, useState } from 'react';
import type { ReactNode } from 'react';
import { useClickOutside } from '@/hooks/useClickOutside';

interface HoverPopoverProps {
    /** Accessible name of the trigger button and the panel. */
    label: string;
    triggerClassName?: string;
    trigger: ReactNode;
    /** Panel contents. Receives `close` so links and buttons inside can dismiss it. */
    children: (close: () => void) => ReactNode;
    panelClassName?: string;
}

const OPEN_DELAY = 100;
const CLOSE_DELAY = 220;

/**
 * Header icon that opens a small panel on mouse hover. It is also a button,
 * so touch and keyboard users open it with a tap or Enter/Space, and
 * Escape or an outside click closes it (focus returns to the icon on Escape).
 * The panel stays open while focus is inside it, so a half-typed login form isn't lost to a stray mouse move.
 */
export function HoverPopover({ label, triggerClassName = '', trigger, children, panelClassName = '' }: HoverPopoverProps) {
    const [open, setOpen] = useState(false);
    const rootRef = useRef<HTMLDivElement>(null);
    const buttonRef = useRef<HTMLButtonElement>(null);
    const timer = useRef<ReturnType<typeof setTimeout> | undefined>(undefined);
    const lastPointer = useRef<string>('');
    const panelId = useId();

    const clear = () => {
        if (timer.current) clearTimeout(timer.current);
    };
    useEffect(() => clear, []);

    const close = useCallback(() => {
        clear();
        setOpen(false);
    }, []);

    useClickOutside(rootRef, close, open);

    // Escape from anywhere inside returns focus to the icon.
    useEffect(() => {
        if (!open) return;
        const onKey = (e: KeyboardEvent) => {
            if (e.key === 'Escape' && rootRef.current?.contains(document.activeElement)) buttonRef.current?.focus();
        };
        document.addEventListener('keydown', onKey);
        return () => document.removeEventListener('keydown', onKey);
    }, [open]);

    const schedule = (next: boolean, delay: number) => {
        clear();
        timer.current = setTimeout(() => setOpen(next), delay);
    };

    return (
        <div
            ref={rootRef}
            className="relative"
            onPointerEnter={(e) => e.pointerType === 'mouse' && schedule(true, OPEN_DELAY)}
            onPointerLeave={(e) => {
                if (e.pointerType !== 'mouse') return;
                // Typing in the panel keeps it open.
                const active = document.activeElement;
                if (active instanceof HTMLInputElement && rootRef.current?.contains(active)) return;
                schedule(false, CLOSE_DELAY);
            }}
            onBlur={(e) => {
                // relatedTarget is null when focus drops to the page (clicking blank panel space, or a button that just became disabled); only close when focus lands somewhere else.
                const next = e.relatedTarget as Node | null;
                if (next && !e.currentTarget.contains(next)) setOpen(false);
            }}
        >
            <button
                ref={buttonRef}
                type="button"
                aria-label={label}
                aria-haspopup="dialog"
                aria-expanded={open}
                aria-controls={open ? panelId : undefined}
                onPointerDown={(e) => (lastPointer.current = e.pointerType)}
                onClick={() => {
                    clear();
                    // A mouse click on an icon the hover already opened keeps it open instead of flicking it shut.
                    setOpen((v) => (lastPointer.current === 'mouse' && v ? true : !v));
                    lastPointer.current = '';
                }}
                className={triggerClassName}
            >
                {trigger}
            </button>

            {open && (
                <div
                    id={panelId}
                    role="dialog"
                    aria-label={label}
                    className={`dsl-pop fixed inset-x-3 top-[104px] z-[70] overflow-hidden rounded-xl border border-[#E4E7EC] bg-white shadow-[0_18px_40px_rgba(21,43,77,0.22)] sm:absolute sm:inset-x-auto sm:right-0 sm:top-full sm:mt-1 sm:w-[360px] ${panelClassName}`}
                >
                    <style>{`
                        @keyframes dsl-pop-in { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
                        .dsl-pop { animation: dsl-pop-in 160ms ease-out; }
                        @media (prefers-reduced-motion: reduce) { .dsl-pop { animation: none; } }
                    `}</style>
                    {children(close)}
                </div>
            )}
        </div>
    );
}
