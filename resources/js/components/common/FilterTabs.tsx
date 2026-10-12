import { useRef, type KeyboardEvent } from 'react';

interface FilterTabsProps {
    tabs: string[];
    active: string;
    onChange: (tab: string) => void;
    /** Id of the element these tabs control; each tab gets `${panelId}-tab-${index}` as its own id. */
    panelId: string;
    label: string;
}

/**
 * Pill-style tab list following the WAI-ARIA tabs pattern: only the
 * active tab is in the tab order (roving tabindex), and Left/Right/Home/End
 * move between tabs. Scrolls horizontally on narrow screens instead of wrapping.
 */
export function FilterTabs({ tabs, active, onChange, panelId, label }: FilterTabsProps) {
    const refs = useRef<(HTMLButtonElement | null)[]>([]);

    function handleKeyDown(event: KeyboardEvent<HTMLDivElement>) {
        const current = tabs.indexOf(active);
        let next = current;

        if (event.key === 'ArrowRight') next = (current + 1) % tabs.length;
        else if (event.key === 'ArrowLeft') next = (current - 1 + tabs.length) % tabs.length;
        else if (event.key === 'Home') next = 0;
        else if (event.key === 'End') next = tabs.length - 1;
        else return;

        event.preventDefault();
        onChange(tabs[next]);
        refs.current[next]?.focus();
    }

    return (
        <div
            role="tablist"
            aria-label={label}
            onKeyDown={handleKeyDown}
            className="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] md:mx-0 md:px-0 [&::-webkit-scrollbar]:hidden"
        >
            {tabs.map((tab, index) => {
                const selected = tab === active;
                return (
                    <button
                        key={tab}
                        ref={(el) => {
                            refs.current[index] = el;
                        }}
                        id={`${panelId}-tab-${index}`}
                        type="button"
                        role="tab"
                        aria-selected={selected}
                        aria-controls={panelId}
                        tabIndex={selected ? 0 : -1}
                        onClick={() => onChange(tab)}
                        className={`shrink-0 whitespace-nowrap rounded-full border-[1.5px] px-4 py-2 text-[13px] font-bold transition-colors duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96] ${
                            selected
                                ? 'border-[#325A96] bg-[#325A96] text-white'
                                : 'border-[#E4E7EC] bg-white text-[#5B6470] hover:border-[#325A96] hover:text-[#325A96]'
                        }`}
                    >
                        {tab}
                    </button>
                );
            })}
        </div>
    );
}
