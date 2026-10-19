<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['KB-101', 'Mechanical Keyboard', Str::slug('Mechanical Keyboard'), 79.00, 40],
            ['MS-201', 'Wireless Mouse', Str::slug('Wireless Mouse'), 24.50, 10],
            ['MN-301', '24" IPS Monitor', Str::slug('24" IPS Monitor'), 149.99, 15],
            ['HD-401', 'Noise Cancelling Headphones', Str::slug('Noise Cancelling Headphones'), 119.00, 25],
            ['WC-501', 'HD Webcam', Str::slug('HD Webcam'), 45.00, 30],
            ['UH-601', 'USB-C Hub 7-in-1', Str::slug('USB-C Hub 7-in-1'), 34.90, 60],
            ['SSD-701', 'Portable SSD 1TB', Str::slug('Portable SSD 1TB'), 89.00, 20],
            ['CH-801', 'Ergonomic Office Chair', Str::slug('Ergonomic Office Chair'), 189.00, 8],
            ['DK-901', 'Standing Desk', Str::slug('Standing Desk'), 329.00, 5],
            ['LP-110', 'Laptop Stand', Str::slug('Laptop Stand'), 29.99, 50],
            ['NB-120', 'Notebook A5 (Pack of 5)', Str::slug('Notebook A5 (Pack of 5)'), 12.00, 120],
            ['PN-130', 'Gel Pen Set (12)', Str::slug('Gel Pen Set (12)'), 8.75, 200],
        ];

        foreach ($products as [$sku, $name, $slug, $price, $stock]) {
            Product::updateOrCreate(['sku' => $sku], [
                'name' => $name,
                'slug' => $slug,
                'price' => $price,
                'stock_quantity' => $stock,
                'is_active' => true,
            ]);
        }
    }
}
