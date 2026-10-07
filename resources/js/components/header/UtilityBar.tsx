import { utilityLinks } from '../../data/utility-links';
import { promotionalLinks } from '../../data/promotional-links';
import { UtilityLinks } from './UtilityLinks';

/**
 * Top-most strip of the header: audience links on the left
 * (For Business / Enterprise / Education / Gaming / Lifestyle),
 * promotional links on the right (Clearance Sale / Best Deals / ...).
 * Hidden on small screens — this tier is a desktop-density affordance;
 * the same destinations are reachable from the mobile drawer.
 */
export function UtilityBar() {
    return (
        <div className="hidden bg-[#1E3A66] md:block">
            <div className="mx-auto flex max-w-[1400px] items-center justify-between px-6 lg:px-8">
                <UtilityLinks links={utilityLinks} />
                <UtilityLinks links={promotionalLinks} divided />
            </div>
        </div>
    );
}
