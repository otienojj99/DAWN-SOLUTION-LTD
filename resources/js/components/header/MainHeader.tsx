import { Logo } from './Logo';
import { SearchBar } from './SearchBar';
import { WishlistButton } from './WishlistButton';
import { CartButton } from './CartButton';
import { AccountMenu } from './AccountMenu';
import { MobileMenuTrigger } from './MobileMenuTrigger';


interface MainHeaderProps {
    logoSrc?: string; // Should come from assets
    wishlistCount?: number;
    cartCount?: number;
    userName?: string;
    onSearch?: (query: string) => void;
    onOpenMobileMenu: () => void;
}

/**
 * The second tier of the header: logo, search, and the account/wishlist/cart
 * cluster. Each piece is its own component — this file only handles layout.
 */
export function MainHeader({
    logoSrc = '',
    wishlistCount = 0,
    cartCount = 0,
    userName,
    onSearch,
    onOpenMobileMenu,
}: MainHeaderProps) {
    return (
        <div className="border-b border-[#E4E7EC] bg-white">
            <div className="mx-auto flex max-w-[1400px] items-center gap-3 px-4 py-3 md:gap-7 md:px-8 md:py-4">
                <MobileMenuTrigger onClick={onOpenMobileMenu} />

                <Logo src={logoSrc} />

                <div className="hidden flex-1 md:block md:max-w-[640px]">
                    <SearchBar onSearch={onSearch} />
                </div>

                <div className="ml-auto flex items-center md:ml-0 md:gap-1">
                    <WishlistButton count={wishlistCount} />
                    <CartButton count={cartCount} />
                    <AccountMenu userName={userName} />
                </div>
            </div>

            {/* Search drops to its own full-width row on mobile rather than
               shrinking to uselessness in the top row. */}
            <div className="border-t border-[#E4E7EC] px-4 py-2.5 md:hidden">
                <SearchBar onSearch={onSearch} />
            </div>
        </div>
    );
}
