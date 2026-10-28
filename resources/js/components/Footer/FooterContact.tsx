import { Mail, MessageCircle, Phone } from 'lucide-react';
import { contactInfo, whatsappHref } from '@/data/contact';

const linkClasses =
    'text-[13.5px] text-white/80 transition-colors hover:text-white hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white';

export function FooterContact() {
    return (
        <div>
            <h2 className="mb-4 text-[13px] font-extrabold uppercase tracking-wider text-[#FAB40A]">Contact Us</h2>
            <address className="grid gap-5 not-italic">
                <ul className="grid gap-2.5">
                    {contactInfo.emails.map((email) => (
                        <li key={email.address} className="flex items-start gap-2.5">
                            <Mail className="mt-0.5 h-4 w-4 shrink-0 text-[#FAB40A]" aria-hidden="true" />
                            <span className="min-w-0">
                                <span className="block text-[11px] font-semibold uppercase tracking-wide text-white/50">{email.label}</span>
                                <a href={`mailto:${email.address}`} className={`${linkClasses} break-all`}>
                                    {email.address}
                                </a>
                            </span>
                        </li>
                    ))}
                </ul>

                <ul className="grid gap-2.5">
                    {contactInfo.phones.map((phone) => (
                        <li key={phone.dial} className="flex items-center gap-2.5">
                            <Phone className="h-4 w-4 shrink-0 text-[#FAB40A]" aria-hidden="true" />
                            <a href={`tel:${phone.dial}`} className={linkClasses}>
                                {phone.display}
                            </a>
                        </li>
                    ))}
                </ul>

                <a
                    href={whatsappHref()}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="inline-flex w-fit items-center gap-2 rounded-lg bg-[#1FA855] px-4 py-2.5 text-[13px] font-bold text-white transition-colors hover:bg-[#188a45] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                >
                    <MessageCircle className="h-4 w-4" aria-hidden="true" />
                    Chat on WhatsApp
                </a>
            </address>
        </div>
    );
}
