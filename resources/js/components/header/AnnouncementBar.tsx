import type { Announcement } from '@/types/navigation';


interface AnnouncementBarProps {
    announcements: Announcement[];
}

const MARQUEE_THRESHOLD_CHARS = 70;

function AnnouncementContent({ announcement }: { announcement: Announcement }) {
    const content = (
        <span className="px-6 text-[12.5px] font-medium tracking-wide text-white/90">
            {announcement.message}
        </span>
    );
    return announcement.href ? (
        <a href={announcement.href} className="hover:text-[#FAB40A]">
            {content}
        </a>
    ) : (
        content
    );
}

export function AnnouncementBar({ announcements}: AnnouncementBarProps) {
    if (announcements.length === 0) return null;
    const combinedLength = announcements.reduce((sum, a) => sum + a.message.length, 0);
    const shouldMarquee = announcements.length > 1 || combinedLength > MARQUEE_THRESHOLD_CHARS;

     if (!shouldMarquee) {
        return (
            <div className="bg-[#325A96] py-2 text-center">
                <AnnouncementContent announcement={announcements[0]} />
            </div>
        );
    }

     return (
        <div className="overflow-hidden bg-[#325A96] py-2" role="status" aria-live="off">
            <div className="dsl-marquee-track flex w-max motion-reduce:animate-none">
                {/* render the list twice back-to-back for a seamless loop */}
                {[...announcements, ...announcements].map((announcement, i) => (
                    <div key={`${announcement.id}-${i}`} className="flex shrink-0 items-center">
                        <AnnouncementContent announcement={announcement} />
                        <span className="text-white/30" aria-hidden="true">
                            •
                        </span>
                    </div>
                ))}
            </div>

            {/* Scoped styles: keeps the keyframe definition colocated with the
                one component that uses it, no global CSS / tailwind.config edit required. */}
            <style>{`
                .dsl-marquee-track {
                    animation: dsl-marquee 28s linear infinite;
                }
                .dsl-marquee-track:hover {
                    animation-play-state: paused;
                }
                @keyframes dsl-marquee {
                    from { transform: translateX(0); }
                    to { transform: translateX(-50%); }
                }
            `}</style>
        </div>
    );
}