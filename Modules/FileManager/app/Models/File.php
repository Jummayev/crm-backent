<?php

namespace Modules\FileManager\Models;

use App\Exceptions\ErrorException;
use Database\Factories\FileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property string $name
 * @property string $path
 * @property string $slug
 * @property string $ext
 * @property string $file
 * @property string $domain
 * @property int $size
 * @property int $user_id
 * @property int $folder_id
 * @property string $description
 * @property int $sort
 * @property string $type
 * @property-read string $src
 * @property-read array $thumbnails
 */
class File extends Model
{
    use HasFactory;

    protected $table = 'files';

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): FileFactory
    {
        return FileFactory::new();
    }

    protected $fillable = [
        'name',
        'path',
        'slug',
        'ext',
        'file',
        'domain',
        'size',
        'user_id',
        'folder_id',
        'description',
        'sort',
        'type',
    ];

    protected $hidden = [
        'description',
        'sort',
        'folder_id',
        'deleted_at',
        'user_id',
        'updated_at',
        'path',
    ];

    public function isImage(): bool
    {
        return in_array($this->ext, config('filemanager.images_ext', []));
    }

    public function getDist(): string
    {
        return base_path("static/{$this->type}/$this->path/$this->file");
    }

    public function getFullPath(): string
    {
        return "static/{$this->type}/$this->path/$this->file";
    }

    protected $appends = [
        'thumbnails',
        'src',
    ];

    public function getSrcAttribute(): string
    {
        $token = \Modules\FileManager\Services\TokenService::generateToken($this, auth()->id() ?? 0);

        if ($this->type === 'private') {
            return "https://file.{$this->domain}/storage/{$this->slug}/view?token={$token}";
        }

        return "https://file.{$this->domain}/public/{$this->path}/{$this->file}?token={$token}";
    }

    public function getThumbnailsAttribute(): array
    {
        $thumbs = config('filemanager.thumbs');
        $thumbnails = [];
        foreach ($thumbs as &$thumb) {
            $slug = $thumb['slug'];
            $path = base_path("static/{$this->type}/$this->path/".$this->slug.'_'.$slug.'.'.$this->ext);
            if (file_exists($path)) {
                $typePrefix = $this->type === 'public' ? 'public' : 'storage';
                $src = "https://file.{$this->domain}/{$typePrefix}/{$this->path}{$this->slug}_{$slug}.{$this->ext}";
            } else {
                $src = $this->getSrcAttribute();
            }
            $thumbnails[$slug] = $src;
        }

        return $thumbnails;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($file) {
            if (! in_array(Str::lower($file->ext), config('filemanager.allowed_ext'))) {
                $path = $file->path.'/'.$file->file;
                FacadesFile::delete($path);
                throw new ErrorException('Unknown extension', Response::HTTP_BAD_REQUEST);
            }
        });
    }
}
