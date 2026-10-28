import { socialLinks } from '@/data/footer';
import { SocialIcon } from './SocialIcon';

export function SocialLinks() {
    return (
        <ul className="flex gap-2.5">
            {socialLinks.map((social) => (
                <li key={social.platform}>
                    <a
                        href={social.href}
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label={social.label}
                        className="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-[#FAB40A] hover:text-[#152B4D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    >
                        <SocialIcon platform={social.platform} />
                    </a>
                </li>
            ))}
        </ul>
    );
}
