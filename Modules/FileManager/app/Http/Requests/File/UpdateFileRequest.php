<?php

namespace Modules\FileManager\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'min:0', 'max:255'],
            'path' => ['nullable', 'string', 'min:0', 'max:255'],
            'slug' => ['nullable', 'string', 'min:0', 'max:255'],
            'ext' => ['nullable', 'string', 'min:0', 'max:255'],
            'file' => ['nullable', 'string', 'min:0', 'max:255'],
            'domain' => ['nullable', 'string', 'min:0', 'max:1000'],
            'size' => ['nullable', 'integer', 'min:-2147483648', 'max:2147483647'],
            'user_id' => ['nullable', 'integer', 'min:-9223372036854775808', 'max:9223372036854775807'],
            'folder_id' => ['nullable', 'integer', 'min:-9223372036854775808', 'max:9223372036854775807'],
            'description' => ['nullable', 'string', 'min:0', 'max:255'],
            'sort' => ['nullable', 'integer', 'min:-2147483648', 'max:2147483647'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
