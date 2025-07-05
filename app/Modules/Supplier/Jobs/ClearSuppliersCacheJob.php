<?php

namespace App\Modules\Supplier\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Redis;

class ClearSuppliersCache implements ShouldQueue
{
    use Queueable;

    protected string $cacheKey;

    public function __construct(string $cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    public function handle(): void
    {
        $keys = Redis::keys($this->cacheKey.'*');

        foreach ($keys as $key) {
            Redis::del($key);
        }
    }
}
