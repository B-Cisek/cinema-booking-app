<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Enums\ResponseCode;
use Illuminate\Http\JsonResponse;

class JsonResponseFactory
{
    /**
     * @param  array<string, mixed>  $context
     */
    public static function make(ResponseCode $code, array $context = []): JsonResponse
    {
        return new JsonResponse([
            'message' => $code->message(),
            'code' => $code->value,
            ...$context,
        ], $code->status());
    }
}
