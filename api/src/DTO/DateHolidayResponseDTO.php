<?php

namespace App\DTO;

readonly class DateHolidayResponseDTO
{
    /**
     * @param string $date
     * @param bool $isHoliday
     * @param bool $isNational
     * @param ?HolidayDTO $holiday
     * @param HolidayStateDTO[] $applicable
     */
    public function __construct(
        public string      $date,
        public bool        $isHoliday,
        public bool        $isNational,
        public ?HolidayDTO $holiday,
        public array       $applicable
    ) {
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'isHoliday' => $this->isHoliday,
            'isNational' => $this->isNational,
            'holiday' => $this->holiday?->toArray(),
            'applicable' => array_map(static fn(HolidayStateDTO $state) => $state->toArray(), $this->applicable),
        ];
    }
}
