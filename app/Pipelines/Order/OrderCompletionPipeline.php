<?php

namespace App\Pipelines\Order;

use App\Pipelines\BasePipeline;
use App\Pipelines\Order\Stages\DeductStock;
use App\Pipelines\Order\Stages\EnsureOrderIsPending;
use App\Pipelines\Order\Stages\LockOrder;
use App\Pipelines\Order\Stages\LockProducts;
use App\Pipelines\Order\Stages\MarkOrderCompleted;
use App\Pipelines\Order\Stages\PostSaleToLedger;
use App\Pipelines\Order\Stages\VerifyStockAvailability;
use Illuminate\Support\Facades\DB;

class OrderCompletionPipeline extends BasePipeline
{
    protected function getFilters(): array
    {
        return [
            LockOrder::class,
            EnsureOrderIsPending::class,
            LockProducts::class,
            VerifyStockAvailability::class,
            DeductStock::class,
            MarkOrderCompleted::class,
            PostSaleToLedger::class,
        ];
    }

    protected function finalize(mixed $result): mixed
    {
        return $result;
    }

    public function getResults(mixed $payload): CompletionContext
    {
        return DB::transaction(
            fn() => parent::getResults($payload),
            attempts: 3,
        );
    }
}
