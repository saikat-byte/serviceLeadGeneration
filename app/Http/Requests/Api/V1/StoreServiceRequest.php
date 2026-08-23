<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // We assume authentication is handled by Sanctum
    }

    public function rules(): array
    {
        return [
            'service_id'         => ['required', 'exists:services,id'],
            'service_variant_id' => ['nullable', 'exists:service_variants,id'],
            'location_id'        => ['required', 'exists:locations,id'],
            'urgency'            => ['required', 'in:normal,urgent,emergency'],
            'preferred_at'       => ['nullable', 'date', 'after:now'],
            'budget_min'         => ['nullable', 'numeric', 'min:0'],
            'budget_max'         => ['nullable', 'numeric', 'gte:budget_min'],
            'description'        => ['nullable', 'string', 'max:1000'],
            'requirements'       => ['nullable', 'array'],
        ];
    }
}