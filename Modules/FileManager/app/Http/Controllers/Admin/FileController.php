<?php

namespace Modules\FileManager\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\FileManager\Dto\GeneratePathFileDTO;
use Modules\FileManager\Http\Requests\File\StoreFileRequest;
use Modules\FileManager\Http\Requests\File\UpdateFileRequest;
use Modules\FileManager\Models\File;
use Modules\FileManager\Repository\Interfaces\FileInterface;

class FileController extends Controller
{
    protected mixed $modelClass = File::class;

    public function __construct(private readonly FileInterface $fileRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $this->getQuery($request);
        $data = $query->paginate($request->get('per_page'));

        return okWithPaginateResponse($data);
    }

    public function client(Request $request): JsonResponse
    {
        $query = $this->getQuery($request);
        $data = $query->paginate($request->get('per_page'));

        return okWithPaginateResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFileRequest $request): JsonResponse
    {
        $request->validate([
            'files' => ['required'],
        ]);
        $files = $request->file('files');

        if (is_array($request->file('files'))) {
            foreach ($files as $file) {
                $ext = $file->getClientOriginalExtension();
                if (! in_array(strtolower($ext), config('filemanager.allowed_ext'))) {
                    return badRequestResponse('Unknown extension');
                }
            }

            $response = [];
            foreach ($files as $file) {
                $dto = new GeneratePathFileDTO;
                $dto->file = $file;
                $dto->folder_id = $request->get('folder_id');
                $response[] = $this->fileRepository->uploadFile($dto);
            }

        } else {
            $ext = $files->getClientOriginalExtension();
            if (! in_array(strtolower($ext), config('filemanager.allowed_ext'))) {
                return badRequestResponse('Unknown extension');
            }

            $dto = new GeneratePathFileDTO;
            $dto->file = $files;
            $dto->folder_id = $request->get('folder_id');

            $response = $this->fileRepository->uploadFile($dto);
        }

        return okResponse($response);
    }

    /**
     * Show the specified resource.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $query = $this->getQuery($request);
        $model = $query->where('slug', $slug)->firstOrFail();

        return okResponse($model);
    }

    public function downloadFile(Request $request, string $slug)
    {
        return $this->fileRepository->downloadFile($request, $slug);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFileRequest $request, File $file): JsonResponse
    {
        $data = $request->validated();
        $file->update($data);

        return okResponse($file);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, File $file): JsonResponse
    {
        $file->delete();

        return okResponse($file);
    }
}
