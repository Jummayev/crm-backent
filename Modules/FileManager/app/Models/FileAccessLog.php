<?php

namespace Modules\FileManager\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\FileManager\Jobs\LogFileAccess;

/**
 * @property int $id
 * @property int $file_id
 * @property int $user_id
 * @property string $ip_address
 * @property string $user_agent
 * @property \Carbon\Carbon $accessed_at
 */
class FileAccessLog extends Model
{
    protected $fillable = [
        'file_id',
        'user_id',
        'ip_address',
        'user_agent',
        'accessed_at',
    ];

    protected function casts(): array
    {
        return [
            'accessed_at' => 'datetime',
        ];
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function logAccess(File $file, int $userId, ?string $ipAddress = null, ?string $userAgent = null): void
    {
        if ($file->visibility === 'private') {
            LogFileAccess::dispatch($file->id, $userId, $ipAddress, $userAgent);
        }
    }
}
