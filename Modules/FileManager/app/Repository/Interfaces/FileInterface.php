<?php

namespace Modules\FileManager\Repository\Interfaces;

use Illuminate\Http\Request;
use Modules\FileManager\Dto\GeneratePathFileDTO;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface FileInterface
{
    public function uploadFile(GeneratePathFileDTO $dto);

    public function downloadFile(Request $request, string $slug): BinaryFileResponse;
}
