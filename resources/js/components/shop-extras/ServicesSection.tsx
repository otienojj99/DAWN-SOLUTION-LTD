import { ArrowRight, Check } from 'lucide-react';
import { consultingCta, consultingSteps, services, servicesIntro } from '@/data/shop-services';
import type { Service } from '@/types/service';

interface ServicesSectionProps {
    items?: Service[];
}

/**
 * Highlights a handful of services (the full list lives on the services
 * page), then a "not sure what to buy?" call to action that walks a
 * corporate buyer through recommendation → demo → instalments.
 */
export function ServicesSection({ items = services }: ServicesSectionProps) {
    return (
        <section aria-labelledby="services-title" className="relative isolate overflow-hidden bg-[#0F2240] py-14 sm:py-16">
            {/* Decoration: a fine grid that fades out, plus two soft glows. Purely visual. */}
            <div
                aria-hidden="true"
                className="pointer-events-none absolute inset-0 -z-10 opacity-[0.07] [background-image:linear-gradient(#fff_1px,transparent_1px),linear-gradient(90deg,#fff_1px,transparent_1px)] [background-size:44px_44px] [-webkit-mask-image:radial-gradient(ellipse_at_top,black,transparent_75%)] [mask-image:radial-gradient(ellipse_at_top,black,transparent_75%)]"
            />
            <div aria-hidden="true" className="pointer-events-none absolute -right-32 -top-32 -z-10 h-96 w-96 rounded-full bg-[#325A96]/40 blur-3xl" />
            <div aria-hidden="true" className="pointer-events-none absolute -bottom-40 -left-24 -z-10 h-80 w-80 rounded-full bg-[#FAB40A]/10 blur-3xl" />

            <div className="mx-auto max-w-[1280px] px-4 sm:px-6">
                <div className="mb-10 flex flex-wrap items-end justify-between gap-5">
                    <div className="max-w-2xl">
                        <p className="mb-3 flex items-center gap-3 text-[12px] font-extrabold uppercase tracking-[0.18em] text-[#FAB40A]">
                            <span className="h-px w-8 bg-[#FAB40A]" aria-hidden="true" />
                            {servicesIntro.kicker}
                        </p>
                        <h2 id="services-title" className="mb-3 text-[28px] font-extrabold leading-tight text-white md:text-[36px]">
                            {servicesIntro.title}
                        </h2>
                        <p className="text-[15px] leading-relaxed text-white/70">{servicesIntro.description}</p>
                    </div>
                    <a
                        href={servicesIntro.allServicesHref}
                        className="group inline-flex items-center gap-2 rounded-lg border border-white/25 px-5 py-2.5 text-[13.5px] font-bold text-white transition-colors hover:border-[#FAB40A] hover:text-[#FAB40A] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    >
                        {servicesIntro.allServicesLabel}
                        <ArrowRight className="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1 motion-reduce:transition-none" aria-hidden="true" />
                    </a>
                </div>

                <ul className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 lg:gap-5">
                    {items.map((service, index) => (
                        <ServiceCard key={service.id} service={service} number={index + 1} />
                    ))}
                </ul>

                <ConsultingCta />
            </div>
        </section>
    );
}

function ServiceCard({ service, number }: { service: Service; number: number }) {
    const Icon = service.icon;

    return (
        <li className="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] p-6 backdrop-blur-sm transition-all duration-300 focus-within:border-[#FAB40A]/60 hover:-translate-y-1 hover:border-[#FAB40A]/60 hover:bg-white/[0.07] motion-reduce:transition-none motion-reduce:hover:translate-y-0">
            {/* Gold line that grows across the top on hover / focus */}
            <span
                aria-hidden="true"
                className="absolute inset-x-0 top-0 h-[3px] origin-left scale-x-0 bg-gradient-to-r from-[#FAB40A] to-[#ffd36b] transition-transform duration-500 group-focus-within:scale-x-100 group-hover:scale-x-100 motion-reduce:transition-none"
            />
            <span aria-hidden="true" className="absolute right-5 top-4 text-[44px] font-extrabold leading-none text-white/[0.06]">
                {String(number).padStart(2, '0')}
            </span>

            <span className="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-[#FAB40A] text-[#152B4D] transition-transform duration-300 group-hover:-rotate-6 group-hover:scale-105 motion-reduce:transition-none motion-reduce:group-hover:transform-none">
                <Icon className="h-6 w-6" aria-hidden="true" />
            </span>

            <h3 className="mb-2 text-[17px] font-extrabold text-white">
                {/* Stretched link: the whole card is clickable, but there is still exactly one link for keyboard and screen readers. */}
                <a href={service.href} className="after:absolute after:inset-0 after:content-[''] focus-visible:outline-none">
                    {service.title}
                </a>
            </h3>
            <p className="mb-4 text-[13.5px] leading-relaxed text-white/70">{service.description}</p>

            <ul className="mb-5 flex flex-wrap gap-2">
                {service.highlights.map((point) => (
                    <li key={point} className="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-[11.5px] font-semibold text-white/85">
                        <Check className="h-3 w-3 text-[#FAB40A]" strokeWidth={3} aria-hidden="true" />
                        {point}
                    </li>
                ))}
            </ul>

            <span className="inline-flex items-center gap-1.5 text-[13px] font-bold text-[#FAB40A]" aria-hidden="true">
                Learn more
                <ArrowRight className="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1 motion-reduce:transition-none" />
            </span>
        </li>
    );
}

function ConsultingCta() {
    return (
        <div className="mt-8 grid gap-8 rounded-2xl bg-white p-6 shadow-[0_20px_50px_rgba(0,0,0,0.25)] sm:p-8 lg:grid-cols-[1fr_1.1fr] lg:items-center lg:gap-10 lg:p-10">
            <div>
                <h3 className="mb-2 text-[22px] font-extrabold leading-tight text-[#152B4D] md:text-[26px]">{consultingCta.title}</h3>
                <p className="mb-5 max-w-md text-[14px] leading-relaxed text-[#5B6470]">{consultingCta.description}</p>
                <a
                    href={consultingCta.primaryHref}
                    className="group inline-flex items-center gap-2 rounded-lg bg-[#325A96] px-6 py-3 text-[14px] font-extrabold text-white transition-colors hover:bg-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96]"
                >
                    {consultingCta.primaryLabel}
                    <ArrowRight className="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1 motion-reduce:transition-none" aria-hidden="true" />
                </a>
            </div>

            <ol className="grid gap-4 sm:grid-cols-2">
                {consultingSteps.map((step, index) => (
                    <li key={step} className="flex items-start gap-3 rounded-xl bg-[#F4F5F7] p-4">
                        <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#152B4D] text-[13px] font-extrabold text-[#FAB40A]" aria-hidden="true">
                            {index + 1}
                        </span>
                        <span className="pt-1 text-[13.5px] font-bold leading-snug text-[#152B4D]">{step}</span>
                    </li>
                ))}
            </ol>
        </div>
    );
}
