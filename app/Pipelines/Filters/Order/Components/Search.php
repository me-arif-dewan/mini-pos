<?php

namespace App\Pipelines\Filters\Order\Components;

use App\Contracts\Pipeline\ComponentInterface;
use Closure;

class Search implements ComponentInterface
{
    public function handle(array $content, Closure $next): mixed
    {
        $search = $content['params']['q'] ?? '';

        if ($search !== '') {
            $content['builder']->where(function ($query) use ($search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhere('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customer) use ($search) {
                        $customer->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $next($content);
    }
}
