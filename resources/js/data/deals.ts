// data/deals.ts
import type { Deal, DealsSectionMeta } from '@/types/deal';

export const deals: Deal[] = [
  {
    id: 'lenovo-thinkpad-e14',
    brand: 'Lenovo',
    name: 'ThinkPad E14 — i7, 16GB, 512GB SSD',
    image: 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&q=80',
    discountPercent: 30,
    priceNow: 53500,
    priceOld: 76500,
    claimedPercent: 82,
    quantityLeft: 9,
  },
  {
    id: 'hp-pavilion-gaming-15',
    brand: 'HP',
    name: 'Pavilion Gaming 15 — Ryzen 7, GTX 1660Ti',
    image: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400&q=80',
    discountPercent: 25,
    priceNow: 84000,
    priceOld: 112000,
    claimedPercent: 65,
    quantityLeft: 14,
  },
  {
    id: 'dell-ultrasharp-27',
    brand: 'Dell',
    name: 'UltraSharp 27" 4K USB-C Monitor',
    image: 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&q=80',
    discountPercent: 20,
    priceNow: 33600,
    priceOld: 42000,
    claimedPercent: 91,
    quantityLeft: 3,
  },
  {
    id: 'logitech-mx-master-3s',
    brand: 'Logitech',
    name: 'MX Master 3S Wireless Combo',
    image: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400&q=80',
    discountPercent: 35,
    priceNow: 6370,
    priceOld: 9800,
    claimedPercent: 48,
    quantityLeft: 26,
  },
];

export const dealsSectionMeta: DealsSectionMeta = {
  badgeText: 'Limited Time',
  title: 'Clearance Deals — While Stock Lasts',
  subtitle:
    "Prices this low won't be around long. Grab them before the timer runs out.",
  countdownDurationSeconds: 2 * 3600 + 14 * 60 + 33, // 02:14:33
  viewAllHref: '#',
};