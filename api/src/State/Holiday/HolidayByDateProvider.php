<?php

namespace App\State\Holiday;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\Holiday\HolidayResponse;
use App\Entity\Holiday;
use App\Exception\DFAException;
use Doctrine\ORM\EntityManagerInterface;

readonly class HolidayByDateProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return object|array|null
     * @throws DFAException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {

        $requestedDate = $uriVariables['date'];

        $holiday = $this->entityManager->getRepository(Holiday::class)->findByDateString($requestedDate);

        return new HolidayResponse($holiday, $requestedDate);
    }
}
