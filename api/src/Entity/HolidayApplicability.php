<?php

namespace App\Entity;

use App\Mapping\EntityBase;
use App\Repository\HolidayApplicabilityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: HolidayApplicabilityRepository::class)]
#[ORM\HasLifecycleCallbacks]
class HolidayApplicability extends EntityBase
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'holidayApplicabilities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Holiday $holiday = null;

    #[ORM\ManyToOne(inversedBy: 'holidayApplicabilities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?State $state = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getHoliday(): ?Holiday
    {
        return $this->holiday;
    }

    public function setHoliday(?Holiday $holiday): static
    {
        $this->holiday = $holiday;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): static
    {
        $this->state = $state;

        return $this;
    }
}
