<?php

namespace App\DataTransferObjects;

use App\DataTransferObjects\OrderItemDTO;
use App\Http\Requests\StoreOrderRequest;
use App\Support\Enums\DiscountType;

class OrderDTO
{
    public function __construct(
        public readonly int $customerId,
        public readonly string $orderDate,
        public readonly ?string $notes,
        public readonly ?DiscountType $discountType,
        public readonly float $discountValue,
        public readonly array $items,
    ) {}

    public static function fromRequest(StoreOrderRequest $request): self
    {
        $discountValue = (float) ($request->discount_value ?? 0);

        return new self(
            customerId: (int) $request->customer_id,
            orderDate: $request->order_date,
            notes: $request->notes,
            discountType: $discountValue > 0 ? DiscountType::tryFrom($request->discount_type ?? '') : null,
            discountValue: $discountValue,
            items: array_map(fn(array $item) => OrderItemDTO::fromArray($item), $request->items),
        );
    }
}
