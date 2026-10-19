<?php

namespace App\Contracts\Pipeline;

use Closure;

interface ComponentInterface
{
    public function handle(array $content, Closure $next): mixed;
}
