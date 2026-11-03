import { SlidersHorizontal } from 'lucide-react';
import { useEffect, useMemo, useRef, useState } from 'react';
import { shopProducts } from '@/data/shop-products';
import { shopTree } from '@/data/shop-tree';
import { SHOP_BASE_PATH } from '@/data/shop-config';
import { usePrefersReducedMotion } from '@/hooks/usePrefersReducedMotion';
import { useShopState } from '@/hooks/useShopState';
import { breadcrumbs, buildSidebar, resolveCategory } from '@/lib/shop-category';
import { computeShopResults } from '@/lib/shop-filter';
import { buildShopUrl, parseShopPath } from '@/lib/shop-url';
import type { Product } from '@/types/product';
import type { CategoryNode, ShopProduct } from '@/types/shop';
import { ActiveFilters } from './ActiveFilters';
import { CategorySidebar } from './CategorySidebar';
import { FilterDrawer } from './FilterDrawer';
import { Pagination } from './Pagination';
import { ProductGrid } from './ProductGrid';
import { ShopBreadcrumb } from './ShopBreadcrumb';
import { ShopFilters } from './ShopFilters';
import { SortSelect } from './SortSelect';

interface ShopPageProps {
    /** The catalogue. Replace the demo data with your backend's products (same `ShopProduct` shape). */
    products?: ShopProduct[];
    /** The category tree. Replace with your `categories` table converted to nested nodes. */
    tree?: CategoryNode[];
    /** Category path from the URL (['laptops','dell','latitude']). Pass the server's value so SSR and hydration agree. */
    initialPath?: string[];
    /** `location.search` from the request. */
    initialSearch?: string;
    onAddToCart?: (product: Product) => void;
    onToggleWishlist?: (product: Product, wishlisted: boolean) => void;
}

const SITE_NAME = 'Dawn Solution';

/**
 * The shop catalogue: breadcrumb, heading, category sidebar, filters, sort,
 * product grid and pagination. Banner and services sections are separate
 * components placed under it by the page (see pages/Shop.example.tsx).
 *
 * All state lives in the URL (useShopState); everything shown is derived
 * from it by the pure functions in lib/shop-*.ts. To move filtering to the
 * server, swap only `computeShopResults` for Inertia props (README).
 */
