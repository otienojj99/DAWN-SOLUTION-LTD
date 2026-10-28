// components/deals/DealCard.tsx
import type { Deal } from '@/types/deal';

const formatPrice = (n: number) => n.toLocaleString('en-KE');

type Props = { deal: Deal };

export function DealCard({ deal }: Props) {
  return (
    <div className="group relative overflow-hidden rounded-xl bg-white transition duration-300 hover:-translate-y-[5px] hover:shadow-[0_16px_30px_rgba(0,0,0,0.25)]">
      {/* image */}
      <div className="relative h-40 overflow-hidden bg-gray-50">
        <span className="absolute left-2.5 top-2.5 z-10 rounded-md bg-[#E14B4B] px-2.5 py-1 text-[13px] font-extrabold text-white">
          -{deal.discountPercent}%
        </span>
       {deal.image ? (
         <img
        src={deal.image}
        alt={deal.name}
        loading="lazy"
        className="..." // keep your existing classes
    />
       ): <div className="flex h-full w-full items-center justify-center bg-[#F4F5F7] text-xs text-[#5B6470]">
        No image
    </div>}
      </div>

      {/* body */}
      <div className="px-4 pb-4 pt-3.5">
        <div className="mb-0.5 text-[10.5px] font-bold uppercase tracking-wide text-[#325A96]">
          {deal.brand}
        </div>

        <div className="mb-2.5 min-h-[35px] text-[13.5px] font-bold leading-snug text-[#2B2B2B]">
          {deal.name}
        </div>

        <div className="mb-2.5 flex items-baseline gap-1.5">
          <span className="text-base font-extrabold text-[#1E3A66]">
            KES {formatPrice(deal.priceNow)}
          </span>
          <span className="text-[11.5px] text-[#a3a9b3] line-through">
            {formatPrice(deal.priceOld)}
          </span>
        </div>

        {/* stock bar */}
        <div>
          <div className="h-1.5 overflow-hidden rounded bg-[#E4E7EC]">
            <div
              className="h-full rounded bg-gradient-to-r from-[#E14B4B] to-[#FAB40A]"
              style={{ width: `${deal.claimedPercent}%` }}
            />
          </div>
          <div className="mt-1.5 flex justify-between text-[10.5px] font-bold text-[#E14B4B]">
            <span>{deal.claimedPercent}% claimed</span>
            <span>{deal.quantityLeft} left</span>
          </div>
        </div>
      </div>
    </div>
  );
}