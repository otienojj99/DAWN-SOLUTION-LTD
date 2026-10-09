import { ReactNode } from "react"

interface StorefrontLayoutProps {
    children: ReactNode
}

export function StorefrontLayout({ children }: StorefrontLayoutProps) {
    return (
        <div className="min-h-screen bg-background">
            {children}
        </div>
    )
}