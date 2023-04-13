<?php

namespace App\DTO\Holiday;

use ApiPlatform\Metadata\ApiProperty;
use App\Enum\FederalState;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

class HolidayAddRequest
{

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['write:admin'])]
    public int $day;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['write:admin'])]
    public int $month;

    #[Groups(['write:admin'])]
    public int $year;

    #[Assert\NotNull]
    #[Groups(['write:admin'])]
    public bool $isGeneral;

    #[Assert\NotBlank]
    #[Groups(['write:admin'])]
    public string $name;

    #[Groups(['write:admin'])]
    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'enum' => ['BW', 'BAY', 'BE', 'BB', 'HB', 'HH', 'HE', 'MV', 'NI', 'NW', 'RP', 'SL', 'SN', 'ST', 'SH', 'TH'],
            'example' => 'BE'
        ]
    )]
    /* @var FederalState[] $appliesTo */
    public array $appliesTo;


}
