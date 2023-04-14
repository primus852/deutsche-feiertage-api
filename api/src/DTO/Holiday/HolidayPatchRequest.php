<?php

namespace App\DTO\Holiday;

use ApiPlatform\Metadata\ApiProperty;
use App\Enum\FederalState;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

class HolidayPatchRequest
{

    #[Assert\Positive]
    #[Groups(['write:admin'])]
    public ?int $day = null;

    #[Assert\Positive]
    #[Groups(['write:admin'])]
    public ?int $month = null;

    #[Groups(['write:admin'])]
    public ?int $year = null;

    #[Groups(['write:admin'])]
    public ?bool $isGeneral = false;

    #[Groups(['write:admin'])]
    public ?string $name = null;

    #[Groups(['write:admin'])]
    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'enum' => ['BW', 'BAY', 'BE', 'BB', 'HB', 'HH', 'HE', 'MV', 'NI', 'NW', 'RP', 'SL', 'SN', 'ST', 'SH', 'TH'],
            'example' => 'BE'
        ]
    )]
    /* @var FederalState[] $appliesTo */
    public ?array $appliesTo = null;


}
