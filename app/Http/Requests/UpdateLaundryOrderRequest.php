<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLaundryOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // MUST BE TRUE
    }

    public function rules(): array
    {
        return [
            'laundry_status'  => ['sometimes', 'in:Pending,Processing,Completed,Cancelled'],
            'payment_status'  => ['sometimes', 'in:Unpaid,Paid'],
            'pickup_date'     => ['sometimes', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'laundry_status.in' => 'Invalid laundry status value.',
        ];
    }
}