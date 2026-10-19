<?php

namespace App\Pipelines\Order\Stages;

use App\Contracts\Order\CompletionStage;
use App\Models\Order;
use App\Pipelines\Order\CompletionContext;
use Closure;

class LockOrder implements CompletionStage
{
    public function handle(CompletionContext $context, Closure $next): mixed
    {
        $context->order = Order::with(['items', 'customer'])
            ->lockForUpdate()
            ->findOrFail($context->orderId);

        return $next($context);
    }
}
