<?php

namespace App\Providers;

use App\Exceptions\ApiException;
use App\Enums\ApiErrorCode;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register anything you'll later want to inject.
     */
    public function register(): void
    {
        // Singleton for the response builder, if you want to use it outside controllers
        $this->app->singleton(\App\Support\ApiResponseBuilder::class);
    }

    /**
     * Boot: register global JSON error rendering for API requests.
     */
    public function boot(): void
    {
        $this->registerExceptionRendering();
    }

    protected function registerExceptionRendering(): void
    {
        // Only render JSON for requests that expect JSON (API routes, XHR, Accept: application/json)
        if (! $this->app->runningInConsole()) {
            $this->renderApiExceptions();
        }
    }

    protected function renderApiExceptions(): void
    {
        $handler = $this->app->make(\Illuminate\Contracts\Debug\ExceptionHandler::class);

        /* -------- 1. Our own ApiException -------- */
        $handler->renderable(function (ApiException $e, Request $request) {
            if ($this->wantsJson($request)) {
                return $e->render();
            }
        });

        /* -------- 2. Validation -------- */
        $handler->renderable(function (ValidationException $e, Request $request) {
            if ($this->wantsJson($request)) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'code'    => ApiErrorCode::VALIDATION_FAILED->value,
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        /* -------- 3. Authentication -------- */
        $handler->renderable(function (AuthenticationException $e, Request $request) {
            if ($this->wantsJson($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                    'code'    => ApiErrorCode::UNAUTHENTICATED->value,
                ], 401);
            }
        });

        /* -------- 4. Authorization -------- */
        $handler->renderable(function (AuthorizationException $e, Request $request) {
            if ($this->wantsJson($request)) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'This action is unauthorized.',
                    'code'    => ApiErrorCode::FORBIDDEN->value,
                ], 403);
            }
        });

        /* -------- 5. Model not found -------- */
        $handler->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($this->wantsJson($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.',
                    'code'    => ApiErrorCode::NOT_FOUND->value,
                ], 404);
            }
        });

        /* -------- 6. Route not found -------- */
        $handler->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($this->wantsJson($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint not found.',
                    'code'    => ApiErrorCode::NOT_FOUND->value,
                ], 404);
            }
        });

        /* -------- 7. Method not allowed -------- */
        $handler->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($this->wantsJson($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method not allowed.',
                    'code'    => ApiErrorCode::METHOD_NOT_ALLOWED->value,
                ], 405);
            }
        });

        /* -------- 8. Throttling -------- */
        $handler->renderable(function (TooManyRequestsHttpException $e, Request $request) {
            if ($this->wantsJson($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please try again later.',
                    'code'    => ApiErrorCode::TOO_MANY_REQUESTS->value,
                ], 429);
            }
        });

        /* -------- 9. DB errors (don't leak SQL) -------- */
        $handler->renderable(function (QueryException $e, Request $request) {
            if ($this->wantsJson($request)) {
                Log::error('Database error', [
                    'message' => $e->getMessage(),
                    'sql'     => $e->getSql(),
                    'bindings'=> $e->getBindings(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => app()->isProduction()
                        ? 'A database error occurred.'
                        : $e->getMessage(),
                    'code'    => ApiErrorCode::SERVER_ERROR->value,
                ], 500);
            }
        });

        /* -------- 10. Catch-all for any other HTTP exception -------- */
        $handler->renderable(function (HttpExceptionInterface $e, Request $request) {
            if ($this->wantsJson($request)) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'HTTP error.',
                    'code'    => ApiErrorCode::UNKNOWN->value,
                ], $e->getStatusCode());
            }
        });

        /* -------- 11. Final safety net: unhandled Throwable -------- */
        $handler->renderable(function (Throwable $e, Request $request) {
            if ($this->wantsJson($request)) {
                Log::error('Unhandled exception', [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                    'trace'   => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => app()->isProduction()
                        ? 'Something went wrong. Please try again.'
                        : $e->getMessage(),
                    'code'    => ApiErrorCode::SERVER_ERROR->value,
                ], 500);
            }
        });
    }

    protected function wantsJson(Request $request): bool
    {
        return $request->is('api/*')
            || $request->expectsJson()
            || $request->ajax()
            || $request->wantsJson();
    }
}