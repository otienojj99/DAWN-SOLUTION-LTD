import type { ProductBadge as ProductBadgeType } from '@/types/product';

interface ProductBadgeProps {
    badge: ProductBadgeType;
    /** For `sale` badges, the discount percentage to show (e.g. 15 → "-15%"). */
    discountPercent?: number | null;
}

const styles: Record<ProductBadgeType, { className: string; text: string }> = {
    sale: { className: 'bg-[#E14B4B] text-white', text: 'Sale' },
    new: { className: 'bg-[#325A96] text-white', text: 'New' },
    bestseller: { className: 'bg-[#FAB40A] text-[#152B4D]', text: 'Best Seller' },
    corporate: { className: 'bg-[#152B4D] text-[#FAB40A]', text: 'Corporate Pick' },
};

/** Small corner badge on product cards. */
export function ProductBadge({ badge, discountPercent }: ProductBadgeProps) {
    const { className, text } = styles[badge];
    const label = badge === 'sale' && discountPercent ? `-${discountPercent}%` : text;

    return (
        <span className={`rounded px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide ${className}`}>{label}</span>
    );
}
