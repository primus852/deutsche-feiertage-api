<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;


readonly class JWTCreatedListener
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            array(
                'email' => $payload['username']
            )
        );

        $event->setData($payload);
    }
}
