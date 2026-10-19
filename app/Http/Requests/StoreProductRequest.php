<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('product')?->id;

        return [
            'sku'            => ['required', 'string', 'max:40', "unique:products,sku,{$id}"],
            'name'           => ['required', 'string', 'max:255'],
            'price'          => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_active'      => ['boolean'],
        ];
    }
}
