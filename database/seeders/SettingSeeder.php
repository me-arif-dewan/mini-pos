<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'pos.tax_rate'        => '5',
            'pos.currency_symbol' => '$',
            'pos.identifier'      => 'uuid',
            'company.name'        => 'Northwind Trading Co.',
            'company.address'     => '12 Market Street, Dhaka 1212',
            'company.email'       => 'accounts@northwind.test',
            'company.phone'       => '+880 1700 000000',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
