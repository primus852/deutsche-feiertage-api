<?php

namespace App\DTO;

readonly class HolidayDTO
{
    /**
     * @param string $name
     * @param string|null $description
     */
    public function __construct(
        public string  $name,
        public ?string $description,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
