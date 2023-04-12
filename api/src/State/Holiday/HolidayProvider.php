<?php

namespace App\State\Holiday;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\Holiday\HolidayAddResponse;
use App\Entity\Holiday;
use App\Exception\DFAException;

readonly class HolidayProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $itemProvider,
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

        /* @var Holiday $holiday */
        $holiday = $this->itemProvider->provide($operation, $uriVariables, $context);

        if ($holiday === null) {
            throw new DFAException('HOLIDAY_NOT_FOUND');
        }

        return new HolidayAddResponse($holiday);
    }
}
