<?php

namespace App\Modules\Supplier\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ClearSuppliersCacheJob implements ShouldQueue
{
    use Queueable;

    protected string $cacheTag;

    public function __construct(string $cacheTag)
    {
        $this->cacheTag = $cacheTag;
    }

    public function handle(): void
    {
        cache()->tags([$this->cacheTag])->flush();
    }
}
