<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only the authenticated user can change their own password
        return auth()->id() === $this->route('user')->user_id;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|string|current_password',
            'password'         => 'required|string|min:8|confirmed',
        ];
    }
}
