<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * GET /api/admin/reviews?status=pending|approved|all&product_id=123
     */
    public function index(Request $request): JsonResponse
    {
        $query = Review::query()->with(['product:id,name,slug']);

        match ($request->input('status', 'pending')) {
            'approved' => $query->approved(),
            'all'      => null,      // no filter
            default    => $query->pending(),
        };

        if ($request->filled('product_id')) {
            $query->where('product_id', (int) $request->input('product_id'));
        }

        if ($request->filled('search')) {
            $query->where(fn ($q) => $q
                ->where('author_name', 'like', '%' . $request->input('search') . '%')
                ->orWhere('text', 'like', '%' . $request->input('search') . '%')
            );
        }

        $paginated = $query->latest()->paginate(20)->withQueryString();

        return response()->json([
            'success' => true,
            'data'    => $paginated->items(),
            'meta'    => [
                'current_page' => $paginated->currentPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'last_page'    => $paginated->lastPage(),
            ],
        ]);
    }

    /**
     * PATCH /api/admin/reviews/{review}/approve
     */
    public function approve(Review $review): JsonResponse
    {
        $review->update(['is_approved' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Review approved and now visible on the storefront.',
            'data'    => $review->fresh(),
        ]);
    }

    /**
     * PATCH /api/admin/reviews/{review}/hide
     */
    public function hide(Review $review): JsonResponse
    {
        $review->update(['is_approved' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Review hidden from the storefront.',
            'data'    => $review->fresh(),
        ]);
    }

    /**
     * PATCH /api/admin/reviews/{review}/reply
     * Body: { reply: "Thank you for your feedback..." }
     */
    public function reply(Request $request, Review $review): JsonResponse
    {
        $validated = $request->validate([
            'reply' => ['nullable', 'string', 'max:2000'],
        ]);

        $reply = trim((string) ($validated['reply'] ?? ''));

        $review->update([
            'admin_reply'       => $reply === '' ? null : $reply,
            'admin_replied_at'  => $reply === '' ? null : now(),
            'admin_replied_by'  => $reply === '' ? null : $request->user()?->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => $reply === ''
                ? 'Reply removed.'
                : 'Reply published. It is now visible on the storefront.',
            'data'    => $review->fresh(),
        ]);
    }

    /**
     * PATCH /api/admin/reviews/{review}/verify
     * Mark as verified purchase (you can auto-derive later once orders exist).
     */
    public function verify(Review $review): JsonResponse
    {
        $review->update(['verified_purchase' => ! $review->verified_purchase]);

        return response()->json([
            'success' => true,
            'data'    => $review->fresh(),
        ]);
    }

    /**
     * DELETE /api/admin/reviews/{review}
     */
    public function destroy(Review $review): JsonResponse
    {
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted.',
        ]);
    }
}