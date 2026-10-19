<?php

namespace App\Http\Responses;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;

class OrderFormResponse implements Responsable
{
    public function __construct(private readonly ?Order $order = null) {}

    public function toResponse($request)
    {
        $data = [
            'customers' => Customer::orderBy('name')->get(),
            'products'  => Product::active()->orderBy('name')->get(),
            'order'     => $this->order,
        ];

        return view('orders.form', $data);
    }
}
