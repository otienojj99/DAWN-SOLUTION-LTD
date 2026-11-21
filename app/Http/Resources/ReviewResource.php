<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => (string) $this->id,
            'author'          => $this->author_name,
            'date'            => $this->created_at->toIso8601String(),
            'rating'          => (int) $this->rating,
            'text'            => $this->text,
            'verified'        => (bool) $this->verified_purchase,
            'adminReply'      => $this->admin_reply,
            'adminRepliedAt'  => $this->admin_replied_at?->toIso8601String(),
        ];
    }
}