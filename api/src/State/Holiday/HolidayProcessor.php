<?php

namespace App\State\Holiday;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Holiday\HolidayAddRequest;
use App\DTO\Holiday\HolidayAddResponse;
use App\Entity\Holiday;
use App\Exception\DFAException;
use Doctrine\ORM\EntityManagerInterface;

class HolidayProcessor implements ProcessorInterface
{
    private static array $allowed = [
        "bw",
        "bay",
        "be",
        "bb",
        "hb",
        "hh",
        "he",
        "mv",
        "ni",
        "nw",
        "rp",
        "sl",
        "sn",
        "st",
        "sh",
        "th",
    ];

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return HolidayAddResponse
     * @throws DFAException
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): HolidayAddResponse
    {
        $holiday = $this->_fillData($data);
        if ($operation instanceof Post) {
            if ($context['operation']->getUriTemplate() === '/admin/add-holiday') {
                $repo = $this->entityManager->getRepository(Holiday::class);
                $repo->save($holiday, true);

                return new HolidayAddResponse($holiday);
            }
        }
        return new HolidayAddResponse($holiday);
    }

    /**
     * @param HolidayAddRequest $data
     * @return Holiday
     * @throws DFAException
     */
    private function _fillData(HolidayAddRequest $data): Holiday
    {

        $year = null;
        if (isset($data->year)) {
            $year = $data->year;
        }

        /**
         * Check if there is a Holiday on this day already
         */
        $exists = $this->entityManager->getRepository(Holiday::class)->findByDateParams($data->day, $data->month, $year);

        $holiday = new Holiday();
        $holiday->setHolidayDay($data->day);
        $holiday->setHolidayMonth($data->month);
        $holiday->setHolidayYear($year);
        $holiday->setIsGeneral($data->isGeneral);
        $holiday->setHolidayName($data->name);

        $found = false;
        foreach (self::$allowed as $item) {
            $method = 'set' . ucfirst(strtolower($item));
            $inArray = in_array($item, $data->appliesTo);
            if ($inArray) {
                $found = true;
            }
            $holiday->$method($inArray || $data->isGeneral);
        }

        if (!$found && !$data->isGeneral) {
            throw new DFAException('INVALID_APPLIES_TO_OR_GENERAL');
        }

        return $holiday;

    }
}
