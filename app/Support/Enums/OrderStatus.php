<?php

namespace App\Support\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-100 text-amber-700 ring-amber-200',
            self::Completed => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
            self::Cancelled => 'bg-rose-100 text-rose-700 ring-rose-200',
        };
    }
}
