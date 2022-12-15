<?php

namespace App\Traits;

use App\Lib\Constant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;

trait Response
{

    /**
     * @param mixed $payload
     * @param int $httpCode
     * @param array $headers
     * @return JsonResponse
     */
    public static function jsonSuccess(mixed $payload = null, int $httpCode = HttpResponse::HTTP_OK, array $headers = []): JsonResponse
    {

        $params = [
            'message' => 'success',
        ];

        if ((is_array($payload) && empty($payload)) || $payload) {
            $params['data'] = $payload;
        }

        return response()->json($params, $httpCode, $headers);
    }

    /**
     * @param int $code
     * @param string|null $message
     * @param int $httpCode
     * @param array $headers
     * @return JsonResponse
     */
    public static function jsonError(int $code = 0, ?string $message = null, int $httpCode = HttpResponse::HTTP_OK, array $headers = []): JsonResponse
    {
        $params = [
            'error' => [
                "code" => $code,
            ],
        ];

        if ($message) {
            $params['error']['message'] = $message;
        }

        return response()->json($params, $httpCode, $headers);
    }

    /**
     * @return JsonResponse
     */
    public static function jsonUnathorized(): JsonResponse
    {
        return response()->json(['message' => Constant::USER_UNAUTHENTICATED_MESSAGE], HttpResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * @param string|null $message
     *
     * @return JsonResponse
     */
    public static function jsonForbidden(?string $message = null): JsonResponse
    {
        return response()->json(['message' => $message ?? Constant::USER_UNATHORIZED_MESSAGE], HttpResponse::HTTP_FORBIDDEN);
    }

    /**
     * @return JsonResponse
     */
    public static function jsonUnprocessable(): JsonResponse
    {
        return response()->json(null, HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
