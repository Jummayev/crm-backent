<?php

namespace Modules\FileManager\Repository\Interfaces;

use Illuminate\Http\Request;
use Modules\FileManager\Dto\GeneratePathFileDTO;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface FileInterface
{
    public function uploadFile(GeneratePathFileDTO $dto);

    public function downloadFile(Request $request, string $slug): BinaryFileResponse;

    public function sanitizeFilename(string $filename): string;

    public function hasDoubleExtension(string $filename): bool;

    public function cleanupFiles(array $files): void;
}
