<?php

namespace App\Http\Response;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CustomResponse extends Response
{
    /**
     * @param int $code
     * @param string $message
     * @param array|null $data
     * @param array|null $auth_data
     * @return JsonResponse
     */
    private function buildResponseBody(int $code, string $message, array $data = null, array $auth_data = null): JsonResponse
    {
        return response()->json(array_filter([
            'meta' => [
                'code' => $code,
                'message' => $message,
            ],
            'auth' => $auth_data,
            'data' => $data,
        ]), $code);
    }

    /**
     * @param string $message
     * @param array|null $data
     * @param array|null $auth_data
     * @return JsonResponse
     */
    public function success(string $message = 'Success', array $data = null, array $auth_data = null): JsonResponse
    {
        return $this->buildResponseBody(self::HTTP_OK, $message, $data, $auth_data);
    }

    /**
     * @return JsonResponse
     */
    public function noContext(): JsonResponse
    {
        return $this->buildResponseBody(self::HTTP_NO_CONTENT, 'No content');
    }
}
