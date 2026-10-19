<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends BaseModel
{
    protected $fillable = ['reference', 'entry_date', 'description', 'order_id'];

    protected function casts(): array
    {
        return ['entry_date' => 'date'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }
}
