import {Header} from '@/components/header/Header';
import { HeroSection, QuickCategoryStrip } from '@/components/hero';
import {ShopPage} from '@/components/shop/ShopPage';

import { Footer } from '@/components/Footer';
import {StorefrontLayout} from '@/layouts/storefront/storefront-layout';
import type { Product } from '@/types/product';
import type { CategoryNode } from '@/types/shop';

// import { brands } from '@/data/brands';

type Props = {
    products: Product[];
    tree: CategoryNode[];
    initialPath?: string[];
    initialSearch?: string;
};


export default function Home({products, tree, initialPath = [], initialSearch = '' }: Props) {
    return (
        <>
            <Header />
            
                <ShopPage initialPath={initialPath} initialSearch={initialSearch} products={products as any} tree={tree} />
           
            <QuickCategoryStrip />
            <Footer />
        </>
    )
}

Home.layout = (page: React.ReactNode) => <StorefrontLayout>{page}</StorefrontLayout>;