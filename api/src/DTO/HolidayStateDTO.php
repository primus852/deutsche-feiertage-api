<?php

namespace App\DTO;

readonly class HolidayStateDTO
{
    public function __construct(
        public string $symbol,
        public string $name,
    ) {
    }

    public function toArray(): array
    {
        return [
            'symbol' => $this->symbol,
            'name' => $this->name,
        ];
    }
}
