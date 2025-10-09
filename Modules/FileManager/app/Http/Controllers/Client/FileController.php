<?php

namespace Modules\FileManager\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\FileManager\Dto\GeneratePathFileDTO;
use Modules\FileManager\Http\Requests\File\StoreFileRequest;
use Modules\FileManager\Models\File;
use Modules\FileManager\Repository\Interfaces\FileInterface;

class FileController extends Controller
{
    protected mixed $modelClass = File::class;

    public function __construct(private readonly FileInterface $fileRepository) {}

    /**
     * Upload files
     *
     * Upload one or multiple files to the file manager.
     *
     * @group File Management
     *
     * @authenticated
     *
     * @bodyParam files file[] required Array of files to upload. No-example
     * @bodyParam folder_id integer Folder ID to upload files to. Example: 1
     *
     * @response 200 scenario="Single file uploaded" {
     *   "success": true,
     *   "message": "Success",
     *   "data": [{
     *     "id": 1,
     *     "slug": "abc123def",
     *     "name": "document.pdf",
     *     "path": "/2025/10/document.pdf",
     *     "size": 1024000,
     *     "mime_type": "application/pdf",
     *     "folder_id": 1,
     *     "created_at": "2025-10-03T10:00:00.000000Z",
     *     "updated_at": "2025-10-03T10:00:00.000000Z"
     *   }]
     * }
     * @response 400 scenario="Invalid file extension" {
     *   "success": false,
     *   "message": "File extension 'exe' is blocked for security reasons.",
     *   "data": null
     * }
     * @response 422 scenario="Validation error" {
     *   "message": "The files field is required.",
     *   "errors": {
     *     "files": ["At least one file is required."]
     *   }
     * }
     *
     * @throws \Throwable
     */
    public function store(StoreFileRequest $request): JsonResponse
    {
        $files = $request->file('files');

        $uploadedFiles = [];
        try {
            DB::transaction(function () use ($files, $request, &$uploadedFiles) {
                foreach ($files as $file) {
                    $dto = new GeneratePathFileDTO;
                    $dto->file = $file;
                    $dto->folder_id = $request->get('folder_id');

                    $uploadedFile = $this->fileRepository->uploadFile($dto);
                    $uploadedFiles[] = $uploadedFile;
                }
            });
        } catch (\Throwable $e) {
            // Rollback: delete uploaded files from disk
            $this->fileRepository->cleanupFiles($uploadedFiles);

            throw $e;
        }

        return okResponse($uploadedFiles);
    }

    /**
     * Get file details
     *
     * Retrieve details of a specific file by its slug.
     *
     * @group File Management
     *
     * @authenticated
     *
     * @urlParam slug string required The file slug. Example: abc123def
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Success",
     *   "data": {
     *     "id": 1,
     *     "slug": "abc123def",
     *     "name": "document.pdf",
     *     "path": "/2025/10/document.pdf",
     *     "size": 1024000,
     *     "mime_type": "application/pdf",
     *     "folder_id": 1,
     *     "created_at": "2025-10-03T10:00:00.000000Z",
     *     "updated_at": "2025-10-03T10:00:00.000000Z"
     *   }
     * }
     * @response 404 scenario="File not found" {
     *   "message": "No query results for model [Modules\\FileManager\\Models\\File]."
     * }
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $query = $this->getQuery($request);
        $model = $query->where('slug', $slug)->firstOrFail();

        return okResponse($model);
    }

    /**
     * Download file
     *
     * Download a file by its slug.
     *
     * @group File Management
     *
     * @authenticated
     *
     * @urlParam slug string required The file slug. Example: abc123def
     *
     * @response 200 scenario="File download" "Binary file content"
     * @response 404 scenario="File not found" {
     *   "message": "No query results for model [Modules\\FileManager\\Models\\File]."
     * }
     */
    public function downloadFile(Request $request, string $slug)
    {
        return $this->fileRepository->downloadFile($request, $slug);
    }

    /**
     * Delete file
     *
     * Delete a file from the system.
     *
     * @group File Management
     *
     * @authenticated
     *
     * @urlParam file integer required The file ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Success",
     *   "data": {
     *     "id": 1,
     *     "slug": "abc123def",
     *     "name": "document.pdf",
     *     "path": "/2025/10/document.pdf",
     *     "size": 1024000,
     *     "mime_type": "application/pdf",
     *     "folder_id": 1,
     *     "created_at": "2025-10-03T10:00:00.000000Z",
     *     "updated_at": "2025-10-03T10:00:00.000000Z"
     *   }
     * }
     * @response 404 scenario="File not found" {
     *   "message": "No query results for model [Modules\\FileManager\\Models\\File]."
     * }
     */
    public function destroy(Request $request, File $file): JsonResponse
    {
        $file->delete();

        return okResponse($file);
    }
}
