<?php

namespace App\Http\Responses;

use App\Models\Customer;
use Illuminate\Contracts\Support\Responsable;

class CustomerFormResponse implements Responsable
{
    public function __construct(private readonly ?Customer $customer = null) {}

    public function toResponse($request)
    {
        return view('customers.form', ['customer' => $this->customer]);
    }
}
