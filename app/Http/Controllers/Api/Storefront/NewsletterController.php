<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Newsletter\SubscribeNewsletterRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(SubscribeNewsletterRequest $request): JsonResponse
    {
        $email = $request->validated()['email'];

        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber) {
            if ($subscriber->status === 'unsubscribed') {
                $subscriber->update([
                    'status'          => 'subscribed',
                    'subscribed_at'   => now(),
                    'unsubscribed_at' => null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "You're already subscribed — thanks for the enthusiasm!",
            ]);
        }

        NewsletterSubscriber::create([
            'email'        => $email,
            'status'       => 'subscribed',
            'ip_address'   => $request->ip(),
            'user_agent'   => substr((string) $request->userAgent(), 0, 500),
            'subscribed_at'=> now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscribed. Watch your inbox for deals.',
        ], 201);
    }
}