<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id', 'author_name', 'author_email',
        'ip_address', 'user_agent',
        'rating', 'text',
        'is_approved', 'verified_purchase', 'moderation_note',
        'admin_reply', 'admin_replied_at', 'admin_replied_by',
    ];

    protected $casts = [
        'rating'          => 'integer',
        'is_approved'     => 'boolean',
        'verified_purchase' => 'boolean',
        'admin_replied_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_replied_by');
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('is_approved', true);
    }

    public function scopePending(Builder $q): Builder
    {
        return $q->where('is_approved', false);
    }

    public function scopeForProduct(Builder $q, int $productId): Builder
    {
        return $q->where('product_id', $productId);
    }
}