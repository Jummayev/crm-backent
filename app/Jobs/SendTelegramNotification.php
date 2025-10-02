<?php

declare(strict_types=1);

namespace App\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTelegramNotification implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $text,
        public ?string $chatId = null
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $text = str_replace('<br>', "\n", $this->text);
        $text = html_entity_decode(strip_tags($text, '<a><strong><b><em><i><code><pre>'));
        $token = config('telegram.bot_token');
        $chatId = $this->chatId ?? config('telegram.chat_id');

        if (! $token || ! $chatId) {
            Log::warning('Telegram notification skipped: missing configuration');

            return;
        }

        try {
            $response = Http::acceptJson()
                ->timeout(10)
                ->get("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'html',
                    'disable_web_page_preview' => true,
                ]);

            if ($response->failed()) {
                Log::error('Telegram notification failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (Exception $exception) {
            Log::error('Telegram notification exception', [
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Exception $exception): void
    {
        Log::error('Telegram notification job failed after retries', [
            'message' => $exception?->getMessage(),
            'text' => $this->text,
        ]);
    }
}
