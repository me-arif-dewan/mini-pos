<?php

namespace App\Support\Enums;

enum AccountType: string
{
    case Asset = 'asset';
    case Liability = 'liability';
    case Equity = 'equity';
    case Revenue = 'revenue';
    case Expense = 'expense';

    public function isDebitNormal(): bool
    {
        return in_array($this, [self::Asset, self::Expense], true);
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
