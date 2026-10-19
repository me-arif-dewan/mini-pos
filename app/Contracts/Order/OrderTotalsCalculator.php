<?php

namespace App\Contracts\Order;

interface OrderTotalsCalculator
{
    public function calculate(array $lines, ?string $orderDiscountType = null, float|int|string|null $orderDiscountValue = 0, ?float $taxRate = null): array;

    public function discountAmount(float $base, ?string $type, float|int|string|null $value): float;
}
