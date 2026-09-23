<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;

abstract class AdminApiController extends ApiController
{
    // Hook point for admin-wide behavior:
    // - authorize() shortcuts
    // - audit logging
    // - admin-only response meta
}