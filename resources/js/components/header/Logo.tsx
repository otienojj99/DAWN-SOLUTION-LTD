// mporting logo from the assets
import logo from '../../../images/dawnslogo.png';

interface LogoProps {
    src: string;
    className?: string;
}


export function 
Logo({ src, className}: LogoProps){

    if (!src) {
        return (
            <a href="/" className={`flex shrink-0 items-center ${className}`} aria-label="Dawn Solution Limited home">
                <img src={logo} alt="Dawn Solution Limited" className="h-10 w-auto md:h-11" />
            </a>
        );
    }
    return (
        <a
            href="/"
            className={`flex shrink-0 items-center gap-1 text-xl font-extrabold text-[#325A96] md:text-2xl ${className}`}
            aria-label="Dawn Solution Limited home"
        >
            D<span className="text-[#FAB40A]">a</span>wn
            <span className="ml-1.5 hidden flex-col text-[10px] font-semibold tracking-wider text-[#5B6470] sm:flex">
                SOLUTION LIMITED
            </span>
        </a>
    )
}