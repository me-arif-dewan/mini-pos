<?php

namespace App\Pipelines\Filters\Product\Components;

use App\Contracts\Pipeline\ComponentInterface;
use Closure;

class Status implements ComponentInterface
{
    public function handle(array $content, Closure $next): mixed
    {
        $status = $content['params']['status'] ?? null;
        if (isset($content['params']['status'])) {
            $content['builder']->when($status === 'active',   fn($q) => $q->where('is_active', true))
                ->when($status === 'inactive', fn($q) => $q->where('is_active', false));
        }

        return $next($content);
    }
}
