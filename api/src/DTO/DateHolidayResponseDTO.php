<?php

namespace App\DTO;

class DateHolidayResponseDTO
{
    /**
     * @param string $date
     * @param bool $isHoliday
     * @param bool $isNational
     * @param HolidayDTO[] $holidays
     * @param HolidayStateDTO[] $applicable
     */
    public function __construct(
        public readonly string $date,
        public readonly bool $isHoliday,
        public readonly bool $isNational,
        public readonly array $holidays,
        public readonly array $applicable
    ) {
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'isHoliday' => $this->isHoliday,
            'isNational' => $this->isNational,
            'holidays' => array_map(static fn(HolidayDTO $holiday) => $holiday->toArray(), $this->holidays),
            'applicable' => array_map(static fn(HolidayStateDTO $state) => $state->toArray(), $this->applicable),
        ];
    }
}
