<?php

namespace Modules\FileManager\Http\Requests\Folder;

use Illuminate\Foundation\Http\FormRequest;

class StoreFolderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:0', 'max:255'],
            'parent_id' => ['nullable', 'exists:folders,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'sort' => ['required', 'integer', 'min:-2147483648', 'max:2147483647'],
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
