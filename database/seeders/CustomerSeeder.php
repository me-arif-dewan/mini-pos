<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['Rahim Traders', Str::slug('Rahim Traders'), 'rahim@rahimtraders.test', '+880 1711 000001', 'House 14, Road 5, Dhanmondi, Dhaka'],
            ['Nusrat Electronics', Str::slug('Nusrat Electronics'), 'orders@nusrat-electronics.test', '+880 1711 000002', '22 Bijoy Sarani, Tejgaon, Dhaka'],
            ['Greenleaf Stationery', Str::slug('Greenleaf Stationery'), 'hello@greenleaf.test', '+880 1711 000003', 'Shop 7, New Market, Dhaka'],
            ['Tanvir Hossain', Str::slug('Tanvir Hossain'), 'tanvir.h@example.test', '+880 1711 000004', 'Flat 6B, Uttara Sector 11, Dhaka'],
            ['Bluebird Cafe', Str::slug('Bluebird Cafe'), 'accounts@bluebird.test', '+880 1711 000005', '9 Gulshan Avenue, Gulshan 1, Dhaka'],
            ['Sadia Rahman', Str::slug('Sadia Rahman'), 'sadia.r@example.test', '+880 1711 000006', '31 Agrabad C/A, Chattogram'],
        ];

        foreach ($customers as [$name, $slug, $email, $phone, $address]) {
            Customer::updateOrCreate(['email' => $email], compact('name', 'slug', 'phone', 'address'));
        }
    }
}