export function ShopPage({ products = shopProducts, tree = shopTree, initialPath = [], initialSearch = '', onAddToCart, onToggleWishlist }: ShopPageProps) {
    const { path, query, updateQuery, setPath, clearFilters } = useShopState({ initialPath, initialSearch });
    const [drawerOpen, setDrawerOpen] = useState(false);
    const prefersReducedMotion = usePrefersReducedMotion();
    const topRef = useRef<HTMLDivElement>(null);
    const firstRender = useRef(true);

    const category = useMemo(() => resolveCategory(tree, path), [tree, path]);
    const results = useMemo(() => computeShopResults(products, category.path, query), [products, category.path, query]);
    const sidebar = useMemo(() => buildSidebar(tree, category.path, products), [tree, category.path, products]);
    const crumbs = useMemo(() => breadcrumbs(category.trail), [category.trail]);

    const heading = !category.valid ? 'Category not found' : (category.current?.heading ?? 'All Products');
    const description = category.valid ? (category.current?.description ?? 'Laptops, desktops, servers, networking and accessories, all genuine with warranty.') : undefined;
    const { page } = results;

    // The browser tab title follows the heading ("Dell Latitude Series | Dawn Solution").
    useEffect(() => {
        document.title = `${heading} | ${SITE_NAME}`;
    }, [heading]);

    // After changing category or page, bring the top of the results into view.
    const pathKey = category.path.join('/');
    useEffect(() => {
        if (firstRender.current) {
            firstRender.current = false;
            return;
        }
        topRef.current?.scrollIntoView({ block: 'start', behavior: prefersReducedMotion ? 'auto' : 'smooth' });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [pathKey, page.page]);

    function goToCategory(next: string[]) {
        setPath(next);
        setDrawerOpen(false);
    }

    // Breadcrumb links arrive as URLs ('/shop/laptops/dell').
    const goToHref = (href: string) => goToCategory(parseShopPath(href));

    if (!category.valid) {
        return (
            <div className="bg-[#F4F5F7]">
                <div className="mx-auto max-w-[1280px] px-4 py-16 text-center sm:px-6">
                    <h1 className="mb-2 text-2xl font-extrabold text-[#152B4D]">{heading}</h1>
                    <p className="mb-6 text-[14px] text-[#5B6470]">That category doesn't exist or has moved.</p>
                    <a href={SHOP_BASE_PATH} onClick={(e) => { e.preventDefault(); goToCategory([]); }} className="inline-block rounded-lg bg-[#325A96] px-5 py-2.5 text-[14px] font-bold text-white hover:bg-[#152B4D]">
                        Browse all products
                    </a>
                </div>
            </div>
        );
    }

    return (
        <div className="bg-[#F4F5F7]">
            <div className="mx-auto max-w-[1280px] px-4 pb-12 pt-5 sm:px-6">
                <div ref={topRef} className="scroll-mt-4">
                    <ShopBreadcrumb crumbs={crumbs} onNavigate={goToHref} />
                    <div className="mb-6 mt-3">
                        <h1 className="text-2xl font-extrabold text-[#152B4D] md:text-[30px]">{heading}</h1>
                        {description && <p className="mt-1 max-w-2xl text-[14px] text-[#5B6470]">{description}</p>}
                    </div>
                </div>

                <div className="lg:grid lg:grid-cols-[272px_minmax(0,1fr)] lg:items-start lg:gap-7">
                    <FilterDrawer open={drawerOpen} onClose={() => setDrawerOpen(false)} applyLabel={`Show ${page.total} product${page.total === 1 ? '' : 's'}`}>
                        <CategorySidebar sections={sidebar} onNavigate={goToCategory} />

                        <div className="mt-5 border-t border-[#E4E7EC] pt-4">
                            <div className="mb-1 flex items-center justify-between px-1">
                                <h2 className="text-[15px] font-extrabold text-[#152B4D]">Filters</h2>
                                {results.activeFilterCount > 0 && (
                                    <button
                                        type="button"
                                        onClick={clearFilters}
                                        className="text-[12.5px] font-bold text-[#325A96] underline-offset-4 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]"
                                    >
                                        Clear all
                                    </button>
                                )}
                            </div>
                            <ShopFilters query={query} results={results} onChange={updateQuery} />
                        </div>
                    </FilterDrawer>

                    <div className="min-w-0">
                        <div className="mb-4 flex flex-wrap items-center justify-between gap-x-4 gap-y-3 rounded-xl border border-[#E4E7EC] bg-white px-4 py-3">
                            <div className="flex items-center gap-3">
                                <button
                                    type="button"
                                    onClick={() => setDrawerOpen(true)}
                                    className="inline-flex items-center gap-2 rounded-lg border-[1.5px] border-[#325A96] px-3.5 py-2 text-[13.5px] font-bold text-[#325A96] hover:bg-[#EAF0F9] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96] lg:hidden"
                                >
                                    <SlidersHorizontal className="h-4 w-4" aria-hidden="true" />
                                    Filters
                                    {results.activeFilterCount > 0 && (
                                        <span className="flex h-5 min-w-5 items-center justify-center rounded-full bg-[#FAB40A] px-1 text-[11px] font-extrabold text-[#152B4D]">
                                            {results.activeFilterCount}
                                        </span>
                                    )}
                                </button>
                                <p role="status" aria-live="polite" className="text-[13.5px] text-[#5B6470]">
                                    {page.total === 0 ? (
                                        'No products found'
                                    ) : (
                                        <>
                                            Showing <strong className="text-[#152B4D]">{page.from}–{page.to}</strong> of <strong className="text-[#152B4D]">{page.total}</strong> product{page.total === 1 ? '' : 's'}
                                        </>
                                    )}
                                </p>
                            </div>
                            <SortSelect value={query.sort} onChange={(sort) => updateQuery({ sort })} />
                        </div>

                        <div className="mb-4 empty:hidden">
                            <ActiveFilters query={query} onChange={updateQuery} onClearAll={clearFilters} />
                        </div>

                        <ProductGrid
                            products={page.items}
                            onAddToCart={onAddToCart}
                            onToggleWishlist={onToggleWishlist}
                            onClearFilters={results.activeFilterCount > 0 ? clearFilters : undefined}
                        />

                        <Pagination
                            page={page.page}
                            pageCount={page.pageCount}
                            hrefFor={(n) => buildShopUrl(category.path, { ...query, page: n })}
                            onPageChange={(n) => updateQuery({ page: n })}
                        />
                    </div>
                </div>
            </div>
        </div>
    );
}
