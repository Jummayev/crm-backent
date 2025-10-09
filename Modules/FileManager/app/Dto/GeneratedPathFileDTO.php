<?php

namespace Modules\FileManager\Dto;

class GeneratedPathFileDTO
{
    public string $file_path;

    public string $file_name;

    public string $file_folder;

    public string $file_ext;

    public ?string $origin_name;

    public float $file_size;

    public ?int $folder_id;

    public string $type = 'public';
}
