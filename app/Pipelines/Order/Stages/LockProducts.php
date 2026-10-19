<?php

namespace App\Pipelines\Order\Stages;

use App\Contracts\Order\CompletionStage;
use App\Models\Product;
use App\Pipelines\Order\CompletionContext;
use Closure;

class LockProducts implements CompletionStage
{
    public function handle(CompletionContext $context, Closure $next): mixed
    {
        $context->products = Product::whereIn('id', $context->order->items->pluck('product_id'))
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        return $next($context);
    }
}
