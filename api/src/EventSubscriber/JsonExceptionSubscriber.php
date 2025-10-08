<?php

namespace App\EventSubscriber;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class JsonExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private bool $debug = false) {}

    public static function getSubscribedEvents(): array
    {
        // High priority so we run before the default error listener
        return [KernelEvents::EXCEPTION => ['onKernelException', 255]];
    }

    /**
     * @throws ReflectionException
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->wantsJson($request)) {
            return; // Let Symfony render HTML if it isn't an API/JSON request
        }

        $e = $event->getThrowable();
        $status = $e instanceof HttpExceptionInterface
            ? $e->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        $payload = [
            'error'   => new ReflectionClass($e)->getShortName(),
            'message' => $this->debug ? $e->getMessage() : (Response::$statusTexts[$status] ?? 'Error'),
        ];

        if ($this->debug) {
            $payload['code']  = $e->getCode();
            $payload['trace'] = array_slice($e->getTrace(), 0, 10);
        }

        $response = new JsonResponse($payload, $status);

        if ($e instanceof HttpExceptionInterface) {
            foreach ($e->getHeaders() as $name => $value) {
                $response->headers->set($name, $value);
            }
        }

        $event->setResponse($response);
        $event->stopPropagation();
    }

    private function wantsJson(Request $request): bool
    {
        // Any of these signals “return JSON”
        return
            str_starts_with($request->getPathInfo(), '/api') ||
            $request->getRequestFormat() === 'json' ||
            $request->headers->has('X-Requested-With') ||
            str_contains((string) $request->headers->get('Accept'), 'application/json');
    }
}
