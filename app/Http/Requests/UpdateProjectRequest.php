<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
            'project_name'        => 'required|string|max:255|unique:projects,project_name',
            'project_description' => 'required|string|max:255',
            'project_deadline'    => 'required|date',
            'user_id'             => 'nullable|exists:users,user_id',
            'client_id'           => 'nullable|exists:users,user_id',

            'skill_ids'   => 'nullable|array',
            'skill_ids.*' => 'exists:skills,skill_id',

            'attachments'         => 'nullable|array|max:5',
            'attachments.*' => [
                'file',
                'max:1024000',
                'mimes:pdf,png,jpg,jpeg,zip,clip,psd',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'attachments.*.mimes' => 'Only PDF, images, and ZIP files are allowed.',
            'attachments.*.max'   => 'Each file must not exceed 1GB.',
            'attachments.max'     => 'You can upload a maximum of 5 files at once.',
        ];
    }
}
