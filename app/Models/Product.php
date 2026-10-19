<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Support\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends BaseModel
{
    protected $fillable = ['sku', 'name', 'slug', 'price', 'stock_quantity', 'is_active'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function soldItems(): HasMany
    {
        return $this->hasMany(OrderItem::class)
            ->whereHas('order', fn($q) => $q->where('status', OrderStatus::Completed));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
