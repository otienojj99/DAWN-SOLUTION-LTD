import type { ReactNode } from 'react';

interface SectionHeadingProps {
    id: string;
    title: string;
    description?: string;
    /** Right-aligned slot — carousel controls, tabs, a "view all" link, etc. */
    actions?: ReactNode;
}

/** Title + subtitle + optional right-side actions, shared by the homepage sections. */
export function SectionHeading({ id, title, description, actions }: SectionHeadingProps) {
    return (
        <div className="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 id={id} className="text-xl font-extrabold text-[#152B4D] md:text-2xl">
                    {title}
                </h2>
                {description && <p className="mt-1 text-[13.5px] text-[#5B6470]">{description}</p>}
            </div>
            {actions}
        </div>
    );
}
