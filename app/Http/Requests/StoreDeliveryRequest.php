<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // MUST BE TRUE
    }

    public function rules(): array
    {
        return [
            'laundry_order_id' => ['required', 'exists:laundry_orders,id', 'unique:deliveries,laundry_order_id'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'laundry_order_id.unique' => 'This order already has a delivery record.',
            'delivery_date.after_or_equal' => 'Delivery date must not be in the past.',
        ];
    }
}