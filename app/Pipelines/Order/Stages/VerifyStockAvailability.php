<?php

namespace App\Pipelines\Order\Stages;

use App\Contracts\Order\CompletionStage;
use App\Exceptions\InsufficientStockException;
use App\Pipelines\Order\CompletionContext;
use Closure;

class VerifyStockAvailability implements CompletionStage
{
    public function handle(CompletionContext $context, Closure $next): mixed
    {
        $problems = [];

        foreach ($context->order->items as $item) {
            $available = $context->products[$item->product_id]->stock_quantity;

            if ($available < $item->quantity) {
                $problems[] = "{$item->product_name} (needs {$item->quantity}, only {$available} in stock)";
            }
        }

        if ($problems !== []) {
            throw new InsufficientStockException($problems);
        }

        return $next($context);
    }
}
