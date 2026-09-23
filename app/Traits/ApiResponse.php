<?php

namespace App\Traits;

use App\Enums\ApiErrorCode;
use App\Exceptions\ApiException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /* =========================================================
     |  SUCCESS RESPONSES
     ========================================================= */

    protected function respondSuccess(
        mixed $data = null,
        string $message = 'Success',
        int $status = Response::HTTP_OK,
        array $meta = []
    ): JsonResponse {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $payload['data'] = $this->normalizeData($data);
        }

        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    protected function respondCreated(
        mixed $data = null,
        string $message = 'Resource created successfully',
        array $meta = []
    ): JsonResponse {
        return $this->respondSuccess($data, $message, Response::HTTP_CREATED, $meta);
    }

    protected function respondNoContent(string $message = 'Deleted successfully'): JsonResponse
    {
        return $this->respondSuccess(null, $message, Response::HTTP_OK);
    }

    protected function respondUpdated(mixed $data = null, string $message = 'Updated successfully'): JsonResponse
    {
        return $this->respondSuccess($data, $message, Response::HTTP_OK);
    }

    /* =========================================================
     |  PAGINATED SUCCESS
     ========================================================= */

    protected function respondPaginated(
        LengthAwarePaginator|Paginator $paginator,
        string $message = 'Success',
        ?callable $transformer = null
    ): JsonResponse {
        $items = $transformer
            ? collect($paginator->items())->map($transformer)->values()
            : $paginator->items();

        $payload = [
            'success' => true,
            'message' => $message,
            'data'    => $items,
            'meta'    => [
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                    'last_page'    => $paginator->lastPage(),
                    'from'         => $paginator->firstItem(),
                    'to'           => $paginator->lastItem(),
                ],
            ],
        ];

        return response()->json($payload);
    }

    /* =========================================================
     |  ERROR RESPONSES
     ========================================================= */

    protected function respondError(
        string $message = 'An error occurred',
        int $status = Response::HTTP_BAD_REQUEST,
        string|ApiErrorCode|null $code = null,
        array $errors = []
    ): JsonResponse {
        $codeValue = $code instanceof ApiErrorCode ? $code->value : ($code ?? ApiErrorCode::UNKNOWN->value);

        $payload = [
            'success' => false,
            'message' => $message,
            'code'    => $codeValue,
        ];

        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    protected function respondValidationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->respondError(
            $message,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            ApiErrorCode::VALIDATION_FAILED,
            $errors
        );
    }

    protected function respondNotFound(string $message = 'Resource not found', ?ApiErrorCode $code = null): JsonResponse
    {
        return $this->respondError(
            $message,
            Response::HTTP_NOT_FOUND,
            $code ?? ApiErrorCode::NOT_FOUND
        );
    }

    protected function respondUnauthorized(string $message = 'Unauthenticated'): JsonResponse
    {
        return $this->respondError($message, Response::HTTP_UNAUTHORIZED, ApiErrorCode::UNAUTHENTICATED);
    }

    protected function respondForbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->respondError($message, Response::HTTP_FORBIDDEN, ApiErrorCode::FORBIDDEN);
    }

    protected function respondServerError(string $message = 'Server error'): JsonResponse
    {
        return $this->respondError($message, Response::HTTP_INTERNAL_SERVER_ERROR, ApiErrorCode::SERVER_ERROR);
    }

    /* =========================================================
     |  HELPERS
     ========================================================= */

    /**
     * Normalize resources / paginators / arrays into plain arrays.
     */
    protected function normalizeData(mixed $data): mixed
    {
        if ($data instanceof JsonResource || $data instanceof ResourceCollection) {
            return $data->resolve();
        }

        if (is_array($data)) {
            return $data;
        }

        if ($data instanceof \JsonSerializable) {
            return $data->jsonSerialize();
        }

        return $data;
    }

    /**
     * Throw an ApiException from inside a controller. The global handler
     * will convert it into the standard error JSON shape.
     */
    protected function fail(ApiErrorCode $code, ?string $message = null, array $errors = []): never
    {
        throw new ApiException($code, $message, $errors);
    }
}