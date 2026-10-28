import { useCallback, useState } from 'react';
import type { SubmitStatus } from '@/types/forms';

export function useSubmitStatus() {
    const [status, setStatus] = useState<SubmitStatus>('idle');
    const [message, setMessage] = useState<string | null>(null);

    const submit = useCallback(async (action: () => Promise<void>) => {
        setStatus('submitting');
        setMessage(null);
        try {
            await action();
            setStatus('success');
        } catch (error) {
            setStatus('error');
            setMessage(error instanceof Error && error.message ? error.message : 'Something went wrong. Please try again.');
        }
    }, []);

    const reset = useCallback(() => {
        setStatus('idle');
        setMessage(null);
    }, []);

    return { status, message, submit, reset, isSubmitting: status === 'submitting' };
}
