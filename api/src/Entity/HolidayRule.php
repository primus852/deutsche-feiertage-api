<?php

namespace App\Entity;

use App\Mapping\EntityBase;
use App\Repository\HolidayRuleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: HolidayRuleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class HolidayRule extends EntityBase
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $month = null;

    #[ORM\Column(nullable: true)]
    private ?int $day = null;

    #[ORM\Column(nullable: true)]
    private ?int $year = null;

    #[ORM\ManyToOne(inversedBy: 'holidayRules')]
    private ?RuleType $ruleType = null;

    #[ORM\Column(nullable: true)]
    private ?int $offsetDays = null;

    #[ORM\ManyToOne(inversedBy: 'holidayRules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Holiday $holiday = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(?int $month): static
    {
        $this->month = $month;

        return $this;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(?int $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getRuleType(): ?RuleType
    {
        return $this->ruleType;
    }

    public function setRuleType(?RuleType $ruleType): static
    {
        $this->ruleType = $ruleType;

        return $this;
    }

    public function getOffsetDays(): ?int
    {
        return $this->offsetDays;
    }

    public function setOffsetDays(?int $offsetDays): static
    {
        $this->offsetDays = $offsetDays;

        return $this;
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
}
