<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }
    
    public function rules(): array
    {
        return [
            'attachments'   => 'required|array|min:1|max:5',
            'attachments.*' => [
                'file',
                'max:1024000',
                'mimes:pdf,png,jpg,jpeg,zip,clip,psd',
            ],
            'comment' => 'nullable|string|max:255',
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