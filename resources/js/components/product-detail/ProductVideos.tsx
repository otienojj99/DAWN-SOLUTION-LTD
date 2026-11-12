import { Play } from 'lucide-react';
import { useState } from 'react';
import type { ProductVideo } from '@/types/product-detail';

interface ProductVideosProps {
    videos: ProductVideo[];
    productName: string;
}

const youtubePoster = (id: string) => `https://i.ytimg.com/vi/${id}/hqdefault.jpg`;

/**
 * Product video section. Renders nothing when a product has no videos.
 * One large player; with several videos a list beside it switches between
 * them. Uploaded files use the browser's own player (controls, fullscreen,
 * keyboard, captions) and download nothing until played; YouTube videos
 * show a still and only load YouTube's player when clicked.
 */
export function ProductVideos({ videos, productName }: ProductVideosProps) {
    const [index, setIndex] = useState(0);
    if (videos.length === 0) return null;

    const current = videos[Math.min(index, videos.length - 1)];
    const many = videos.length > 1;

    return (
        <section id="product-video" aria-labelledby="product-video-title" className="scroll-mt-4">
            <div className="mb-5">
                <h2 id="product-video-title" className="text-[22px] font-extrabold text-[#152B4D] md:text-[26px]">
                    See It in Action
                </h2>
                <p className="mt-1 text-[14px] text-[#5B6470]">Watch the {productName} before you buy.</p>
            </div>

            <div className={many ? 'grid gap-5 lg:grid-cols-[minmax(0,1fr)_300px]' : ''}>
                <div>
                    <Player key={current.id} video={current} />
                    <p className="mt-3 text-[14px] font-bold text-[#152B4D]">{current.title}</p>
                </div>

                {many && (
                    <ul aria-label="More videos" className="grid content-start gap-3">
                        {videos.map((video, i) => (
                            <li key={video.id}>
                                <button
                                    type="button"
                                    onClick={() => setIndex(i)}
                                    aria-current={i === index ? 'true' : undefined}
                                    className={`flex w-full items-center gap-3 rounded-xl border-[1.5px] p-2 text-left transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#325A96] ${
                                        i === index ? 'border-[#325A96] bg-[#EAF0F9]' : 'border-[#E4E7EC] bg-white hover:border-[#325A96]'
                                    }`}
                                >
                                    <span className="relative h-16 w-28 shrink-0 overflow-hidden rounded-lg bg-[#152B4D]">
                                        {(video.poster ?? (video.kind === 'youtube' ? youtubePoster(video.src) : undefined)) && (
                                            <img src={video.poster ?? youtubePoster(video.src)} alt="" loading="lazy" className="h-full w-full object-cover" />
                                        )}
                                        <span className="absolute inset-0 flex items-center justify-center bg-black/25" aria-hidden="true">
                                            <Play className="h-5 w-5 fill-white text-white" />
                                        </span>
                                    </span>
                                    <span className="min-w-0">
                                        <span className="line-clamp-2 block text-[13px] font-bold text-[#152B4D]">{video.title}</span>
                                        {video.duration && <span className="text-[12px] text-[#5B6470]">{video.duration}</span>}
                                    </span>
                                </button>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </section>
    );
}

function Player({ video }: { video: ProductVideo }) {
    const [activated, setActivated] = useState(false);

    const frame = 'relative aspect-video w-full overflow-hidden rounded-2xl bg-[#0F2240] shadow-[0_10px_30px_rgba(21,43,77,0.18)]';

    if (video.kind === 'file') {
        return (
            <div className={frame}>
                <video controls preload="none" playsInline poster={video.poster} className="h-full w-full object-contain" aria-label={video.title}>
                    <source src={video.src} />
                    {video.captionsSrc && <track kind="captions" src={video.captionsSrc} srcLang="en" label="English" />}
                    Your browser can't play this video.
                </video>
            </div>
        );
    }

    return (
        <div className={frame}>
            {activated ? (
                <iframe
                    title={video.title}
                    src={`https://www.youtube-nocookie.com/embed/${encodeURIComponent(video.src)}?autoplay=1&rel=0`}
                    allow="autoplay; encrypted-media; picture-in-picture; fullscreen"
                    allowFullScreen
                    className="absolute inset-0 h-full w-full border-0"
                />
            ) : (
                <button
                    type="button"
                    onClick={() => setActivated(true)}
                    aria-label={`Play video: ${video.title}`}
                    className="group absolute inset-0 flex items-center justify-center focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-4 focus-visible:outline-[#FAB40A]"
                >
                    <img src={video.poster ?? youtubePoster(video.src)} alt="" loading="lazy" className="absolute inset-0 h-full w-full object-cover" />
                    <span className="relative flex h-16 w-16 items-center justify-center rounded-full bg-[#FAB40A] text-[#152B4D] shadow-lg transition-transform group-hover:scale-110 motion-reduce:transition-none">
                        <Play className="ml-1 h-7 w-7 fill-current" aria-hidden="true" />
                    </span>
                </button>
            )}
        </div>
    );
}
