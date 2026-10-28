// types/deal.ts

export type Deal = {
  id: string;
  brand: string;
  name: string;
  image: string | null; 
  discountPercent: number;
  priceNow: number;
  priceOld: number;
  claimedPercent: number; // 0–100, drives the stock bar
  quantityLeft: number;
};

export type DealsSectionMeta = {
  badgeText: string;
  title: string;
  subtitle: string;
  countdownDurationSeconds: number;
  viewAllHref: string;
};

export type DealsSectionProps = DealsSectionMeta & {
  deals: Deal[];
};