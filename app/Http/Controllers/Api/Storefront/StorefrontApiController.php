<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Api\ApiController;

abstract class StorefrontApiController extends ApiController
{
    // Hook point for storefront-wide behavior:
    // - response caching headers
    // - locale / currency context
    // - public-only response meta
}