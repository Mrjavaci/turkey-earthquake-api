<?php

namespace App\Helpers;

use ArrayAccess;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class Result
{
    public static function success(string $message = ''): JsonResponse
    {
        return response()->json(['message' => $message]);
    }

    public static function fail(string $message, int $responseCode = 400): JsonResponse
    {
        return response()->json(['message' => $message], $responseCode);
    }

    public static function successWithData(?string $message = '', array|Arrayable|ArrayAccess $data = []): JsonResponse
    {
        return response()->json(['message' => $message, 'data' => $data]);
    }

    public static function failWithData(
        string $message = '',
        array|Arrayable $data = [],
        int $responseCode = 400
    ): JsonResponse {
        return response()->json(['message' => $message, 'data' => $data], $responseCode);
    }

    public static function exception(Exception $exception): JsonResponse
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => __('validation.failed'),
                'errors'  => $exception->validator->errors()->toArray(),
            ], 422);
        }

        return response()->json(
            ['message' => $exception->getMessage()],
            static::getResponseCodeFromException($exception)
        );
    }

    public static function getResponseCodeFromException(Exception $exception, int|string $default = 400)
    {
        if (
            ! $exception->getCode() ||
            $exception->getCode() == 0 ||
            ! is_numeric($exception->getCode()) ||
            $exception->getCode() > 599
        ) {
            return $default;
        }

        return $exception->getCode();
    }
}
