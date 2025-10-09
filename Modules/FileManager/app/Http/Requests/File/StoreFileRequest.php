<?php

namespace Modules\FileManager\Http\Requests\File;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Validator;

class StoreFileRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'files' => ['required', 'array', 'min:1', 'max:'.config('filemanager.max_files_per_upload', 10)],
            'files.*' => [
                //                'required',
                'file',
                'max:'.config('filemanager.max_file_size', 102_400), // 100 MB
                'mimes:'.implode(',', config('filemanager.allowed_ext', [])),
            ],
            'folder_id' => ['nullable', 'exists:folders,id'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $files = $this->file('files');

            if (! $files) {
                return;
            }

            if (! is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $index => $file) {

                $ext = strtolower($file->getClientOriginalExtension());

                $originalName = $file->getClientOriginalName();

                // 1. Blocked extensions check
                if (in_array($ext, config('filemanager.blocked_extensions', []))) {
                    $validator->errors()->add(
                        "files.{$index}",
                        "File extension '{$ext}' is blocked for security reasons."
                    );
                }

                // 3. Double extension check
                if ($this->hasDoubleExtension($originalName)) {
                    $validator->errors()->add(
                        "files.{$index}",
                        'Double extensions are not allowed for security reasons.'
                    );
                }
            }
        });
    }

    /**
     * Check for double extension attacks
     */
    private function hasDoubleExtension(string $filename): bool
    {
        $parts = explode('.', $filename);

        if (count($parts) > 2) {
            $dangerousExts = config('filemanager.blocked_extensions', []);

            if (array_any($parts, fn ($part) => in_array(strtolower($part), $dangerousExts))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'files.required' => 'At least one file is required.',
            'files.array' => 'Files must be uploaded as an array.',
            'files.min' => 'At least one file must be uploaded.',
            'files.max' => 'You can only upload :max files at once.',
            'files.*.required' => 'File is required.',
            'files.*.file' => 'The uploaded item must be a file.',
            'files.*.max' => 'File size cannot exceed :max KB.',
            'files.*.mimes' => 'File type is not allowed.',
            'folder_id.exists' => 'Selected folder does not exist.',
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
