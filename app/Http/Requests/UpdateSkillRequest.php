<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSkillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $skillId = $this->route('skill')->skill_id;

        return [
            'skill_name' => ['required','string','max:25',
                Rule::unique('skills', 'skill_name')->ignore($skillId, 'skill_id'),
            ],
            'skill_description' => 'nullable|string|max:255',
        ];
    }
}
