<?php

namespace App\Exceptions;

use App\Enums\ApiErrorCode;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class ApiException extends Exception
{
    protected ApiErrorCode $errorCode;
    protected array $errors;

    public function __construct(
        ApiErrorCode $errorCode,
        ?string $message = null,
        array $errors = [],
        ?Throwable $previous = null
    ) {
        $this->errorCode = $errorCode;
        $this->errors    = $errors;

        parent::__construct(
            $message ?? $errorCode->defaultMessage(),
            $errorCode->httpStatus(),
            $previous
        );
    }

    public function getErrorCode(): ApiErrorCode
    {
        return $this->errorCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'code'    => $this->errorCode->value,
            'errors'  => $this->errors,
        ], $this->errorCode->httpStatus());
    }

    /* ---------- Named constructors — the ones you'll actually use ---------- */

    public static function notFound(ApiErrorCode $code = ApiErrorCode::NOT_FOUND, ?string $message = null): self
    {
        return new self($code, $message);
    }

    public static function validation(array $errors, ?string $message = null): self
    {
        return new self(ApiErrorCode::VALIDATION_FAILED, $message, $errors);
    }

    public static function forbidden(?string $message = null): self
    {
        return new self(ApiErrorCode::FORBIDDEN, $message);
    }

    public static function unauthorized(?string $message = null): self
    {
        return new self(ApiErrorCode::UNAUTHORIZED, $message);
    }

    public static function conflict(ApiErrorCode $code, ?string $message = null, array $errors = []): self
    {
        return new self($code, $message, $errors);
    }

    public static function server(?string $message = null, ?Throwable $previous = null): self
    {
        return new self(ApiErrorCode::SERVER_ERROR, $message, [], $previous);
    }
}