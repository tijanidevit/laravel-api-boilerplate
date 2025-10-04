<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    public function successResponse($message = 'Request completed successfully.', $data = [], $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function successMessageResponse($message = 'Request completed successfully.', $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], $statusCode);
    }

    public function errorResponse($message = 'Unable to complete request. Please try again later.', $errors = [], $statusCode = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    public function errorMessageResponse($message = 'Unable to complete request. Please try again later.', $statusCode = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }

    public function notFoundResponse($message = 'Requested resource not found.', $statusCode = 404): JsonResponse
    {
        return $this->errorMessageResponse($message, $statusCode);
    }

    public function unauthorizedResponse($message = 'Unathorized access to requested resource.', $statusCode = 403): JsonResponse
    {
        return $this->errorMessageResponse($message, $statusCode);
    }

    public function invalidInputResponse($message = 'Invalid input.', $statusCode = 422): JsonResponse
    {
        return $this->errorMessageResponse($message, $statusCode);
    }

    public function paginatedCollection(string $message = 'Collection retrieved successfully', mixed $data = null): JsonResponse
    {
        $meta = [
            'current_page' => $data->currentPage(),
            'has_more_pages' => $data->hasMorePages(),
            'last_page' => $data->lastPage(),
            'next_page_url' => $data->nextPageUrl(),
            'to' => $data->lastItem(),
            'from' => $data->firstItem(),
            'per_page' => $data->perPage(),
            'previous_page_url' => $data->previousPageUrl(),
            'total' => $data->total(),
            'url' => $data->path(),
        ];

        $responseData = array_merge(['success' => true, 'message' => $message], ['data' => $data], $meta);

        return response()->json($responseData, 200);
    }

    public function transformPagination(mixed $data = null): array
    {
        $meta = [
            'current_page' => $data->currentPage(),
            'has_more_pages' => $data->hasMorePages(),
            'last_page' => $data->lastPage(),
            'next_page_url' => $data->nextPageUrl(),
            'per_page' => $data->perPage(),
            'previous_page_url' => $data->previousPageUrl(),
            'total' => $data->total(),
            'url' => $data->path(),
        ];

        $responseData = array_merge(['data' => $data], $meta);
        return $responseData;
    }

    public function createdResponse($message = "Record created successfully", $data = null): JsonResponse {
        return $this->successResponse($message, $data, 201);
    }
}
