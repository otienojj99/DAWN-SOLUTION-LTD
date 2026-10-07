import { Search } from 'lucide-react';
import { useState, type FormEvent } from 'react';

interface SearchBarProps {
    placeholder?: string;
    /** Called with the submitted query. Wire this to an Inertia visit or route() call. */
    onSearch?: (query: string) => void;
    className?: string;
}

/**
 * Header search field. Self-contained — owns its own input state and just
 * reports the submitted value upward via `onSearch`, so the parent decides
 * what actually happens (navigate, fire a request, etc).
 */
export function SearchBar({
    placeholder = 'Search laptops, servers, accessories...',
    onSearch,
    className = '',
}: SearchBarProps) {
    const [query, setQuery] = useState('');

    function handleSubmit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        const trimmed = query.trim();
        if (trimmed.length > 0) {
            onSearch?.(trimmed);
        }
    }

    return (
        <form
            role="search"
            onSubmit={handleSubmit}
            className={`flex w-full overflow-hidden rounded-md border-2 border-[#325A96] ${className}`}
        >
            <label htmlFor="site-search" className="sr-only">
                Search products
            </label>
            <input
                id="site-search"
                type="search"
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                placeholder={placeholder}
                className="w-full min-w-0 flex-1 px-3.5 py-2.5 text-sm text-[#2B2B2B] outline-none placeholder:text-[#5B6470]/70"
            />
            <button
                type="submit"
                aria-label="Search"
                className="flex shrink-0 items-center gap-1.5 bg-[#FAB40A] px-4 font-bold text-[#152B4D] transition-colors duration-200 hover:bg-[#DE9E00] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#152B4D]"
            >
                <Search className="h-4 w-4" strokeWidth={2.5} aria-hidden="true" />
                <span className="hidden text-sm sm:inline">Search</span>
            </button>
        </form>
    );
}
