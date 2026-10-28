import type { FooterLinkGroup } from '@/types/footer';

export function FooterLinkColumn({ group }: { group: FooterLinkGroup }) {
    const headingId = `footer-${group.id}`;

    return (
        <nav aria-labelledby={headingId}>
            <h2 id={headingId} className="mb-4 text-[13px] font-extrabold uppercase tracking-wider text-[#FAB40A]">
                {group.title}
            </h2>
            <ul className="grid gap-2.5">
                {group.links.map((link) => (
                    <li key={link.href}>
                        <a
                            href={link.href}
                            className="text-[13.5px] text-white/75 transition-colors hover:text-white hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                        >
                            {link.label}
                        </a>
                    </li>
                ))}
            </ul>
        </nav>
    );
}
