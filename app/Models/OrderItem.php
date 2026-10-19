<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Support\Enums\DiscountType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends BaseModel
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'unit_price',
        'quantity',
        'discount_type',
        'discount_value',
        'discount_amount',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'discount_type' => DiscountType::class,
            'unit_price' => 'decimal:2',
            'quantity' => 'integer',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
