import { companyName, footerAbout, footerGroups, legalLinks, paymentMethods, trustPoints } from '@/data/footer';
import { FooterContact } from './FooterContact';
import { FooterLinkColumn } from './FooterLinkColumn';
import { SocialLinks } from './SocialLinks';

interface FooterProps {
    /** Logo image. Shown on a white pill because the logo's dark-blue text disappears on the navy footer. */
    logoSrc?: string;
    /** Copyright year; defaults to the current year. */
    year?: number;
}

export function Footer({ logoSrc, year = new Date().getFullYear() }: FooterProps) {
    return (
        <footer className="bg-[#152B4D] text-white">
            <div className="mx-auto max-w-[1280px] px-4 pb-8 pt-12 sm:px-6 sm:pt-16">
                <div className="grid grid-cols-2 gap-x-6 gap-y-10 lg:grid-cols-[1.5fr_1fr_1fr_1fr_1.4fr] lg:gap-x-10">
                    {/* Brand */}
                    <div className="col-span-2 lg:col-span-1">
                        <a href="/" aria-label={`${companyName} home`} className="mb-4 inline-block focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">
                            {logoSrc ? (
                                <span className="inline-block rounded-lg bg-white px-3 py-2">
                                    <img src={logoSrc} alt={companyName} className="h-9 w-auto" />
                                </span>
                            ) : (
                                <span className="text-2xl font-extrabold text-white">
                                    D<span className="text-[#FAB40A]">a</span>wn Solution
                                </span>
                            )}
                        </a>
                        <p className="mb-5 max-w-[320px] text-[13.5px] leading-relaxed text-white/70">{footerAbout}</p>
                        <SocialLinks />
                    </div>

                    {/* Link groups: share one row on mobile/tablet, become grid columns on desktop */}
                    <div className="col-span-2 grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:contents">
                        {footerGroups.map((group) => (
                            <FooterLinkColumn key={group.id} group={group} />
                        ))}
                    </div>

                    <div className="col-span-2 lg:col-span-1">
                        <FooterContact />
                    </div>
                </div>

                {/* Payment + trust */}
                <div className="mt-12 flex flex-col gap-6 border-t border-white/10 pt-8 lg:flex-row lg:items-center lg:justify-between">
                    {paymentMethods.length > 0 && (
                        <div className="flex flex-wrap items-center gap-3">
                            <span className="text-[12px] font-semibold uppercase tracking-wide text-white/60">We accept</span>
                            <ul className="flex flex-wrap gap-2">
                                {paymentMethods.map((method) => (
                                    <li key={method} className="rounded-md bg-white px-3 py-1.5 text-[12px] font-extrabold tracking-wide text-[#1FA855]">
                                        {method}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}

                    <ul className="flex flex-wrap gap-x-6 gap-y-3">
                        {trustPoints.map(({ id, label, icon: Icon }) => (
                            <li key={id} className="flex items-center gap-2 text-[12.5px] font-semibold text-white/80">
                                <Icon className="h-4 w-4 text-[#FAB40A]" aria-hidden="true" />
                                {label}
                            </li>
                        ))}
                    </ul>
                </div>

                {/* Bottom bar */}
                <div className="mt-8 flex flex-col gap-3 border-t border-white/10 pt-6 text-[12.5px] text-white/60 sm:flex-row sm:items-center sm:justify-between">
                    <p>
                        &copy; {year} {companyName}. All rights reserved.
                    </p>
                    <ul className="flex flex-wrap gap-x-5 gap-y-2">
                        {legalLinks.map((link) => (
                            <li key={link.href}>
                                <a href={link.href} className="hover:text-white hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                                    {link.label}
                                </a>
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </footer>
    );
}
