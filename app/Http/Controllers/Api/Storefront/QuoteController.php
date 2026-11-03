<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quote\StoreQuoteRequest;
use App\Jobs\SendQuoteNotificationJob;
use App\Models\QuoteRequest;
use Illuminate\Http\JsonResponse;

class QuoteController extends Controller
{
    public function store(StoreQuoteRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Honeypot: silently accept, but don't save
        if (! empty($data['website'])) {
            return response()->json([
                'success' => true,
                'message' => 'Request received. We will be in touch shortly.',
            ]);
        }

        $quote = QuoteRequest::create([
            'reference'    => QuoteRequest::nextReference(),
            'full_name'    => $data['full_name'],
            'phone'        => $data['phone'],
            'email'        => $data['email'],
            'company'      => $data['company'] ?? null,
            'interest'     => $data['interest'],
            'details'      => $data['details'] ?? null,
            'status'       => 'new',
            'ip_address'   => $request->ip(),
            'user_agent'   => substr((string) $request->userAgent(), 0, 500),
        ]);

        SendQuoteNotificationJob::dispatch($quote);

        return response()->json([
            'success'   => true,
            'message'   => 'Thank you. We will respond within 1 business day.',
            'reference' => $quote->reference,
        ], 201);
    }
}