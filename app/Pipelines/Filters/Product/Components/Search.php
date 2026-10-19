<?php

namespace App\Pipelines\Filters\Product\Components;

use App\Contracts\Pipeline\ComponentInterface;
use Closure;

class Search implements ComponentInterface
{
    public function handle(array $content, Closure $next): mixed
    {
        $search = $content['params']['q'] ?? '';

        if ($search !== '') {
            $content['builder']->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        return $next($content);
    }
}
