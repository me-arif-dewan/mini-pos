<?php

namespace App\DataTransferObjects;

use App\Http\Requests\StoreProductRequest;
use Illuminate\Support\Str;

class ProductDTO
{
    public function __construct(
        public readonly string $sku,
        public readonly string $name,
        public readonly string $slug,
        public readonly float  $price,
        public readonly int    $stockQuantity,
        public readonly bool   $isActive,
    ) {}

    public static function fromRequest(StoreProductRequest $request, bool $defaultActive = true): self
    {
        return new self(
            sku:           $request->sku,
            name:          $request->name,
            slug:          Str::slug($request->name),
            price:         (float) $request->price,
            stockQuantity: (int) $request->stock_quantity,
            isActive:      $request->boolean('is_active', $defaultActive),
        );
    }

    public function toArray(): array
    {
        return [
            'sku'            => $this->sku,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'price'          => $this->price,
            'stock_quantity' => $this->stockQuantity,
            'is_active'      => $this->isActive,
        ];
    }
}
