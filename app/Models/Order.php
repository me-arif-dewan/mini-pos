<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Support\Enums\DiscountType;
use App\Support\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends BaseModel
{
    protected $fillable = [
        'order_number',
        'invoice_number',
        'customer_id',
        'user_id',
        'status',
        'order_date',
        'discount_type',
        'discount_value',
        'subtotal',
        'discount_total',
        'taxable_amount',
        'tax_rate',
        'tax_amount',
        'grand_total',
        'notes',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'discount_type' => DiscountType::class,
            'order_date' => 'date',
            'completed_at' => 'datetime',
            'discount_value' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'taxable_amount' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function journalEntry(): HasOne
    {
        return $this->hasOne(JournalEntry::class);
    }

    public function resolveRouteBinding($value, $field = null): static
    {
        return static::findByIdentifier((string) $value);
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::Completed;
    }

    public function getOrderDiscountAmountAttribute(): string
    {
        return number_format((float) $this->discount_total - (float) $this->items->sum('discount_amount'), 2, '.', '');
    }
}
