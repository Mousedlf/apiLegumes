<?php

namespace App\EventListener;

use App\Service\KeyService;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ApiKeyListener
{
    private $keyService;

    public function __construct(KeyService $keyService)
    {
        $this->keyService = $keyService;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $apiKey = $request->headers->get('api_key') ?: $request->get('api_key');

        if (!$apiKey) {
            throw new AccessDeniedHttpException('API key missing.');
        }

        if (!$this->keyService->isValidApiKey($apiKey)) {
            throw new AccessDeniedHttpException('Invalid API key.');
        }
    }
}