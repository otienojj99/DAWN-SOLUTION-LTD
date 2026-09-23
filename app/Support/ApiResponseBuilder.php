<?php

namespace App\Support;

use App\Enums\ApiErrorCode;
use App\Exceptions\ApiException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseBuilder
{
    public function success(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    public function paginated(LengthAwarePaginator $paginator, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $paginator->items(),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],
        ]);
    }

    public function error(
        string $message = 'An error occurred',
        int $status = Response::HTTP_BAD_REQUEST,
        string|ApiErrorCode|null $code = null,
        array $errors = []
    ): JsonResponse {
        $codeValue = $code instanceof ApiErrorCode ? $code->value : ($code ?? ApiErrorCode::UNKNOWN->value);

        return response()->json(array_filter([
            'success' => false,
            'message' => $message,
            'code'    => $codeValue,
            'errors'  => $errors ?: null,
        ]), $status);
    }

    public function fail(ApiErrorCode $code, ?string $message = null, array $errors = []): never
    {
        throw new ApiException($code, $message, $errors);
    }
}