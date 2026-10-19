<?php

namespace App\Services;

use App\Contracts\Order\OrderTotalsCalculator;
use App\DataTransferObjects\OrderDTO;
use App\Events\OrderCompleted;
use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Pipelines\Filters\Order\OrderFilter;
use App\Pipelines\Order\CompletionContext;
use App\Pipelines\Order\OrderCompletionPipeline;
use App\Support\Enums\OrderStatus;
use DomainException;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private readonly OrderTotalsCalculator $calculator) {}

    public function get(array $queryParams = [])
    {
        $queryBuilder =  Order::with('customer')->latest();

        $offers = resolve(OrderFilter::class)->getResults([
            'builder' => $queryBuilder,
            'params' => $queryParams
        ]);

        return $offers;
    }

    public function create(OrderDTO $dto, User $user): Order
    {
        return DB::transaction(function () use ($dto, $user) {
            $lines  = $this->resolveLines($dto);
            $totals = $this->calculator->calculate($lines, $dto->discountType?->value, $dto->discountValue);
            $order  = $this->persistOrder($dto, $user, $totals);

            $this->persistItems($order, $totals['lines']);

            return $order;
        });
    }

    private function resolveLines(OrderDTO $dto): array
    {
        $products = Product::whereIn('id', array_column($dto->items, 'productId'))->get()->keyBy('id');

        return array_map(fn($item) => [
            'product_id'     => $item->productId,
            'product_name'   => $products[$item->productId]->name,
            'unit_price'     => (float) $products[$item->productId]->price,
            'quantity'       => $item->quantity,
            'discount_type'  => $item->discountType?->value,
            'discount_value' => $item->discountValue,
        ], $dto->items);
    }

    private function persistOrder(OrderDTO $dto, User $user, array $totals, ?Order $existing = null): Order
    {
        $attributes = [
            'customer_id'    => $dto->customerId,
            'order_date'     => $dto->orderDate,
            'discount_type'  => $dto->discountType,
            'discount_value' => $dto->discountValue,
            'subtotal'       => $totals['subtotal'],
            'discount_total' => $totals['discount_total'],
            'taxable_amount' => $totals['taxable_amount'],
            'tax_rate'       => $totals['tax_rate'],
            'tax_amount'     => $totals['tax_amount'],
            'grand_total'    => $totals['grand_total'],
            'notes'          => $dto->notes,
        ];

        if ($existing) {
            $existing->update($attributes);
            return $existing;
        }

        $order = Order::create($attributes + [
            'user_id' => $user->id,
            'status'  => OrderStatus::Pending,
        ]);

        $order->update(['order_number' => 'ORD-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT)]);

        return $order;
    }

    private function persistItems(Order $order, array $lines): void
    {
        $order->items()->createMany(array_map(fn(array $line) => [
            'product_id'     => $line['product_id'],
            'product_name'   => $line['product_name'],
            'unit_price'     => $line['unit_price'],
            'quantity'       => $line['quantity'],
            'discount_type'  => $line['discount_type'],
            'discount_value' => $line['discount_value'],
            'discount_amount' => $line['discount_amount'],
            'line_total'     => $line['line_total'],
        ], $lines));
    }

    public function complete(Order $order): Order
    {
        $context = resolve(OrderCompletionPipeline::class)
            ->getResults(new CompletionContext($order->id));

        OrderCompleted::dispatch($context->order);

        return $context->order;
    }

    public function update(Order $order, OrderDTO $dto): Order
    {
        if (! $order->isPending()) {
            throw new DomainException('Only pending orders can be edited.');
        }

        return DB::transaction(function () use ($order, $dto) {
            $lines  = $this->resolveLines($dto);
            $totals = $this->calculator->calculate($lines, $dto->discountType?->value, $dto->discountValue);

            $order->items()->delete();

            $this->persistOrder($dto, $order->user, $totals, $order);
            $this->persistItems($order, $totals['lines']);

            return $order->fresh();
        });
    }

    public function cancel(Order $order): Order
    {
        if (! $order->isPending()) {
            throw new DomainException('Only pending orders can be cancelled.');
        }

        $order->update(['status' => OrderStatus::Cancelled]);

        return $order;
    }
}
