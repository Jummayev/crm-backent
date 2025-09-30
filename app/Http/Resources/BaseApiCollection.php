<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseApiCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'status' => 'success',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Customize the pagination information.
     *
     * @return array<string, mixed>
     */
    public function paginationInformation(Request $request, array $paginated, array $default): array
    {
        return [
            'meta' => [
                'current_page' => $paginated['current_page'],
                'from' => $paginated['from'],
                'last_page' => $paginated['last_page'],
                'per_page' => $paginated['per_page'],
                'to' => $paginated['to'],
                'total' => $paginated['total'],
            ],
        ];
    }
}
