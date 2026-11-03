<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email', 'status', 'ip_address', 'user_agent',
        'subscribed_at', 'unsubscribed_at',
    ];

    protected $casts = [
        'subscribed_at'   => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];


    public function scopeSubscribed(Builder $q): Builder
    {
        return $q->where('status', 'subscribed');
    }



}