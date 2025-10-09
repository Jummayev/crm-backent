<?php

namespace Modules\FileManager\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\FileManager\Models\File;
use Modules\FileManager\Models\FileAccessLog;

class LogFileAccess implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $fileId,
        public int $userId,
        public ?string $ipAddress = null,
        public ?string $userAgent = null
    ) {
        $this->onQueue('file-access-logs');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $file = File::find($this->fileId);

        if (! $file || $file->visibility !== 'private') {
            return;
        }

        FileAccessLog::create([
            'file_id' => $this->fileId,
            'user_id' => $this->userId,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'accessed_at' => now(),
        ]);
    }
}
