<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id'  => 'required|exists:bookings,id',
            'category'    => 'required|string|max:255',
            'description' => 'required|string|max:2000',
        ];
    }
}