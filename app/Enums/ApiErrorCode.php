<?php

namespace App\Enums;

enum ApiErrorCode: string
{
    // Generic
    case UNKNOWN              = 'UNKNOWN_ERROR';
    case VALIDATION_FAILED    = 'VALIDATION_FAILED';
    case UNAUTHENTICATED      = 'UNAUTHENTICATED';
    case UNAUTHORIZED         = 'UNAUTHORIZED';
    case FORBIDDEN            = 'FORBIDDEN';
    case NOT_FOUND            = 'NOT_FOUND';
    case METHOD_NOT_ALLOWED   = 'METHOD_NOT_ALLOWED';
    case TOO_MANY_REQUESTS    = 'TOO_MANY_REQUESTS';
    case SERVER_ERROR         = 'SERVER_ERROR';
    case SERVICE_UNAVAILABLE  = 'SERVICE_UNAVAILABLE';

    // Domain — Categories
    case CATEGORY_NOT_FOUND         = 'CATEGORY_NOT_FOUND';
    case CATEGORY_HAS_CHILDREN      = 'CATEGORY_HAS_CHILDREN';
    case CATEGORY_MAX_DEPTH         = 'CATEGORY_MAX_DEPTH';
    case CATEGORY_INVALID_MOVE      = 'CATEGORY_INVALID_MOVE';
    case CATEGORY_DUPLICATE_SLUG    = 'CATEGORY_DUPLICATE_SLUG';

    // Domain — Products (add as you go)
    case PRODUCT_NOT_FOUND              = 'PRODUCT_NOT_FOUND';
    case PRODUCT_OUT_OF_STOCK           = 'PRODUCT_OUT_OF_STOCK';
    case PRODUCT_SKU_DUPLICATE          = 'PRODUCT_SKU_DUPLICATE';
    case PRODUCT_SLUG_DUPLICATE         = 'PRODUCT_SLUG_DUPLICATE';
    case PRODUCT_TYPE_CHANGE_FORBIDDEN  = 'PRODUCT_TYPE_CHANGE_FORBIDDEN';
    case PRODUCT_HAS_ORDERS             = 'PRODUCT_HAS_ORDERS';
    case PRODUCT_NOT_PUBLISHED          = 'PRODUCT_NOT_PUBLISHED';

    // Domain — Variants
    case VARIANT_NOT_FOUND              = 'VARIANT_NOT_FOUND';
    case VARIANT_SKU_DUPLICATE          = 'VARIANT_SKU_DUPLICATE';
    case VARIANT_REQUIRED_ATTRS_MISSING = 'VARIANT_REQUIRED_ATTRS_MISSING';
    case VARIANT_DEFAULT_REQUIRED       = 'VARIANT_DEFAULT_REQUIRED';

    // Domain — Bundles
    case BUNDLE_REQUIRES_COMPONENTS     = 'BUNDLE_REQUIRES_COMPONENTS';
    case BUNDLE_CANNOT_CONTAIN_ITSELF   = 'BUNDLE_CANNOT_CONTAIN_ITSELF';
    case BUNDLE_CANNOT_CONTAIN_BUNDLE   = 'BUNDLE_CANNOT_CONTAIN_BUNDLE';

    // Domain — Images
    case IMAGE_PRIMARY_REQUIRED         = 'IMAGE_PRIMARY_REQUIRED';
    case IMAGE_UPLOAD_FAILED            = 'IMAGE_UPLOAD_FAILED';

    // Domain — Promotions / Use cases
    case PROMOTION_NOT_FOUND            = 'PROMOTION_NOT_FOUND';
    case USE_CASE_NOT_FOUND             = 'USE_CASE_NOT_FOUND';

    // Domain — Auth / Users
    case INVALID_CREDENTIALS        = 'INVALID_CREDENTIALS';
    case ACCOUNT_DISABLED           = 'ACCOUNT_DISABLED';
    case TOKEN_EXPIRED              = 'TOKEN_EXPIRED';

    public function httpStatus(): int
    {
        return match ($this) {
            self::VALIDATION_FAILED    => 422,
            self::UNAUTHENTICATED,
            self::INVALID_CREDENTIALS,
            self::TOKEN_EXPIRED        => 401,
            self::UNAUTHORIZED,
            self::FORBIDDEN,
            self::ACCOUNT_DISABLED     => 403,
            self::NOT_FOUND,
            self::CATEGORY_NOT_FOUND,
            self::PRODUCT_NOT_FOUND    => 404,
            self::VARIANT_NOT_FOUND,
            self::PROMOTION_NOT_FOUND,
            self::USE_CASE_NOT_FOUND          => 404,

            self::PRODUCT_SKU_DUPLICATE,
            self::PRODUCT_SLUG_DUPLICATE,
            self::VARIANT_SKU_DUPLICATE,
            self::PRODUCT_HAS_ORDERS,
            self::PRODUCT_TYPE_CHANGE_FORBIDDEN,
            self::BUNDLE_REQUIRES_COMPONENTS,
            self::BUNDLE_CANNOT_CONTAIN_ITSELF,
            self::BUNDLE_CANNOT_CONTAIN_BUNDLE,
            self::PRODUCT_OUT_OF_STOCK,
            self::IMAGE_PRIMARY_REQUIRED       => 409,

            self::PRODUCT_NOT_PUBLISHED,
            self::VARIANT_REQUIRED_ATTRS_MISSING,
            self::VARIANT_DEFAULT_REQUIRED     => 422,
            self::METHOD_NOT_ALLOWED   => 405,
            self::CATEGORY_DUPLICATE_SLUG,
            self::CATEGORY_HAS_CHILDREN,
            self::CATEGORY_MAX_DEPTH,
            self::CATEGORY_INVALID_MOVE,
            self::PRODUCT_OUT_OF_STOCK => 409,
            self::TOO_MANY_REQUESTS    => 429,
            self::SERVER_ERROR         => 500,
            self::SERVICE_UNAVAILABLE  => 503,
            default                    => 400,
        };
    }

    public function defaultMessage(): string
    {
        return match ($this) {
            self::VALIDATION_FAILED    => 'The given data was invalid.',
            self::UNAUTHENTICATED      => 'Authentication required.',
            self::UNAUTHORIZED,
            self::FORBIDDEN            => 'You are not allowed to perform this action.',
            self::NOT_FOUND            => 'Resource not found.',
            self::METHOD_NOT_ALLOWED   => 'Method not allowed.',
            self::TOO_MANY_REQUESTS    => 'Too many requests. Please try again later.',
            self::SERVER_ERROR         => 'Something went wrong. Please try again.',
            self::SERVICE_UNAVAILABLE  => 'Service temporarily unavailable.',
            self::CATEGORY_NOT_FOUND   => 'Category not found.',
            self::CATEGORY_HAS_CHILDREN=> 'Category has children and cannot be deleted.',
            self::CATEGORY_MAX_DEPTH   => 'Maximum category depth reached.',
            self::CATEGORY_INVALID_MOVE=> 'Invalid category move.',
            self::CATEGORY_DUPLICATE_SLUG => 'A category with this slug already exists under the same parent.',
            self::PRODUCT_NOT_FOUND    => 'Product not found.',
            self::PRODUCT_OUT_OF_STOCK => 'Product is out of stock.',
            self::INVALID_CREDENTIALS  => 'Invalid credentials.',
            self::ACCOUNT_DISABLED     => 'Account is disabled.',
            self::TOKEN_EXPIRED        => 'Token has expired.',
            default                    => 'An error occurred.',
        };
    }
}