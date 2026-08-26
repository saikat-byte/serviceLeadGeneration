<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\ServiceRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Enforce policy check for creation
        return $this->user()->can('create', ServiceRequest::class);
    }

    public function rules(): array
    {
        return [
            'service_id'         => ['required', 'exists:services,id'],
            'service_variant_id' => ['nullable', 'exists:service_variants,id'],
            'location_id'        => [
                'required', 
                Rule::exists('locations', 'id')->where(function ($query) {
                    return $query->where('user_id', $this->user()->id);
                }),
            ],
            
            'urgency'            => ['required', 'in:normal,urgent,emergency'],
            'preferred_at'       => ['nullable', 'date', 'after:now'],
            'budget_min'         => ['nullable', 'numeric', 'min:0'],
            'budget_max'         => ['nullable', 'numeric', 'gte:budget_min'],
            'description'        => ['nullable', 'string', 'max:1000'],
            'requirements'       => ['nullable', 'array'],
        ];
    }
}