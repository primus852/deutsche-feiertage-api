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
    public ?int $day = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['write:admin'])]
    public ?int $month = null;

    #[Groups(['write:admin'])]
    public ?int $year = null;

    #[Assert\NotNull]
    #[Groups(['write:admin'])]
    public ?bool $isGeneral = false;

    #[Assert\NotBlank]
    #[Groups(['write:admin'])]
    public ?string $name = null;

    #[Groups(['write:admin'])]
    #[ApiProperty(
        openapiContext: [
            'type' => FederalState::class,
            'enum' => ['BW', 'BAY', 'BE', 'BB', 'HB', 'HH', 'HE', 'MV', 'NI', 'NW', 'RP', 'SL', 'SN', 'ST', 'SH', 'TH'],
            'example' => 'BE'
        ]
    )]
    /* @var FederalState[] $appliesTo */
    public ?array $appliesTo = null;


}
