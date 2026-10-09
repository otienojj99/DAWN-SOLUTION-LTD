import  { Link } from '@inertiajs/react';

interface ProductCardProps {
    product: {
        id: number;
        name: string;
        slug: string;
        image: string;
        price: number;
        originalPrice?: number;
    };
}

export function ProductCard({ product }: ProductCardProps) {
    const hasOriginalPrice = Boolean(product.originalPrice);

    return (
        <div className="group relative overflow-hidden rounded-lg border bg-white shadow-sm transition-all duration-300 hover:shadow-md">
            <Link href={`/shop/products/${product.slug}`}>
                <img
                    src={product.image}
                    alt={product.name}
                    className="h-full w-full object-cover"
                />
            </Link>

            <div className="p-4">
                <h3 className="font-semibold text-lg">{product.name}</h3>
                <p className="text-2xl font-bold text-primary">${product.price.toFixed(2)}</p>
                {hasOriginalPrice ? (
                    <p className="text-lg text-muted-foreground line-through">
                        ${product.originalPrice!.toFixed(2)}
                    </p>
                ) : null}
            </div>
        </div>
    );
}
