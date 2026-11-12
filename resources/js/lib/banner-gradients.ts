export const BANNER_GRADIENTS: Record<string, string> = {
    'blue-deep': 'from-[#152B4D] via-[#1D3A64] to-[#325A96]',
    'purple':    'from-[#2B1A5E] via-[#3B2A7E] to-[#5B47B5]',
    'green':     'from-[#0F4C3A] via-[#14604A] to-[#1E9E5A]',
    'orange':    'from-[#7A2E0E] via-[#A8411A] to-[#D9651F]',
    'red':       'from-[#7A1E1E] via-[#A83232] to-[#DC2626]',
    'gold':      'from-[#B88800] via-[#D4A017] to-[#FAB40A]',
};

export function resolveBannerGradient(key: string | null | undefined): string {
    const fallback = 'blue-deep';
    return BANNER_GRADIENTS[key ?? fallback] ?? BANNER_GRADIENTS[fallback];
}