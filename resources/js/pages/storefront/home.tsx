import {Header} from '@/components/header/Header';
import { HeroSection, QuickCategoryStrip } from '@/components/hero';
import { PromoSection } from '@/components/promotion';
import type { PromoOffer } from '@/types/promo';
import type { Category } from '@/types/category';
import type { ShopCategory } from '@/types/category';
import { ShopByCategory } from '@/components/category/ShopByCategory';
import { BestSellersSection } from '@/components/products/BestSellersSection';

type Props = {
    promoOffers: PromoOffer[];
    // categoryTree: Category[];
    shopCategories: ShopCategory[];
};


export default function Home({ promoOffers, shopCategories }: Props) {

    
    return (
        < >
            <Header />
          
                <HeroSection />
                <QuickCategoryStrip />
                <PromoSection offers={promoOffers} />
                <ShopByCategory categories={shopCategories}/>
                <BestSellersSection />
        </>
    )
}