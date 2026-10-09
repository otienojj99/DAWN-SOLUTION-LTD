import {Header} from '@/components/header/Header';
import { HeroSection, QuickCategoryStrip } from '@/components/hero';
import { PromoSection } from '@/components/promotion';
import type { PromoOffer } from '@/types/promo';

type Props = {
    promoOffers: PromoOffer[];
};


export default function Home({ promoOffers }: Props) {

    
    return (
        <div >
            <Header />
          
                <HeroSection />
                <QuickCategoryStrip />
                 <PromoSection offers={promoOffers} />
        </div>
    )
}