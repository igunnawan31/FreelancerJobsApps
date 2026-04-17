<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'project_name' => 'required|string|max:25|unique:projects,project_name',
            'project_description' => 'required|string|max:255',
            'project_deadline' => 'required|date',
            'user_id' => 'nullable|exists:users,user_id',
            'client_id' => 'nullable|exists:users,user_id'
        ];
    }
}
