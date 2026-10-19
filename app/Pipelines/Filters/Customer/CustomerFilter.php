<?php

namespace App\Pipelines\Filters\Customer;

use App\Pipelines\BaseFilterPipeline;
use App\Pipelines\Filters\Customer\Components\Search;

class CustomerFilter extends BaseFilterPipeline
{
    protected function getFilters(): array
    {
        return [
            Search::class,
        ];
    }
}
