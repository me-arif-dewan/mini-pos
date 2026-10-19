<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'order_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],

            'discount_type' => ['nullable', Rule::in(['percent', 'fixed'])],
            'discount_value' => ['nullable', 'numeric', 'min:0', 'max:99999999'],

            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => [
                'required', 'integer', 'distinct',
                Rule::exists('products', 'id')->where('is_active', true),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'items.*.discount_type' => ['nullable', Rule::in(['percent', 'fixed'])],
            'items.*.discount_value' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'items.required' => 'Add at least one product to the order.',
            'items.min' => 'Add at least one product to the order.',
            'items.*.product_id.required' => 'Choose a product for every line.',
            'items.*.product_id.distinct' => 'Each product can only appear once. Increase its quantity instead.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }

    /**
     * Rules that need the database or depend on two fields together.
     * Runs only after the basic rules above have passed.
     *
     * @return array<int, callable>
     */
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

                $products = Product::whereIn('id', collect($this->input('items'))->pluck('product_id'))
                    ->get()
                    ->keyBy('id');

                foreach ($this->input('items') as $index => $item) {
                    $product = $products[$item['product_id']];

                    if ((int) $item['quantity'] > $product->stock_quantity) {
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
