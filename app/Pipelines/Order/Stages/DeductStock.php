<?php

namespace App\Pipelines\Order\Stages;

use App\Contracts\Order\CompletionStage;
use App\Pipelines\Order\CompletionContext;
use Closure;

class DeductStock implements CompletionStage
{
    public function handle(CompletionContext $context, Closure $next): mixed
    {
        foreach ($context->order->items as $item) {
            $context->products[$item->product_id]->decrement('stock_quantity', $item->quantity);
        }

        return $next($context);
    }
}
