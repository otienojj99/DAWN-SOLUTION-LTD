<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference', 'full_name', 'phone', 'email', 'company',
        'interest', 'details', 'status', 'spam_reason',
        'emailed_at', 'handled_by', 'internal_notes',
        'ip_address', 'user_agent',
    ];

    protected $casts = [
        'emailed_at' => 'datetime',
    ];

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function scopeNew(Builder $q): Builder
    {
        return $q->where('status', 'new');
    }

    public function scopeNotSpam(Builder $q): Builder
    {
        return $q->where('status', '!=', 'spam');
    }

    /**
     * Generates the next reference like QR-2026-0001.
     * Call this when creating — no race condition because we retry on unique collision.
     */
    public static function nextReference(): string
    {
        $prefix = 'QR-' . now()->format('Y') . '-';

        $last = static::withTrashed()
            ->where('reference', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('reference');

        $next = $last
            ? ((int) substr($last, strlen($prefix))) + 1
            : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}