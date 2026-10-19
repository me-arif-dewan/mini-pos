<?php

namespace App\Pipelines\Order\Stages;

use App\Contracts\Order\CompletionStage;
use App\Contracts\Order\SalesLedger;
use App\Pipelines\Order\CompletionContext;
use Closure;

class PostSaleToLedger implements CompletionStage
{
    public function __construct(private readonly SalesLedger $ledger) {}

    public function handle(CompletionContext $context, Closure $next): mixed
    {
        $this->ledger->postOrderCompletion($context->order);

        return $next($context);
    }
}
