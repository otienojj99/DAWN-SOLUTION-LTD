import { getCsrfToken } from './csrf';

type ApiError = {
    message: string;
    errors?: Record<string, string[]>;
};

async function post<T>(path: string, body: unknown): Promise<T> {
    const response = await fetch(path, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify(body),
    });

    const data = await response.json();

    if (!response.ok) {
        const error: ApiError = data;
        const firstFieldError = error.errors
            ? Object.values(error.errors)[0]?.[0]
            : null;

        throw new Error(firstFieldError ?? error.message ?? 'Request failed');
    }

    return data as T;
}

export const api = { post };