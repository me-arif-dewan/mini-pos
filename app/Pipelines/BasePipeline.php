<?php

namespace App\Pipelines;

use Illuminate\Pipeline\Pipeline;

abstract class BasePipeline
{
    abstract protected function getFilters(): array;

    protected function finalize(mixed $result): mixed
    {
        return $result;
    }

    public function getResults(mixed $payload)
    {
        $result = app(Pipeline::class)
            ->send($payload)
            ->through($this->getFilters())
            ->thenReturn();

        return $this->finalize($result);
    }
}
