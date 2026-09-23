import type { ImgHTMLAttributes } from 'react';

import logo from '@/assets/dawnslogo.png';

export default function AppLogoIcon(
    props: ImgHTMLAttributes<HTMLImageElement>,
) {
    return <img src={logo} alt="Dawn Solutions" {...props} />;
}