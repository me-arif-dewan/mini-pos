<?php

namespace App\DataTransferObjects;

use App\Http\Requests\StoreCustomerRequest;
use Illuminate\Support\Str;

class CustomerDTO
{
    public function __construct(
        public readonly string  $name,
        public readonly string  $slug,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $address,
    ) {}

    public static function fromRequest(StoreCustomerRequest $request): self
    {
        return new self(
            name:    $request->name,
            slug:    Str::slug($request->name),
            email:   $request->email,
            phone:   $request->phone,
            address: $request->address,
        );
    }

    public function toArray(): array
    {
        return [
            'name'    => $this->name,
            'slug'    => $this->slug,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'address' => $this->address,
        ];
    }
}
