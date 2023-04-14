<?php

namespace App\State\Holiday;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Holiday\HolidayAddRequest;
use App\DTO\Holiday\HolidayResponse;
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
     * @return HolidayResponse
     * @throws DFAException
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): HolidayResponse
    {
        if ($operation instanceof Post) {
            if ($context['operation']->getUriTemplate() === '/admin/holiday') {
                $holiday = $this->_fillData($data);
                $repo = $this->entityManager->getRepository(Holiday::class);
                $repo->save($holiday, true);

                return new HolidayResponse($holiday);
            }
        }

        if ($operation instanceof Patch) {
            if ($context['operation']->getUriTemplate() === '/admin/holiday/{id}') {
                $holiday = $this->_patchHoliday($data, $uriVariables);

                $repo = $this->entityManager->getRepository(Holiday::class);
                $repo->save($holiday, true);

                return new HolidayResponse($holiday);
            }
        }

        if ($operation instanceof Get){
            if ($context['operation']->getUriTemplate() === '/by-date/{date}') {
                dump($data);
                dump($uriVariables);
                die;
            }
        }

        throw new DFAException('NOT_IMPLEMENTED');
    }

    /**
     * @param mixed $data
     * @param array $variables
     * @return Holiday
     * @throws DFAException
     */
    private function _patchHoliday(mixed $data, array $variables): Holiday
    {
        /* @var $holiday Holiday */
        $holiday = $this->entityManager->getRepository(Holiday::class)->find($variables['id']);

        if ($holiday === null) {
            throw new DFAException('HOLIDAY_NOT_FOUND');
        }

        /**
         * Check if there is a Holiday on this day already
         */
        $exists = $this->entityManager->getRepository(Holiday::class)->findByDateParams($data->day, $data->month, $variables['id']);

        if ($exists) {
            throw new DFAException('DUPLICATE_HOLIDAY');
        }

        /**
         * Update whatever is set
         */
        $found = false;
        foreach ($data as $key => $value) {
            if ($value !== null) {
                if ($key !== 'appliesTo') {
                    if ($key === 'day' || $key === 'month' || $key === 'year' || $key === 'name') {
                        $method = 'setHoliday' . ucfirst(strtolower($key));
                        if ($key === 'year' && $value === 0) {
                            $value = null;
                        }
                    } else {
                        $method = 'set' . ucfirst(strtolower($key));
                    }
                    $holiday->$method($value);
                } else {
                    /**
                     * Reset to false
                     */
                    foreach (self::$allowed as $item) {
                        $method = 'set' . ucfirst(strtolower($item));
                        $holiday->$method(false);
                    }

                    /**
                     * Re-Apply from array
                     */
                    foreach (self::$allowed as $item) {
                        $method = 'set' . ucfirst(strtolower($item));
                        $inArray = in_array($item, $data->appliesTo);
                        if ($inArray) {
                            $found = true;
                        }
                        $holiday->$method($inArray || $data->isGeneral);
                    }
                }
            }
        }
        if (!$found && !$holiday->getIsGeneral()) {
            throw new DFAException('INVALID_APPLIES_TO_OR_GENERAL');
        }

        if(!$found && $holiday->getIsGeneral()){
            /**
             * Reset to true
             */
            foreach (self::$allowed as $item) {
                $method = 'set' . ucfirst(strtolower($item));
                $holiday->$method(true);
            }
        }

        return $holiday;

    }

    /**
     * @param HolidayAddRequest $data
     * @return Holiday
     * @throws DFAException
     */
    private function _fillData(HolidayAddRequest $data): Holiday
    {

        $year = null;
        if (isset($data->year) && $data->year !== 0) {
            $year = $data->year;
        }

        /**
         * Check if there is a Holiday on this day already
         */
        $exists = $this->entityManager->getRepository(Holiday::class)->findByDateParams($data->day, $data->month);

        if ($exists !== null) {
            throw new DFAException('HOLIDAY_EXISTS');
        }

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
