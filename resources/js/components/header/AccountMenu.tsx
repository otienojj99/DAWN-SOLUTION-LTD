import { ChevronDown, LogIn, Package, User, UserRound } from 'lucide-react';
import { useRef, useState } from 'react';
import { useClickOutside } from '@/hooks/useClickOutside';
import type { NavigationItem } from '@/types/navigation';

interface AccountMenuProps {
    /** Pass the authenticated user's name to switch from "Sign In" to an account dropdown. */
    userName?: string;
    items?: NavigationItem[];
}

const defaultSignedInItems: NavigationItem[] = [
    { label: 'My Account', href: '/account', icon: User },
    { label: 'My Orders', href: '/account/orders', icon: Package },
];

/**
 * Account icon-action. Renders a plain "Sign In" link when logged out,
 * or a dropdown trigger with the user's name when `userName` is supplied.
 * Dropdown closes on outside click and Escape (see useClickOutside).
 */
export function AccountMenu({ userName, items = defaultSignedInItems }: AccountMenuProps) {
    const [open, setOpen] = useState(false);
    const containerRef = useRef<HTMLDivElement>(null);

    useClickOutside(containerRef, () => setOpen(false), open);

    if (!userName) {
        return (
            <a
                href="/login"
                className="group flex flex-col items-center gap-0.5 px-2 py-1.5 text-[11.5px] font-semibold text-[#2B2B2B] transition-colors duration-200 hover:text-[#FAB40A]"
            >
                <LogIn className="h-5 w-5 text-[#325A96] transition-colors duration-200 group-hover:text-[#FAB40A]" strokeWidth={2} aria-hidden="true" />
                <span className="hidden sm:inline">Sign In</span>
            </a>
        );
    }

    return (
        <div className="relative" ref={containerRef}>
            <button
                type="button"
                onClick={() => setOpen((v) => !v)}
                aria-haspopup="menu"
                aria-expanded={open}
                className="group flex flex-col items-center gap-0.5 px-2 py-1.5 text-[11.5px] font-semibold text-[#2B2B2B] transition-colors duration-200 hover:text-[#FAB40A]"
            >
                <span className="flex items-center gap-1">
                    <UserRound className="h-5 w-5 text-[#325A96] transition-colors duration-200 group-hover:text-[#FAB40A]" strokeWidth={2} aria-hidden="true" />
                    <ChevronDown
                        className={`hidden h-3.5 w-3.5 text-[#5B6470] transition-transform duration-200 sm:block ${open ? 'rotate-180' : ''}`}
                        aria-hidden="true"
                    />
                </span>
                <span className="hidden max-w-[80px] truncate sm:inline">{userName}</span>
            </button>

            {open && (
                <div
                    role="menu"
                    className="absolute right-0 top-full z-50 mt-2 w-52 overflow-hidden rounded-lg border border-[#E4E7EC] bg-white py-1.5 shadow-[0_16px_32px_rgba(30,58,102,0.18)]"
                >
                    {items.map((item) => {
                        const Icon = item.icon;
                        return (
                            <a
                                key={item.href}
                                href={item.href}
                                role="menuitem"
                                className="flex items-center gap-2.5 px-4 py-2.5 text-[13.5px] text-[#2B2B2B] transition-colors duration-150 hover:bg-[#F4F5F7] hover:text-[#325A96]"
                            >
                                {Icon && <Icon className="h-4 w-4 text-[#325A96]" aria-hidden="true" />}
                                {item.label}
                            </a>
                        );
                    })}
                    <div className="my-1 border-t border-[#E4E7EC]" />
                    <a
                        href="/logout"
                        role="menuitem"
                        className="block px-4 py-2.5 text-[13.5px] text-[#E14B4B] transition-colors duration-150 hover:bg-[#F4F5F7]"
                    >
                        Sign Out
                    </a>
                </div>
            )}
        </div>
    );
}
