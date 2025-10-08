<?php

namespace App\Mapping;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse
{
    public static function success(string $message, int $code, mixed $data = null): JsonResponse
    {
        if ($data === null) {
            return new JsonResponse([
                'message' => $message,
                'code' => $code,
            ]);
        }

        return new JsonResponse([
            'message' => $message,
            'code' => $code,
            'data' => $data
        ]);
    }

    public static function deleted(string $entity, string $id): JsonResponse
    {
        return new JsonResponse([
            'message' => 'DELETED',
            'code' => 200,
            'data' => array(
                'entity' => $entity,
                'id' => $id,
            )
        ]);
    }
}
