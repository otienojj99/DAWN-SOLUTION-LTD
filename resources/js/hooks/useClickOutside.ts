import { useEffect, type RefObject } from 'react';

/**
 * Calls `onOutside` when a pointer event happens outside `ref`, and when
 * Escape is pressed. Used by AccountMenu (and any future dropdown) to close
 * itself on outside click — keeps that logic out of each component.
 */
export function useClickOutside<T extends HTMLElement>(
    ref: RefObject<T | null>,
    onOutside: () => void,
    active: boolean = true,
): void {
    useEffect(() => {
        if (!active) return;

        function handlePointerDown(event: PointerEvent) {
            if (ref.current && !ref.current.contains(event.target as Node)) {
                onOutside();
            }
        }

        function handleKeyDown(event: KeyboardEvent) {
            if (event.key === 'Escape') {
                onOutside();
            }
        }

        document.addEventListener('pointerdown', handlePointerDown);
        document.addEventListener('keydown', handleKeyDown);

        return () => {
            document.removeEventListener('pointerdown', handlePointerDown);
            document.removeEventListener('keydown', handleKeyDown);
        };
    }, [ref, onOutside, active]);
}
