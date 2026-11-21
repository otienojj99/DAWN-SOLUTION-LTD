import { useEffect, useState } from 'react';

export interface CountdownState {
    /** False until the first client-side tick. Render placeholders until then so server and client HTML match. */
    ready: boolean;
    expired: boolean;
    days: number;
    hours: number;
    minutes: number;
    seconds: number;
}

const EMPTY: CountdownState = { ready: false, expired: false, days: 0, hours: 0, minutes: 0, seconds: 0 };

/** Splits the time between `now` and `target` (both ms timestamps) into days/hours/minutes/seconds. Never negative. */
export function remainingParts(target: number, now: number): Omit<CountdownState, 'ready'> {
    const diff = Math.max(0, target - now);
    const totalSeconds = Math.floor(diff / 1000);
    return {
        expired: diff === 0,
        days: Math.floor(totalSeconds / 86400),
        hours: Math.floor((totalSeconds % 86400) / 3600),
        minutes: Math.floor((totalSeconds % 3600) / 60),
        seconds: totalSeconds % 60,
    };
}

/**
 * Counts down to `endsAt`, ticking once a second and stopping at zero.
 * The first value is a not-ready placeholder: the real remaining time is
 * only known in the browser, and computing it during server rendering
 * would make the server HTML differ from the client's (a hydration
 * mismatch). An invalid date is treated as already expired.
 */
export function useCountdown(endsAt: string | Date): CountdownState {
    const [state, setState] = useState<CountdownState>(EMPTY);
    const target = new Date(endsAt).getTime();

    useEffect(() => {
        if (Number.isNaN(target)) {
            setState({ ...EMPTY, ready: true, expired: true });
            return;
        }

        function tick() {
            const parts = remainingParts(target, Date.now());
            setState({ ready: true, ...parts });
            return parts.expired;
        }

        if (tick()) return;
        const id = setInterval(() => {
            if (tick()) clearInterval(id);
        }, 1000);
        return () => clearInterval(id);
    }, [target]);

    return state;
}
