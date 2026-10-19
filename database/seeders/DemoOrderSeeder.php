<?php

namespace Database\Seeders;

use App\DataTransferObjects\OrderDTO;
use App\DataTransferObjects\OrderItemDTO;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use App\Support\Enums\DiscountType;
use Illuminate\Database\Seeder;


class DemoOrderSeeder extends Seeder
{
    public function run(OrderService $orders): void
    {
        if (Order::exists()) {
            return;
        }

        $user = User::where('email', 'admin@example.com')->firstOrFail();

        foreach (
            [
                ['rahim@rahimtraders.test', [['KB-101', 5, 'percent', 10], ['MS-201', 2]], null, 0],
                ['orders@nusrat-electronics.test', [['MN-301', 3], ['UH-601', 6, 'fixed', 15]], 'percent', 5],
                ['hello@greenleaf.test', [['NB-120', 20], ['PN-130', 30, 'percent', 8]], null, 0],
            ] as $i => [$email, $items, $type, $value]
        ) {
            $order = $this->make($orders, $user, $email, $items, $type, $value, now()->subDays(3 - $i));
            $orders->complete($order);
        }

        $this->make($orders, $user, 'tanvir.h@example.test', [['MS-201', 8], ['LP-110', 2]], null, 0, now());
        $this->make($orders, $user, 'accounts@bluebird.test', [['MS-201', 8], ['HD-401', 1, 'fixed', 10]], 'fixed', 5, now());
        $this->make($orders, $user, 'sadia.r@example.test', [['SSD-701', 2]], null, 0, now());
    }

    private function make(OrderService $orders, User $user, string $email, array $items, ?string $type, float $value, \Illuminate\Support\Carbon $date): Order
    {
        $products = Product::whereIn('sku', array_column($items, 0))->get()->keyBy('sku');

        $dto = new OrderDTO(
            customerId: Customer::where('email', $email)->value('id'),
            orderDate: $date->toDateString(),
            notes: null,
            discountType: $type ? DiscountType::from($type) : null,
            discountValue: $value,
            items: array_map(fn(array $row) => new OrderItemDTO(
                productId: $products[$row[0]]->id,
                quantity: $row[1],
                discountType: isset($row[2]) ? DiscountType::from($row[2]) : null,
                discountValue: (float) ($row[3] ?? 0),
            ), $items),
        );

        return $orders->create($dto, $user);
    }
}
