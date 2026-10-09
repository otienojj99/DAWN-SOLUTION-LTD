import { usePage} from '@inertiajs/react';
// import { utilityLinks } from '../../data/utility-links';
import { promotionalLinks } from '../../data/promotional-links';
import { UtilityLinks } from './UtilityLinks';
import type { UseCase } from '@/types/use-case';

/**
 * Top-most strip of the header: audience links on the left
 * (For Business / Enterprise / Education / Gaming / Lifestyle),
 * promotional links on the right (Clearance Sale / Best Deals / ...).
 * Hidden on small screens — this tier is a desktop-density affordance;
 * the same destinations are reachable from the mobile drawer.
 */
export function UtilityBar() {

    const { useCases } = usePage<{ useCases: UseCase[] }>().props;

    const audienceLinks = useCases.map((useCase) => ({
        label: useCase.name,
        href: useCase.href,
    }))

    
    return (
        <div className="hidden bg-[#1E3A66] md:block">
            <div className="mx-auto flex max-w-[1400px] items-center justify-between px-6 lg:px-8">
                <UtilityLinks links={audienceLinks} />
                <UtilityLinks links={promotionalLinks} divided />
            </div>
        </div>
    );
}
