import { Head, Link } from '@inertiajs/react';
import {StorefrontLayout} from '@/layouts/storefront/storefront-layout';
import { ProductCard } from '@/components/storefront/product-card';
import type { Product } from '@/types/product';


type CategoryDealsProps = {
     category: { id: number; name: string; slug: string; breadcrumb: string };
     promotion: {
        id: number;
        name: string;
        slug: string;
        kind: string;
        tag: string | null;
        title: string;
        subtitle: string | null;
        ctaLabel: string;
        heroImage: string | null;
        heroImageAlt: string | null;
    };
    products: Product[];
    pagination: { current_page: number; per_page: number; total: number; last_page: number };
}


export default function CategoryDeals({ category, promotion, products, pagination }: CategoryDealsProps){
    const hasHero = Boolean(promotion.heroImage);

    return (
         <>
            <Head title={`${promotion.title} — ${category.name}`} />

            {/* Hero banner */}
            {hasHero ? (
                <section className="relative h-[280px] md:h-[340px] overflow-hidden bg-primary">
                    <img
                        src={promotion.heroImage!}
                        alt={promotion.heroImageAlt ?? promotion.title}
                        className="absolute inset-0 h-full w-full object-cover"
                    />
                    <div className="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent" />

                    <div className="relative mx-auto flex h-full max-w-[1400px] flex-col justify-center px-6 md:px-10 text-white">
                        {promotion.tag ? (
                            <span className="mb-3 inline-flex w-fit rounded-full bg-[#FAB40A] px-3 py-1 text-xs font-bold uppercase tracking-wide text-[#152B4D]">
                                {promotion.tag}
                            </span>
                        ) : null}

                        <h1 className="font-display text-3xl font-bold md:text-5xl max-w-2xl leading-tight">
                            {promotion.title}
                        </h1>

                        {promotion.subtitle ? (
                            <p className="mt-3 max-w-xl text-base opacity-90 md:text-lg">
                                {promotion.subtitle}
                            </p>
                        ) : null}
                    </div>
                </section>
            ) : (
                <section className="bg-primary text-primary-foreground">
                    <div className="mx-auto max-w-[1400px] px-6 py-10 md:px-10">
                        <h1 className="font-display text-3xl font-semibold">
                            {promotion.title}
                        </h1>
                        {promotion.subtitle ? (
                            <p className="mt-2 opacity-90">{promotion.subtitle}</p>
                        ) : null}
                    </div>
                </section>
            )}

            {/* Body */}
            <div className="mx-auto max-w-[1400px] px-4 md:px-10 py-8">
                {/* Breadcrumb */}
                <div className="mb-5 text-sm text-muted-foreground">
                    <Link href="/shop" className="hover:text-primary">Home</Link>
                    <span className="mx-1.5">·</span>
                    <Link
                        href={`/shop/categories/${category.slug}`}
                        className="hover:text-primary"
                    >
                        {category.name}
                    </Link>
                    <span className="mx-1.5">·</span>
                    <span>{promotion.title}</span>
                </div>

                <div className="mb-6 flex items-baseline justify-between">
                    <h2 className="font-display text-xl font-semibold">
                        {pagination.total} products on offer
                    </h2>
                </div>

                {products.length ? (
                    <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        {products.map((p) => (
                            <ProductCard key={p.id} product={p} />
                        ))}
                    </div>
                ) : (
                    <div className="py-20 text-center text-muted-foreground">
                        No products on this offer right now. Check back soon.
                    </div>
                )}
            </div>
        </>
    )
}


CategoryDeals.layout = (page: React.ReactNode) => <StorefrontLayout>{page}</StorefrontLayout>;