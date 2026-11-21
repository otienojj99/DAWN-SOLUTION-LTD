<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'path'       => $this->path,
             'url'        => $this->resolveUrl(),
            'alt_text'   => $this->alt_text,
            'is_primary' => (bool) $this->is_primary,
            'sort_order' => $this->sort_order,
            'variant_id' => $this->variant_id,
        ];
    }

    protected function resolveUrl(): ?string
    {
        if (! $this->path) {
            return null;
        }

        // External URL — return as-is
        if (str_starts_with($this->path, 'http://') || str_starts_with($this->path, 'https://')) {
            return $this->path;
        }

         $diskPath = storage_path('app/public/' . ltrim($this->path, '/'));

          return file_exists($diskPath)
            ? asset('storage/' . ltrim($this->path, '/'))
            : null;
    }
}