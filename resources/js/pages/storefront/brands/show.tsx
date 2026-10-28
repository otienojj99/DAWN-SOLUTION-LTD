// import { Head, Link } from '@inertiajs/react';
// import StorefrontLayout from '@/layouts/storefront-layout';
// import { ProductCard } from '@/components/products/ProductCard';
// import type { Product } from '@/types/product';

// type Props = {
//     brand: { id: number; name: string; slug: string; logoUrl: string | null; website: string | null };
//     products: Product[];
//     pagination: { current_page: number; per_page: number; total: number; last_page: number };
// };

// export default function BrandShow({ brand, products, pagination }: Props) {
//     return (
//         <>
//             <Head title={`${brand.name} — Dawn Solutions`} />

//             <section className="bg-primary text-primary-foreground">
//                 <div className="mx-auto max-w-[1400px] px-4 md:px-10 py-12 flex items-center gap-6">
//                     {brand.logoUrl ? (
//                         <img src={brand.logoUrl} alt={brand.name} className="h-16 w-auto object-contain bg-white rounded-lg p-2" />
//                     ) : null}
//                     <div>
//                         <h1 className="font-display text-3xl font-semibold">{brand.name}</h1>
//                         <p className="mt-1 opacity-90">{pagination.total} products</p>
//                     </div>
//                 </div>
//             </section>

//             <div className="mx-auto max-w-[1400px] px-4 md:px-10 py-8">
//                 {products.length ? (
//                     <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
//                         {products.map((p) => (
//                             <ProductCard key={p.id} product={p} />
//                         ))}
//                     </div>
//                 ) : (
//                     <div className="py-20 text-center text-muted-foreground">
//                         No products from {brand.name} yet.
//                     </div>
//                 )}
//             </div>
//         </>
//     );
// }

// BrandShow.layout = (page: React.ReactNode) => <StorefrontLayout>{page}</StorefrontLayout>;