<?php

namespace App\Pipelines\Filters\Order;

use App\Pipelines\BaseFilterPipeline;
use App\Pipelines\Filters\Order\Components\Search;
use App\Pipelines\Filters\Order\Components\Status;

class OrderFilter extends BaseFilterPipeline
{
    protected function getFilters(): array
    {
        return [
            Status::class,
            Search::class,
        ];
    }
}
