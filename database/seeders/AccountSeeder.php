<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Support\Enums\AccountType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['1000', 'Cash', Str::slug('Cash'), AccountType::Asset],
            ['1100', 'Accounts Receivable', Str::slug('Accounts Receivable'), AccountType::Asset],
            ['2100', 'Tax Payable', Str::slug('Tax Payable'), AccountType::Liability],
            ['3000', "Owner's Equity", Str::slug("Owner's Equity"), AccountType::Equity],
            ['4000', 'Sales Revenue', Str::slug('Sales Revenue'), AccountType::Revenue],
            ['5000', 'Cost of Goods Sold', Str::slug('Cost of Goods Sold'), AccountType::Expense],
        ];

        foreach ($accounts as [$code, $name, $slug, $type]) {
            Account::updateOrCreate(['code' => $code], ['name' => $name, 'slug' => $slug, 'type' => $type]);
        }
    }
}
