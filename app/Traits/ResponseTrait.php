<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    public function success($data = null, $message = null, $code = 200, $success = true, $extras = []): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'data'    => $data,
            'message' => $message,
            ...$extras,
        ], $code);
    }

    public function error($message = null, $code = 400, $success = false): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
        ], $code);
    }
}
