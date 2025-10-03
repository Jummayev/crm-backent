<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class ErrorException extends Exception
{
    protected int $statusCode;

    protected string $userMessage;

    protected array $errorData;

    public function __construct(
        string $message = 'An error occurred',
        int $statusCode = 500,
        protected string $debugMessage = '',
        array $data = [],
        ?Throwable $previous = null,
        public string $type = 'error'
    ) {
        parent::__construct($message, $statusCode, $previous);
        $this->userMessage = $message;
        $this->statusCode = $statusCode;
        $this->errorData = $data;
    }

    public function render(): JsonResponse
    {
        $data = [
            'status' => $this->type,
            'userMessage' => $this->userMessage,
            'data' => $this->errorData,
        ];
        if (config('app.debug') && $this->debugMessage) {
            $data['debugMessage'] = $this->debugMessage;
        }

        return response()->json($data, $this->statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    public function getErrorData(): array
    {
        return $this->errorData;
    }
}
