<?php

namespace App\Observers;

use App\Models\UseCase;
use Illuminate\Support\Facades\Cache;

class UseCaseObserver
{
    public function saved(UseCase $useCase): void
    {
        $this->flush();
    }

    public function deleted(UseCase $useCase): void
    {
        $this->flush();
    }

    protected function flush(): void
    {
        Cache::forget('storefront.use_cases');
    }
}