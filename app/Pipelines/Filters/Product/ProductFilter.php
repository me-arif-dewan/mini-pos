<?php

namespace App\Pipelines\Filters\Product;

use App\Pipelines\BaseFilterPipeline;
use App\Pipelines\Filters\Product\Components\Search;
use App\Pipelines\Filters\Product\Components\Status;

class ProductFilter extends BaseFilterPipeline
{
    protected function getFilters(): array
    {
        return [
            Status::class,
            Search::class,
        ];
    }
}
