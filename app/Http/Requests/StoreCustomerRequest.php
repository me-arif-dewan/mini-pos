<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('customer')?->id;

        return [
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['nullable', 'email', 'max:255', "unique:customers,email,{$id}"],
            'phone'   => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
