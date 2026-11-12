import {Header} from '@/components/header/Header';
import { HeroSection, QuickCategoryStrip } from '@/components/hero';
import { PromoSection } from '@/components/promotion';
import type { PromoOffer } from '@/types/promo';
import type { Product } from '@/types/product';
import {StorefrontLayout} from '@/layouts/storefront/storefront-layout';
import type { Category } from '@/types/category';
import type { ShopCategory } from '@/types/category';
import { ShopByCategory } from '@/components/category/ShopByCategory';
import { BestSellersSection } from '@/components/products/BestSellersSection';
import { BrandStrip } from '@/components/brands/BrandStrip';
import {UseCaseSection } from '@/components/collections/UseCaseSection';
import type { Brand } from '@/types/brand';
import type { UseCase } from '@/types/collection';
import { DealsSection } from '@/components/deals';
// import { deals, dealsSectionMeta } from '@/data/deals';
import type { Deal, DealsSectionMeta } from '@/types/deal';
import { NewsletterQuoteSection} from '@/components/cta/NewsletterQuoteSection';
import { Footer } from '@/components/Footer';
import { api } from '@/lib/api';
import type { QuoteRequest } from '@/types/forms';

// import { brands } from '@/data/brands';

type Props = {
    promoOffers: PromoOffer[];
    // categoryTree: Category[];
    shopCategories: ShopCategory[];

    bestSellers: Product[];

    brands: Brand[];
    useCases: UseCase[];
    deals: Deal[];
    dealsMeta: DealsSectionMeta;
};


export default function Home({ promoOffers, shopCategories, bestSellers, brands, useCases, deals, dealsMeta }: Props) {

  
    async function handleSubscribe(email: string): Promise<void> {
        await api.post('/newsletter/subscribe', { email });
    }

    async function handleRequestQuote(request: QuoteRequest): Promise<void> {
        await api.post('/quote-requests', request);
    }
    return (
        < >
            <Header />
          
                <HeroSection products={bestSellers} />
                <QuickCategoryStrip />
                <PromoSection offers={promoOffers} />
                <ShopByCategory categories={shopCategories}/>
                <BestSellersSection  products={bestSellers}/>
                <BrandStrip  brands={brands}/>
               <UseCaseSection
                   useCases={useCases}
               />
               <DealsSection {...dealsMeta} deals={deals} />
            

                <NewsletterQuoteSection
                onSubscribe={handleSubscribe}
                    onRequestQuote={handleRequestQuote}
               />

            <Footer />
        </>
    )
}