<?php

namespace App\DTO\Holiday;

use App\Entity\Holiday;
use App\Enum\FederalState;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

class HolidayResponse implements \JsonSerializable
{
    #[Groups(['read:admin'])]
    public Uuid $id;

    #[Groups(['read:admin', 'read:public'])]
    public string $date;

    #[Groups(['read:admin'])]
    public int $day;

    #[Groups(['read:admin'])]
    public int $month;

    #[Groups(['read:admin'])]
    public ?int $year;

    #[Groups(['read:admin', 'read:public'])]
    public bool $isGeneral;

    #[Groups(['read:admin', 'read:public'])]
    public string $name;

    #[Groups(['read:admin', 'read:public'])]
    public array $appliesTo;

    /**
     * @param Holiday $holiday
     */
    public function __construct(Holiday $holiday)
    {
        $this->id = $holiday->getId();

        if ($holiday->getHolidayYear() !== null) {
            $date = \DateTime::createFromFormat('Y-n-j', $holiday->getHolidayYear() . '-' . $holiday->getHolidayMonth() . '-' . $holiday->getHolidayDay());
            $this->date = $date->format('Y-m-d');
        } else {
            $date = \DateTime::createFromFormat('Y-n-j', '0000-' . $holiday->getHolidayMonth() . '-' . $holiday->getHolidayDay());
            $this->date = $date->format('*-m-d');
        }

        $this->day = $holiday->getHolidayDay();
        $this->month = $holiday->getHolidayMonth();
        $this->year = $holiday->getHolidayYear();
        $this->isGeneral = $holiday->getIsGeneral();
        $this->name = $holiday->getHolidayName();

        foreach (FederalState::cases() as $federalState) {
            $method = 'is' . ucfirst((strtolower($federalState->value)));
            if ($holiday->$method()) {
                $this->appliesTo[] = strtolower($federalState->value);
            }
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'day' => $this->day,
            'month' => $this->month,
            'year' => $this->year,
            'isGeneral' => $this->isGeneral,
            'name' => $this->name,
            'appliesTo' => $this->appliesTo,
        ];
    }

}
