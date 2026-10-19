<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Support\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends BaseModel
{
    protected $fillable = ['name', 'slug', 'email', 'phone', 'address'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function completedOrders(): HasMany
    {
        return $this->hasMany(Order::class)->where('status', OrderStatus::Completed);
    }
}
