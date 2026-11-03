import {Header} from '@/components/header/Header';
import { HeroSection, QuickCategoryStrip } from '@/components/hero';
import {ShopPage} from '@/components/shop/ShopPage';

import { Footer } from '@/components/Footer';

// import { brands } from '@/data/brands';

type Props = {
    initialPath?: string[];
    initialSearch?: string;
};


export default function Home({ initialPath = [], initialSearch = '' }: Props) {
    return (
        <>
            <Header />
            <ShopPage initialPath={initialPath} initialSearch={initialSearch} />
            <QuickCategoryStrip />
            <Footer />
        </>
    )
}