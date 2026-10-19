<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Support\Enums\AccountType;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends BaseModel
{
    protected $fillable = ['code', 'name', 'slug', 'type', 'balance'];

    protected function casts(): array
    {
        return [
            'type' => AccountType::class,
            'balance' => 'decimal:2',
        ];
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }
}
