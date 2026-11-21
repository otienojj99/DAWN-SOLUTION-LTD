<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\Products;
use App\Models\Review;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Honeypot — silent success, nothing saved
        if (! empty($data['website'])) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you. Your review will be checked shortly.',
            ]);
        }

        // Rate limit per IP: 3 reviews / hour
        $recent = Review::query()
            ->where('ip_address', $request->ip())
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recent >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'You have submitted several reviews recently. Please try again later.',
            ], 429);
        }

        $product = Products::findOrFail($data['product_id']);

        Review::create([
            'product_id'      => $product->id,
            'author_name'     => $data['name'],
            'rating'          => (int) $data['rating'],
            'text'            => $data['text'],
            'ip_address'      => $request->ip(),
            'user_agent'      => substr((string) $request->userAgent(), 0, 500),
            'is_approved'     => false,   // moderation queue — admin approves
            'verified_purchase' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you. Your review will appear after our team reviews it.',
        ], 201);
    }
}