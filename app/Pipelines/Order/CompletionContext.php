<?php

namespace App\Pipelines\Order;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;

final class CompletionContext
{
    public ?Order $order = null;

    public Collection $products;

    public function __construct(public readonly int $orderId)
    {
        $this->products = new Collection;
    }
}
