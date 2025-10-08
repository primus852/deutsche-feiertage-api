<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ApiException extends HttpException
{
    private static ?string $environment = null;

    /** @var array<string,mixed> */
    public array $data {
        get {
            return $this->data;
        }
    }

    public function __construct(
        int        $statusCode,
        string     $message,
        array      $data = [],
        ?Throwable $previous = null,
        array      $headers = []
    ) {
        $this->data = $data;
        parent::__construct($statusCode, $message, $previous, $headers, $statusCode);
    }

    /**
     * Set the current environment (e.g. prod, dev, test)
     */
    public static function setEnvironment(string $env): void
    {
        self::$environment = $env;
    }

    private static function isProduction(): bool
    {
        return self::$environment === 'prod';
    }

    /** 404 */
    public static function notFound(string $entityType, string $id, string $identifier = 'ID'): self
    {
        $errorCode = strtoupper($entityType) . '_NOT_FOUND';
        $message = sprintf('%s not found', $entityType);
        $detailMessage = sprintf('%s with %s %s not found', $entityType, $identifier, $id);

        return new self(
            404,
            $errorCode,
            [
                'message' => $detailMessage,
                'error' => $errorCode,
            ]
        );
    }

    /** 400 */
    public static function badRequest(string $message, string $errorCode, array $additionalData = []): self
    {
        $data = [
            'message' => $message,
            'error' => $errorCode,
        ];

        if (!self::isProduction() && !empty($additionalData)) {
            $data = array_merge($data, $additionalData);
        }

        return new self(400, $message, $data);
    }

    /** 409 */
    public static function duplicate(string $message, string $errorCode, array $additionalData = []): self
    {
        // Delay scraping of existing mails
        sleep(5);
        $data = [
            'message' => $message,
            'error' => $errorCode,
        ];

        if (!empty($additionalData) && !self::isProduction()) {
            $data = array_merge($data, $additionalData);
        }

        return new self(409, $errorCode, $data);
    }

    /** 400 (DB query error) */
    public static function database(string $entityType, array $additionalData = []): self
    {
        $m = sprintf('Error when querying %s in database', $entityType);

        $data = [
            'message' => $m,
            'error' => 'DATABASE_ERROR',
        ];

        if (!empty($additionalData) && !self::isProduction()) {
            $data = array_merge($data, $additionalData);
        }

        return new self(400, 'DATABASE_ERROR', $data);
    }

    /** 500 */
    public static function internalServerError(string $message, string $errorCode, array $additionalData = []): self
    {
        $data = [
            'message' => $message,
            'error' => $errorCode,
        ];

        if (!empty($additionalData) && !self::isProduction()) {
            $data = array_merge($data, $additionalData);
        }

        return new self(500, $message, $data);
    }

    public static function unauthorized(string $message, string $errorCode, array $additionalData = []): self
    {
        sleep(3); // Delay brute-force attacks
        $data = [
            'message' => $message,
            'error' => $errorCode,
        ];

        if (!empty($additionalData) && !self::isProduction()) {
            $data = array_merge($data, $additionalData);
        }

        return new self(403, $message, $data);
    }
}
