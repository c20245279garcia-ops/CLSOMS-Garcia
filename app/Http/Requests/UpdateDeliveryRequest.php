<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // MUST BE TRUE
    }

    public function rules(): array
    {
        return [
            'delivery_date' => ['sometimes', 'date', 'after_or_equal:today'],
        ];
    }
}