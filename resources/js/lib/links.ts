import type { MouseEvent } from 'react';

/**
 * True for an ordinary left click. Links handled in JavaScript (client-side
 * navigation) should only intercept these, so Ctrl/Cmd-click (new tab),
 * Shift-click and middle-click keep working like normal links.
 */
export function isPlainLeftClick(event: MouseEvent): boolean {
    return !event.defaultPrevented && event.button === 0 && !event.metaKey && !event.ctrlKey && !event.shiftKey && !event.altKey;
}
