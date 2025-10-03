<?php

namespace Modules\FileManager\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    const int STATUS_ACTIVE = 1;

    protected $table = 'folders';

    protected $fillable = [
        'name',
        'parent_id',
        'user_id',
        'sort',
    ];
}
