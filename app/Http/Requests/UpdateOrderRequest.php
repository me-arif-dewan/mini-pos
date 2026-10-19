<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\Validator;

class UpdateOrderRequest extends StoreOrderRequest
{
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                if ($this->input('discount_type') === 'percent' && (float) $this->input('discount_value') > 100) {
                    $validator->errors()->add('discount_value', 'A percentage discount cannot be more than 100.');
                }

                foreach ($this->input('items', []) as $index => $item) {
                    if (($item['discount_type'] ?? null) === 'percent' && (float) ($item['discount_value'] ?? 0) > 100) {
                        $validator->errors()->add("items.$index.discount_value", 'A percentage discount cannot be more than 100.');
                    }
                }

                if (! $this->input('items')) {
                    $validator->errors()->add('items', 'Orders must have at least one item.');
                }

                $order = $this->route('order');
                $order->loadMissing('items');

                $existingQty = $order->items->keyBy('product_id')->map(fn($i) => $i->quantity);

                $products = Product::whereIn('id', collect($this->input('items'))->pluck('product_id'))
                    ->get()
                    ->keyBy('id');

                foreach ($this->input('items') as $index => $item) {
                    $product    = $products[$item['product_id']];
                    $alreadyHad = $existingQty->get($item['product_id'], 0);
                    $available  = $product->stock_quantity + $alreadyHad;

                    if ((int) $item['quantity'] > $available) {
                        $validator->errors()->add(
                            "items.$index.quantity",
                            "Only {$product->stock_quantity} in stock for {$product->name}."
                        );
                    }
                }
            },
        ];
    }
}
