<?php

namespace App\Http\Responses;

use App\Models\Product;
use Illuminate\Contracts\Support\Responsable;

class ProductFormResponse implements Responsable
{
    public function __construct(private readonly ?Product $product = null) {}

    public function toResponse($request)
    {
        return view('products.form', ['product' => $this->product]);
    }
}
