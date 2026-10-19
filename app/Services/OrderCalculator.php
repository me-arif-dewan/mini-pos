<?php

namespace App\Services;

use App\Contracts\Order\OrderTotalsCalculator;

class OrderCalculator implements OrderTotalsCalculator
{
    /**
     * @param  array<int, array{unit_price: float|int|string, quantity: int, discount_type?: ?string, discount_value?: float|int|string|null}>  $lines
     * @return array{lines: array<int, array<string, mixed>>, subtotal: float, line_discount_total: float, order_discount: float, discount_total: float, taxable_amount: float, tax_rate: float, tax_amount: float, grand_total: float}
     */
    public function calculate(array $lines, ?string $orderDiscountType = null, float|int|string|null $orderDiscountValue = 0, ?float $taxRate = null): array
    {
        $taxRate ??= (float) config('pos.tax_rate');

        $computed = [];
        $subtotal = 0.0;
        $lineDiscountTotal = 0.0;
        $linesNet = 0.0;

        foreach ($lines as $key => $line) {
            $gross = round((float) $line['unit_price'] * (int) $line['quantity'], 2);
            $discount = $this->discountAmount($gross, $line['discount_type'] ?? null, $line['discount_value'] ?? 0);
            $net = round($gross - $discount, 2);

            $computed[$key] = $line + [
                'gross' => $gross,
                'discount_amount' => $discount,
                'line_total' => $net,
            ];

            $subtotal += $gross;
            $lineDiscountTotal += $discount;
            $linesNet += $net;
        }

        $subtotal = round($subtotal, 2);
        $lineDiscountTotal = round($lineDiscountTotal, 2);
        $linesNet = round($linesNet, 2);

        $orderDiscount = $this->discountAmount($linesNet, $orderDiscountType, $orderDiscountValue);
        $discountTotal = round($lineDiscountTotal + $orderDiscount, 2);
        $taxable = round($subtotal - $discountTotal, 2);
        $tax = round($taxable * $taxRate / 100, 2);

        return [
            'lines' => $computed,
            'subtotal' => $subtotal,
            'line_discount_total' => $lineDiscountTotal,
            'order_discount' => $orderDiscount,
            'discount_total' => $discountTotal,
            'taxable_amount' => $taxable,
            'tax_rate' => $taxRate,
            'tax_amount' => $tax,
            'grand_total' => round($taxable + $tax, 2),
        ];
    }

    public function discountAmount(float $base, ?string $type, float|int|string|null $value): float
    {
        $value = (float) $value;

        if ($value <= 0 || $base <= 0 || $type === null) {
            return 0.0;
        }

        $amount = $type === 'percent'
            ? round($base * min($value, 100) / 100, 2)
            : $value;

        return round(min($amount, $base), 2);
    }
}
