export interface FooterLink {
    label: string;
    href: string;
}

/** One column of links in the footer, e.g. "Quick Links". */
export interface FooterLinkGroup {
    id: string;
    title: string;
    links: FooterLink[];
}

export type SocialPlatform = 'x' | 'instagram' | 'facebook' | 'tiktok';

export interface SocialLink {
    platform: SocialPlatform;
    /** Accessible name, e.g. "Follow us on Instagram". */
    label: string;
    href: string;
}

export interface EmailContact {
    /** What this address is for, e.g. "Sales". */
    label: string;
    address: string;
}

export interface PhoneContact {
    /** As shown to people, e.g. "+254 781 001144". */
    display: string;
    /** Dialable form for the tel: link, e.g. "+254781001144". */
    dial: string;
}

export interface ContactInfo {
    emails: EmailContact[];
    phones: PhoneContact[];
    whatsapp: {
        /** International format with no "+" or spaces, as wa.me expects. */
        number: string;
        /** Pre-filled first message in the chat. */
        message: string;
    };
}
