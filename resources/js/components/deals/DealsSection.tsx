// components/deals/DealsSection.tsx
import type { DealsSectionProps } from '@/types/deal';
import { CountdownTimer } from './CountdownTimer';
import { DealCard } from './DealCard';

export function DealsSection({
  deals,
  badgeText,
  title,
  subtitle,
  countdownDurationSeconds,
  viewAllHref,
}: DealsSectionProps) {
   if (!deals.length) return null;
  return (
    <section className="relative overflow-hidden bg-[linear-gradient(120deg,#1E3A66_0%,#1D3A64_55%,#325A96_100%)] px-10 pb-14 pt-12">
      {/* decorative radial glows */}
      <div className="pointer-events-none absolute -left-[8%] top-[-40%] h-[600px] w-[600px] bg-[radial-gradient(circle,rgba(225,75,75,0.18)_0%,rgba(225,75,75,0)_65%)]" />
      <div className="pointer-events-none absolute -bottom-[40%] -right-[8%] h-[600px] w-[600px] bg-[radial-gradient(circle,rgba(250,180,10,0.14)_0%,rgba(250,180,10,0)_65%)]" />

      <div className="relative z-10 mx-auto max-w-[1400px]">
        {/* header row */}
        <div className="mb-8 flex flex-wrap items-center justify-between gap-5">
          <div className="text-white">
            <div className="mb-3 inline-flex items-center gap-1.5 rounded-full border border-[#E14B4B] bg-[rgba(225,75,75,0.15)] px-3.5 py-1.5 text-[11.5px] font-extrabold uppercase tracking-wider text-[#ff8080]">
              <span className="relative flex h-[7px] w-[7px]">
                <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#E14B4B] opacity-75" />
                <span className="relative inline-flex h-[7px] w-[7px] rounded-full bg-[#E14B4B]" />
              </span>
              {badgeText}
            </div>

            <h2 className="mb-1.5 text-[28px] font-extrabold">{title}</h2>
            <p className="text-[13.5px] text-white/70">{subtitle}</p>
          </div>

          <CountdownTimer durationSeconds={countdownDurationSeconds} />
        </div>

        {/* grid */}
        <div className="grid grid-cols-1 gap-[18px] sm:grid-cols-2 lg:grid-cols-4">
          {deals.map((deal) => (
            <DealCard key={deal.id} deal={deal} />
          ))}
        </div>

        {/* footer CTA */}
        <div className="mt-8 text-center">
          <a
            href={viewAllHref}
            className="inline-block rounded-[7px] bg-[#FAB40A] px-8 py-3 text-sm font-extrabold text-[#1E3A66] shadow-[0_8px_20px_rgba(250,180,10,0.25)] transition hover:-translate-y-0.5 hover:bg-[#DE9E00]"
          >
            View All Clearance Deals →
          </a>
        </div>
      </div>
    </section>
  );
}