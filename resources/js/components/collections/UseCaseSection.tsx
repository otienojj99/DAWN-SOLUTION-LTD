// import { useCases as defaultUseCases, useCaseSection } from '@/data/use-cases';
import type { UseCase } from '@/types/collection';
import { UseCaseCard } from './UseCaseCard';

interface UseCaseSectionProps {
    useCases: UseCase[]; 
    eyebrow?: string,
    title: string,
    description: string,
}

const sectionCopy = {
    eyebrow: 'Curated Collections',
    title: 'Shop by Use Case',
    description: "Whatever you're building, running, or playing — we've put together the right gear for it",
};

/**
 * "Shop by Use Case" — one photo banner per audience (Business,
 * Enterprise, Gaming, Education), mirroring the utility-bar links.
 * Four across on desktop, two on tablet, stacked on mobile. Content comes
 * from data/use-cases.ts or the `useCases` prop.
 */
export function UseCaseSection({
    useCases,
    eyebrow =  sectionCopy.eyebrow,
    title = sectionCopy.title,
    description = sectionCopy.description,
}: UseCaseSectionProps) {

     if (!useCases.length) return null;
    return (
        <section aria-labelledby="use-case-heading" className="bg-white">
            <div className="mx-auto max-w-[1400px] px-4 py-12 md:px-10 md:py-16">
                <div className="mx-auto mb-9 max-w-[620px] text-center">
                    <span className="mb-3 inline-block rounded-full bg-[#F4F5F7] px-3.5 py-1.5 text-[11.5px] font-extrabold uppercase tracking-[0.08em] text-[#325A96]">
                        {eyebrow}
                    </span>
                    <h2 id="use-case-heading" className="mb-2 text-xl font-extrabold text-[#152B4D] md:text-2xl">
                        {title}
                    </h2>
                    <p className="text-[13.5px] text-[#5B6470]">{description}</p>
                </div>

                <ul className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    {useCases.map((useCase) => (
                        <li key={useCase.id}>
                            <UseCaseCard useCase={useCase} />
                        </li>
                    ))}
                </ul>
            </div>
        </section>
    );
}
