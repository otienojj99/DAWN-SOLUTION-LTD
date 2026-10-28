// components/deals/CountdownTimer.tsx
'use client';

import { Fragment, useEffect, useState } from 'react';

type TimeLeft = { hours: number; minutes: number; seconds: number };

const pad = (n: number) => String(n).padStart(2, '0');

function splitDuration(totalSeconds: number): TimeLeft {
  return {
    hours: Math.floor(totalSeconds / 3600),
    minutes: Math.floor((totalSeconds % 3600) / 60),
    seconds: totalSeconds % 60,
  };
}

const UNITS = [
  { key: 'hours', label: 'Hrs' },
  { key: 'minutes', label: 'Min' },
  { key: 'seconds', label: 'Sec' },
] as const;

type Props = { durationSeconds: number };

export function CountdownTimer({ durationSeconds }: Props) {
  // Initialise from the static duration so SSR + first client render match.
  const [timeLeft, setTimeLeft] = useState<TimeLeft>(() =>
    splitDuration(durationSeconds),
  );

  useEffect(() => {
    const endAt = Date.now() + durationSeconds * 1000;

    const tick = () => {
      const diff = Math.max(0, endAt - Date.now());
      setTimeLeft(splitDuration(Math.floor(diff / 1000)));
    };

    tick();
    const id = setInterval(tick, 1000);
    return () => clearInterval(id);
  }, [durationSeconds]);

  return (
    <div className="flex items-center gap-2.5">
      <span className="mr-1.5 text-xs font-bold uppercase tracking-wider text-white/65">
        Ends in
      </span>

      {UNITS.map(({ key, label }, i) => (
        <Fragment key={key}>
          {i > 0 && (
            <span className="text-[20px] font-extrabold text-[#FAB40A]">:</span>
          )}
          <div className="min-w-[58px] rounded-lg border border-white/[0.18] bg-white/[0.08] px-3.5 py-2.5 text-center backdrop-blur-sm">
            <div className="text-[22px] font-extrabold leading-none tabular-nums text-[#FAB40A]">
              {pad(timeLeft[key])}
            </div>
            <div className="mt-1 text-[9.5px] uppercase tracking-wider text-white/60">
              {label}
            </div>
          </div>
        </Fragment>
      ))}
    </div>
  );
}