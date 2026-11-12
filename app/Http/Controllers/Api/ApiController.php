<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class ApiController extends Controller
{
    protected function ok(array $payload = [], int $status = 200): JsonResponse
    {
        return response()->json(['success' => true] + $payload, $status, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function fail(string $error, int $status = 400): JsonResponse
    {
        return response()->json(['success' => false, 'error' => $error], $status, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function str(Request $request, string $key, string $default = ''): string
    {
        return trim((string)$request->input($key, $default));
    }
}
