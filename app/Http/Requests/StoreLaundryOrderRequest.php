<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaundryOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // MUST BE TRUE
    }

    public function rules(): array
    {
        return [
            'customer_name'     => ['required', 'string', 'max:100'],
            'contact_number'    => ['required', 'string', 'regex:/^[+0-9() .-]{10,20}$/'],
            'pickup_date'       => ['required', 'date', 'after_or_equal:today'],
            'payment_status'    => ['nullable', 'in:Unpaid,Paid'],
            'express_service'   => ['nullable', 'boolean'],

            'items'                     => ['required', 'array', 'min:1'],
            'items.*.service_type_id'   => ['required', 'exists:service_types,id'],
            'items.*.weight_kg'         => ['required', 'numeric', 'min:0.1'],
        ];
    }

    public function messages(): array
    {
        return [
            'contact_number.regex'       => 'Enter a valid contact number using 10–11 digits.',
            'pickup_date.after_or_equal' => 'Pickup date cannot be in the past.',
            'items.required'             => 'At least one service item is required.',
            'items.*.weight_kg.min'      => 'Weight must be greater than 0.',
        ];
    }
}