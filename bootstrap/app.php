<?php

use App\Exceptions\BusinessRuleException;
use App\Helpers\ApiResponse;
use App\Http\Middleware\EnsureAcceptJsonMiddleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('api', [
            HandleCors::class,
            EnsureAcceptJsonMiddleware::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        $response = new ApiResponse;

        // Integration::handles($exceptions);

        // 404
        $exceptions->renderable(function (NotFoundHttpException $ex, $request) use ($response) {
            Log::warning($ex->getMessage());

            if ($request->is("api/*")) {
                return $response->notFoundResponse('The requested URL was not found.');
            }
        });

        // Invalid method
        $exceptions->renderable(function (MethodNotAllowedHttpException $ex, $request) use ($response) {
            Log::warning($ex->getMessage());

            if ($request->is("api/*")) {
                return $response->unauthorizedResponse('Invalid API method.', 422);
            }
        });

        // Unauthenticated
        $exceptions->renderable(function (AuthenticationException $ex, $request) use ($response) {
            Log::warning($ex->getMessage());

            if ($request->is("api/*")) {
                return $response->unauthorizedResponse(
                    'Authentication failed. Please login or create an account to continue',
                    401
                );
            }
        });

        // Unauthorized
        $exceptions->renderable(function (AuthorizationException $ex, $request) use ($response) {
            Log::warning($ex->getMessage());

            if ($request->is("api/*")) {
                return $response->unauthorizedResponse('You do not have permission to access this resource.');
            }
        });

        // Too many requests
        $exceptions->renderable(function (TooManyRequestsHttpException $ex, $request) use ($response) {
            Log::warning($ex->getMessage());
            if ($request->is("api/*")) {
                return $response->unauthorizedResponse('Too many attempts. Please try again in a while.', 429);
            }
        });

        // Generic HttpException
        $exceptions->renderable(function (HttpException $ex, $request) use ($response) {
            Log::warning($ex->getMessage());

            if ($request->is("api/*")) {
                return $response->unauthorizedResponse($ex->getMessage(), $ex->getStatusCode());
            }
        });

        // Business rule
        $exceptions->renderable(function (BusinessRuleException $ex, $request) use ($response) {
            Log::warning($ex->getMessage());

            if ($request->is("api/*")) {
                if (!empty($ex->getErrors())) {
                    return $response->errorResponse(
                        $ex->getMessage(),
                        $ex->getErrors(),
                        422
                    );
                }

                return $response->errorMessageResponse($ex->getMessage(), 422);
            }
        });

        // Catch-all
        $exceptions->renderable(function (Throwable $ex, $request) use ($response) {
            if ($ex instanceof ValidationException || $ex instanceof BusinessRuleException) {
                return;
            }

            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());

            // if (app()->bound('sentry')) {
            //     \Sentry\captureException($ex);
            // }

            if ($request->is("api/*")) {
                if (App::environment('local')) {
                    return $response->errorMessageResponse(
                        'Something went wrong. Please try again later. ' . $ex->getMessage()
                    );
                } else {
                    return $response->errorMessageResponse('Something went wrong. Please try again later.');
                }
            }
        });
    })
    ->create();
