<?php

namespace Modules\FileManager\Repository;

use App\Exceptions\ErrorException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\FileManager\Dto\GeneratedPathFileDTO;
use Modules\FileManager\Dto\GeneratePathFileDTO;
use Modules\FileManager\Models\File;
use Modules\FileManager\Repository\Interfaces\FileInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class FileRepository implements FileInterface
{
    /**
     * @throws ErrorException
     * @throws Throwable
     */
    public function uploadFile(GeneratePathFileDTO $dto)
    {
        DB::beginTransaction();
        try {
            $generatedDTO = $this->generatePath($dto);

            $generatedDTO->origin_name = $dto->file->getClientOriginalName();
            $generatedDTO->file_size = (float) $dto->file->getSize();
            $generatedDTO->folder_id = $dto->folder_id;
            $dto->file->move($generatedDTO->file_path, $generatedDTO->file_name.'.'.$generatedDTO->file_ext);
            $file = $this->createFileModel($generatedDTO);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new ErrorException($e->getMessage());
        }

        return $file;
    }

    /**
     * @throws ErrorException
     */
    public function generatePath(GeneratePathFileDTO $generatePathFileDTO): GeneratedPathFileDTO
    {
        $generatedPathFileDTO = new GeneratedPathFileDTO;
        $created_at = time();

        $file = $generatePathFileDTO->file;
        $y = date('Y', $created_at);
        $m = date('m', $created_at);
        $d = date('d', $created_at);
        $h = date('H', $created_at);
        $i = date('i', $created_at);

        $file_hash = Str::random(32);

        $basePath = base_path('static');
        $folderPath = "$y/$m/$d/$h/$i";
        $fullPath = "$basePath/$folderPath";
        if (! is_dir($fullPath)) {
            mkdir($fullPath, 00777, recursive: true);
        }
        if (! is_writable($fullPath)) {
            throw new ErrorException('Path is not writeable');
        }
        $generatedPathFileDTO->file_folder = $folderPath;
        $generatedPathFileDTO->file_name = $file_hash;
        $generatedPathFileDTO->file_ext = $file->getClientOriginalExtension();
        $generatedPathFileDTO->file_path = $fullPath;

        return $generatedPathFileDTO;
    }

    /**
     * @throws ErrorException
     */
    private function createFileModel(GeneratedPathFileDTO $generatedDTO)
    {
        $data = [
            'name' => $generatedDTO->origin_name,
            'description' => $generatedDTO->origin_name,
            'slug' => $generatedDTO->file_name,
            'ext' => $generatedDTO->file_ext,
            'file' => $generatedDTO->file_name.'.'.$generatedDTO->file_ext,
            'folder_id' => $generatedDTO->folder_id,
            'path' => $generatedDTO->file_folder,
            'domain' => config('filemanager.cdn_domain'),
            'user_id' => Auth::guard('api')->id(),
            'size' => $generatedDTO->file_size,
        ];

        try {
            $file = File::query()->create($data);
        } catch (Exception $exception) {
            throw new ErrorException($exception->getMessage());
        }

        return $file;
    }

    public function downloadFile(Request $request, string $slug): BinaryFileResponse
    {
        $file = File::query()->where('slug', $slug)->firstOrFail();

        $link = $file->getDist();
        $headers = ['Content-Type' => 'application/'.$file->ext];

        return response()->download($link, $file->name, $headers);
    }
}
