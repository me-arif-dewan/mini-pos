<?php

namespace App\Pipelines\Order\Stages;

use App\Contracts\Order\CompletionStage;
use App\Pipelines\Order\CompletionContext;
use App\Support\Enums\OrderStatus;
use Closure;

class MarkOrderCompleted implements CompletionStage
{
    public function handle(CompletionContext $context, Closure $next): mixed
    {
        $context->order->update([
            'status' => OrderStatus::Completed,
            'completed_at' => now(),
            'invoice_number' => 'INV-' . str_pad((string) $context->order->id, 6, '0', STR_PAD_LEFT),
        ]);

        return $next($context);
    }
}
