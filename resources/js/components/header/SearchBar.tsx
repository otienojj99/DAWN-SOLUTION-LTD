import { ShopProduct } from '@/types/shop';
import {useId, useMemo,useRef, useState} from 'react'; 
import type { FormEvent, KeyboardEvent } from 'react';
import type { SearchFn } from '@/hooks/useProductSearch';
import { Loader2, Search } from 'lucide-react';
import { formatPrice } from '@/lib/format';
import { useProductSearch } from '@/hooks/useProductSearch';
import { highlightParts, tokenize } from '@/lib/search';
// import { productHref } from '@/data/products';
import { productHref } from '@/lib/routes';
import { shopProducts } from '@/data/shop-products';
import { useClickOutside } from '@/hooks/useClickOutside';

interface SearchBarProps {
    placeholder?: string;
    /** Called with the submitted query. Wire this to an Inertia visit or route() call. */
    onSearch?: (query: string) => void;

     catalogue?: ShopProduct[];
     search?: SearchFn;
    className?: string;

}

const defaultGo = (q: string) => {
    window.location.assign(`/shop?q=${encodeURIComponent(q)}`);
};

/**
 * Header search field. Self-contained — owns its own input state and just
 * reports the submitted value upward via `onSearch`, so the parent decides
 * what actually happens (navigate, fire a request, etc).
 */
