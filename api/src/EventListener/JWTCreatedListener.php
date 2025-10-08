<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $user = $event->getUser();

        // Example: Add custom data to the payload
        if (method_exists($user, 'getId')) {
            $payload['userId'] = $user->getId();
        }

        if (method_exists($user, 'getClub')) {
            $payload['clubId'] = $user->getClub()->getId();
        }
        if (method_exists($user, 'getRoles')) {
            $payload['roles'] = $user->getRoles();
        }
        $event->setData($payload);
    }
}

