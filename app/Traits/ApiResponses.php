<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    protected function success($data = null, string $message = 'Success', int $statusCode = 200) : JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function error(string $message = 'Error', int $statusCode = 400, $errors = null) : JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors) $response['errors'] = $errors;

        return response()->json($response, $statusCode);
    }
}
