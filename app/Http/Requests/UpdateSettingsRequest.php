<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return match ($this->query('tab', 'pos')) {
            'company' => [
                'company_name'    => ['required', 'string', 'max:255'],
                'company_address' => ['nullable', 'string', 'max:500'],
                'company_email'   => ['nullable', 'email', 'max:255'],
                'company_phone'   => ['nullable', 'string', 'max:50'],
            ],
            'identifier' => [
                'identifier' => ['required', Rule::in(['uuid', 'slug', 'id'])],
            ],
            default => [ // pos
                'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
                'currency' => ['required', 'string', 'max:5'],
            ],
        };
    }
}
