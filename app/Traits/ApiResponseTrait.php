<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Success Response
     *
     * @param string $message
     * @param array $additionalData
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successResponse(string $message, Collection|array|null $additionalData = null, int $statusCode = 200): JsonResponse
    {
        if ($additionalData instanceof Collection) {
            $additionalData = $additionalData->toArray();
        }
    
        $response = array_merge(['message' => $message], $additionalData ?? []);

        return response()->json($response, $statusCode);
    }

    /**
     * Error Response
     *
     * @param string $message
     * @param int $statusCode
     * @param array $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $statusCode = 400, array $errors = []): JsonResponse
    {
        $response = array_merge(['message' => $message], $errors);

        return response()->json($response, $statusCode);
    }
}
