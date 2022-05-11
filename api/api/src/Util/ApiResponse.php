<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse
{

    /**
     * @param string $message
     * @param int $status
     * @param array $data
     * @return JsonResponse
     */
    public static function success(string $message, int $status = 200, array $data = array()): JsonResponse
    {
        return self::_display('success', $status, $message, $data);
    }

    public static function error(string $message, int $status, array $data = array()): JsonResponse
    {
        return self::_display('error', $status, $message, $data);
    }

    public static function exception(ProjectException $exception): JsonResponse
    {
        return new JsonResponse(array(
            'result' => 'error',
            'message' => $exception->getMessage(),
            'debug' => $exception->getDetails(),
        ), $exception->getCode() === 0 ? 500 : $exception->getCode());
    }

    /**
     * @param string $body
     * @return array
     */
    public static function toArray(string $body): array
    {
        return json_decode($body, true);
    }

    /**
     * @param $result
     * @param int $status
     * @param string $message
     * @param array $data
     * @return JsonResponse
     */
    private static function _display($result, int $status, string $message = '', array $data = array()): JsonResponse
    {
        $response = is_array($result) ? new JsonResponse($result) : new JsonResponse(array(
            'result' => $result,
            'message' => $message,
            'data' => $data
        ), $status === 0 ? 500 : $status);


        $response->headers->set('Content-Type', 'application/json; charset=utf-8');

        return $response;

    }

}
