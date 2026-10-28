export interface Brand {
    id: string;
    name: string;
    logo: string | null;    // ← was `string`
    href: string;
}