export function SearchBar({
    placeholder = 'Search laptops, servers, accessories...',
    onSearch,
    catalogue = shopProducts,
    className = '',
    search, 
}: SearchBarProps) {
    const uid = useId();
    const listId = `${uid}-list`;
    const [query, setQuery] = useState('');
    const [open, setOpen] = useState(false);
    const [active, setActive] = useState(-1);
    const rootRef = useRef<HTMLFormElement>(null);

    const state = useProductSearch(query, { catalogue, search });
    const trimmed = query.trim();
    const tokens = useMemo(() => tokenize(trimmed), [trimmed]);
    const { items, total } = state.result;
    const ready = trimmed.length >= 2;
    const settled = state.settledFor === trimmed && !state.loading;
    const showPanel = open && ready;
    // Options: each product, then the "see all" row when there are results.
    const optionCount = items.length + (total > 0 ? 1 : 0);

    useClickOutside(rootRef, () => setOpen(false), open);

    const go = (q: string) => {
        setOpen(false);
        (onSearch ?? defaultGo)(q);
    }

    function handleSubmit(event: React.FormEvent) {
        event.preventDefault();

        if (active >= 0 && active < items.length) {
            window.location.assign(productHref(items[active]));
            return;
        }
        if (trimmed) go(trimmed); 
    }


    function onKeyDown(e: React.KeyboardEvent<HTMLInputElement>){
         if (e.key === 'ArrowDown') {
            e.preventDefault();
            setOpen(true);
            if (optionCount) setActive((i) => (i + 1) % optionCount);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (optionCount) setActive((i) => (i <= 0 ? optionCount - 1 : i - 1));
        } else if (e.key === 'Escape' && open) {
            e.preventDefault();
            e.stopPropagation();
            setOpen(false);
            setActive(-1);
        } else if (e.key === 'Enter' && active === items.length && total > 0) {
            e.preventDefault();
            go(trimmed);
        }
    }

    const optionId = (i: number) => `${uid}-opt-${i}`;
    const status = !ready ? '' : state.loading ? 'Searching' : state.error ? state.error : total === 0 ? `No products found for ${trimmed}` : `${total} ${total === 1 ? 'result' : 'results'} for ${trimmed}`;

     return (
        <form ref={rootRef} role="search" onSubmit={handleSubmit} className={`relative w-full ${className}`}>
            <div className="flex w-full overflow-hidden rounded-md border-2 border-[#325A96] bg-white">
                <label htmlFor={`${uid}-input`} className="sr-only">
                    Search products
                </label>
                <input
                    id={`${uid}-input`}
                    type="search"
                    role="combobox"
                    aria-expanded={showPanel}
                    aria-controls={listId}
                    aria-autocomplete="list"
                    aria-activedescendant={showPanel && active >= 0 ? optionId(active) : undefined}
                    autoComplete="off"
                    value={query}
                    onChange={(e) => {
                        setQuery(e.target.value);
                        setOpen(true);
                        setActive(-1);
                    }}
                    onFocus={() => setOpen(true)}
                    onKeyDown={onKeyDown}
                    placeholder={placeholder}
                    className="w-full min-w-0 flex-1 px-3.5 py-2.5 text-sm text-[#2B2B2B] outline-none placeholder:text-[#5B6470]/70"
                />
                <button
                    type="submit"
                    aria-label="Search"
                    className="flex shrink-0 items-center gap-1.5 bg-[#FAB40A] px-4 font-bold text-[#152B4D] transition-colors duration-200 hover:bg-[#DE9E00] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-[#152B4D]"
                >
                    {state.loading ? <Loader2 className="h-4 w-4 animate-spin motion-reduce:animate-none" aria-hidden="true" /> : <Search className="h-4 w-4" strokeWidth={2.5} aria-hidden="true" />}
                    <span className="hidden text-sm sm:inline">Search</span>
                </button>
            </div>

            <p role="status" className="sr-only">
                {status}
            </p>

            {showPanel && (
                <div className="absolute inset-x-0 top-full z-[60] mt-1 overflow-hidden rounded-lg border border-[#E4E7EC] bg-white shadow-[0_18px_40px_rgba(21,43,77,0.22)]">
                    {state.error ? (
                        <p className="px-4 py-6 text-center text-[13.5px] font-semibold text-[#C0292B]">{state.error}</p>
                    ) : state.loading && items.length === 0 ? (
                        <p className="px-4 py-6 text-center text-[13.5px] text-[#5B6470]">Searching…</p>
                    ) : settled && total === 0 ? (
                        <div className="px-4 py-6 text-center">
                            <p className="text-[14px] font-bold text-[#152B4D]">No products found for “{trimmed}”</p>
                            <p className="mt-1 text-[12.5px] text-[#5B6470]">Check the spelling, or try a brand or a category like “Dell” or “laptop”.</p>
                        </div>
                    ) : (
                        <ul id={listId} role="listbox" aria-label="Product suggestions" className="max-h-[420px] overflow-y-auto">
                            {items.map((p, i) => (
                                <li key={p.id} role="presentation">
                                    <a
                                        id={optionId(i)}
                                        role="option"
                                        aria-selected={active === i}
                                        href={productHref(p)}
                                        onMouseEnter={() => setActive(i)}
                                        className={`flex items-center gap-3 px-4 py-2.5 ${active === i ? 'bg-[#EAF0F9]' : 'hover:bg-[#F4F5F7]'}`}
                                    >
                                        <img src={p.image ?? ''} alt="" loading="lazy" className="h-12 w-12 shrink-0 rounded-md bg-[#F4F5F7] object-cover" />
                                        <span className="min-w-0 flex-1">
                                            <span className="block truncate text-[13.5px] font-bold text-[#2B2B2B]">
                                                {highlightParts(p.name, tokens).map((part, k) => (part.match ? <mark key={k} className="bg-[#FAB40A]/40 text-inherit">{part.text}</mark> : <span key={k}>{part.text}</span>))}
                                            </span>
                                            <span className="block text-[11.5px] text-[#5B6470]">{p.brand} · {p.category}</span>
                                        </span>
                                        <span className="shrink-0 text-right">
                                            <span className="block text-[13.5px] font-extrabold text-[#152B4D]">{formatPrice(p.price, p.currency)}</span>
                                            {p.inStock === false && <span className="block text-[11px] font-bold text-[#C0292B]">Out of stock</span>}
                                        </span>
                                    </a>
                                </li>
                            ))}
                            {total > 0 && (
                                <li role="presentation">
                                    <a
                                        id={optionId(items.length)}
                                        role="option"
                                        aria-selected={active === items.length}
                                        href={`/shop?q=${encodeURIComponent(trimmed)}`}
                                        onClick={(e) => {
                                            if (onSearch) {
                                                e.preventDefault();
                                                go(trimmed);
                                            }
                                        }}
                                        onMouseEnter={() => setActive(items.length)}
                                        className={`block border-t border-[#E4E7EC] px-4 py-3 text-center text-[13px] font-bold text-[#325A96] ${active === items.length ? 'bg-[#EAF0F9]' : 'bg-[#F9FAFB] hover:bg-[#EAF0F9]'}`}
                                    >
                                        See all {total} {total === 1 ? 'result' : 'results'} for “{trimmed}”
                                    </a>
                                </li>
                            )}
                        </ul>
                    )}
                </div>
            )}
        </form>
    );
}
