import {Header} from '@/components/header/Header';
import { HeroSection, QuickCategoryStrip } from '@/components/hero';
import {ShopPage} from '@/components/shop/ShopPage';

import { Footer } from '@/components/Footer';
import { PromoBannerSlider } from '@/components/shop-extras/PromoBannerSlider';
import { ServicesSection } from '@/components/shop-extras/ServicesSection';
import {StorefrontLayout} from '@/layouts/storefront/storefront-layout';
import type { Product } from '@/types/product';
import type { CategoryNode } from '@/types/shop';
import { ShopBanner } from '@/types/banner';

// import { brands } from '@/data/brands';

type Props = {
    products: Product[];
    tree: CategoryNode[];
    banners: ShopBanner[];
    initialPath?: string[];
    initialSearch?: string;
};


export default function Home({products, tree, banners, initialPath = [], initialSearch = '' }: Props) {
    return (
        <>
            <Header />
            
                <ShopPage initialPath={initialPath} initialSearch={initialSearch} products={products as any} tree={tree} />

                  <PromoBannerSlider banners={banners} />
                  <ServicesSection />
           
            <QuickCategoryStrip />

          
            <Footer />
        </>
    )
}

Home.layout = (page: React.ReactNode) => <StorefrontLayout>{page}</StorefrontLayout>;