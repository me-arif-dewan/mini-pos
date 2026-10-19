<?php

namespace App\DataTransferObjects;

use App\Support\Enums\DiscountType;

class OrderItemDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantity,
        public readonly ?DiscountType $discountType,
        public readonly float $discountValue,
    ) {}

    public static function fromArray(array $item): self
    {
        $value = (float) ($item['discount_value'] ?? 0);

        return new self(
            productId: (int) $item['product_id'],
            quantity: (int) $item['quantity'],
            discountType: $value > 0 ? DiscountType::tryFrom($item['discount_type'] ?? '') : null,
            discountValue: $value,
        );
    }
}
