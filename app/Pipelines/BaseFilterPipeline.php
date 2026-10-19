<?php

namespace App\Pipelines;

use App\Pipelines\BasePipeline;

abstract class BaseFilterPipeline extends BasePipeline
{
    protected int $defaultPerPage = 5;
    protected int $maxPerPage = 100;

    protected function finalize(mixed $result): mixed
    {
        return $result['builder']
            ->paginate($this->resolvePerPage())
            ->withQueryString();
    }

    private function resolvePerPage(): int
    {
        $requested = (int) request('per_page', $this->defaultPerPage);

        return min(max($requested, 1), $this->maxPerPage);
    }
}
