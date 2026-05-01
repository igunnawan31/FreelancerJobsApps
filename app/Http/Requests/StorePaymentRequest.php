<?php

namespace App\Http\Requests;

use App\Enums\PaymentEnums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => ['required', new Enum(PaymentMethod::class)],
            'payment_attachments' => 'required|array|min:1|max:5',
            'payment_attachments.*' => [
                'file',
                'max:1024',
                'mimes:pdf,png,jpg,jpeg',
            ],
            'note' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_attachments.*.mimes' => 'Only PDF and images are allowed.',
            'payment_attachments.*.max'   => 'Each file must not exceed 1 MB.',
            'payment_attachments.max'     => 'You can upload a maximum of 5 files at once.',
        ];
    }
}
