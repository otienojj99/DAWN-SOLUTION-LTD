import { Menu } from 'lucide-react';

interface MobileMenuTriggerProps {
    onClick: () => void;
}

/**
 * Hamburger button that opens MobileNavigation. Split into its own file
 * (rather than inlined in MainHeader) since it's a distinct, reusable
 * control with its own accessible name and touch target sizing.
 */
export function MobileMenuTrigger({ onClick }: MobileMenuTriggerProps) {
    return (
        <button
            type="button"
            onClick={onClick}
            aria-label="Open menu"
            className="flex h-10 w-10 shrink-0 items-center justify-center rounded-md text-[#325A96] transition-colors duration-150 hover:bg-[#F4F5F7] md:hidden"
        >
            <Menu className="h-6 w-6" strokeWidth={2.25} aria-hidden="true" />
        </button>
    );
}
