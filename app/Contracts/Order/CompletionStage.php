<?php

namespace App\Contracts\Order;

use App\Pipelines\Order\CompletionContext;
use Closure;

interface CompletionStage
{
    public function handle(CompletionContext $context, Closure $next): mixed;
}
