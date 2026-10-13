import type { ProductBadge as ProductBadgeType } from '@/types/product';

interface ProductBadgeProps {
    badge: ProductBadgeType;
}

const styles: Record<string, string> = {
    red: 'bg-[#E14B4B] text-white',
    orange: 'bg-[#E97826] text-white',
    amber: 'bg-[#FAB40A] text-[#152B4D]',
    yellow: 'bg-[#FAB40A] text-[#152B4D]',
    green: 'bg-[#1E9E5A] text-white',
    blue: 'bg-[#325A96] text-white',
    purple: 'bg-[#7554A6] text-white',
    pink: 'bg-[#C44575] text-white',
    gray: 'bg-[#5B6470] text-white',
};

/** Small corner badge on product cards. */
export function ProductBadge({ badge }: ProductBadgeProps) {
    const className = styles[badge.color.toLowerCase()] ?? styles.gray;

    return (
        <span className={`rounded px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide ${className}`}>{badge.label}</span>
    );
}
