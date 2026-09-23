<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    public $collects = CategoryResource::class;

    protected ?string $mode = null;

    public function mode(string $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

    public function toArray(Request $request): array
    {
        return $this->collection
            ->map(function (CategoryResource $resource) {
                if ($this->mode) {
                    $resource->mode($this->mode);
                }
                return $resource->toArray($request);
            })
            ->all();
    }
}