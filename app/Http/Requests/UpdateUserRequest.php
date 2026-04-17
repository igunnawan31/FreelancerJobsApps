<?php

namespace App\Http\Requests;

use App\Enums\UserEnums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')->user_id;

        return [
            'name'            => 'required|string|max:30',
            'email'           => "required|email|unique:users,email,{$userId},user_id",
            'role'            => ['required', new Enum(UserRole::class)],
            'phone_number'    => "nullable|string|max:13|unique:users,phone_number,{$userId},user_id",
            'profile_picture' => 'nullable|image|max:2048',
            'portfolio'       => 'nullable|url',
            'skill_ids'       => 'nullable|array',
            'skill_ids.*'     => 'exists:skills,skill_id',
        ];
    }
}
