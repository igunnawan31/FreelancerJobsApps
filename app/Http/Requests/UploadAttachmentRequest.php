<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadAttachmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'attachments'   => 'required|array|min:1|max:5',
            'attachments.*' => [
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,zip',
            ],
            'comment' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'attachments.*.mimes' => 'Only PDF, Word, Excel, images, and ZIP files are allowed.',
            'attachments.*.max'   => 'Each file must not exceed 10MB.',
            'attachments.max'     => 'You can upload a maximum of 5 files at once.',
        ];
    }
}