<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct(public readonly array $problems)
    {
        parent::__construct('Insufficient stock: ' . implode('; ', $problems));
    }
}
