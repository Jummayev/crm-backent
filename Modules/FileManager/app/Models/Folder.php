<?php

namespace Modules\FileManager\Models;

use Database\Factories\FolderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    const int STATUS_ACTIVE = 1;

    protected $table = 'folders';

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): FolderFactory
    {
        return FolderFactory::new();
    }

    protected $fillable = [
        'name',
        'parent_id',
        'user_id',
        'sort',
    ];
}
