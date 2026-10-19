<?php

namespace App\Pipelines\Order\Stages;

use App\Contracts\Order\CompletionStage;
use App\Pipelines\Order\CompletionContext;
use Closure;
use DomainException;

class EnsureOrderIsPending implements CompletionStage
{
    public function handle(CompletionContext $context, Closure $next): mixed
    {
        if (! $context->order->isPending()) {
            throw new DomainException('Only pending orders can be completed.');
        }

        return $next($context);
    }
}
