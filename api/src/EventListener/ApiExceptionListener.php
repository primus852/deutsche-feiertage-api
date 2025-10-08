<?php

namespace App\EventListener;

use App\Exception\ApiException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener]
readonly class ApiExceptionListener
{

    public function __construct(string $environment, private LoggerInterface $logger)
    {
        ApiException::setEnvironment($environment);
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ApiException) {
            return;
        }

        $statusCode = $exception->getCode();

        $responseData = [
            'error' => [
                'message' => $exception->getMessage(),
                'status' => $statusCode,
                'code' => $exception->data['code'] ?? 'INTERNAL_SERVER_ERROR',
            ]
        ];

        // Add additional data if available
        if ($exception->data) {
            $responseData = array_merge($responseData, $exception->data);
        }

        if($statusCode === 404){
            $this->logger->warning($exception->getMessage(), [$exception->data]);
        }else{
            $this->logger->error($exception->getMessage(), [$exception->data]);
        }


        $response = new JsonResponse($responseData, $statusCode);
        $event->setResponse($response);
    }
}
