<?php

namespace App\Entity;

use App\Mapping\EntityBase;
use App\Repository\HolidayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: HolidayRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Holiday extends EntityBase
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, HolidayApplicability>
     */
    #[ORM\OneToMany(targetEntity: HolidayApplicability::class, mappedBy: 'holiday', orphanRemoval: true)]
    private Collection $holidayApplicabilities;

    /**
     * @var Collection<int, HolidayRule>
     */
    #[ORM\OneToMany(targetEntity: HolidayRule::class, mappedBy: 'holiday', orphanRemoval: true)]
    private Collection $holidayRules;

    public function __construct()
    {
        $this->holidayApplicabilities = new ArrayCollection();
        $this->holidayRules = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, HolidayApplicability>
     */
    public function getHolidayApplicabilities(): Collection
    {
        return $this->holidayApplicabilities;
    }

    public function addHolidayApplicability(HolidayApplicability $holidayApplicability): static
    {
        if (!$this->holidayApplicabilities->contains($holidayApplicability)) {
            $this->holidayApplicabilities->add($holidayApplicability);
            $holidayApplicability->setHoliday($this);
        }

        return $this;
    }

    public function removeHolidayApplicability(HolidayApplicability $holidayApplicability): static
    {
        if ($this->holidayApplicabilities->removeElement($holidayApplicability)) {
            // set the owning side to null (unless already changed)
            if ($holidayApplicability->getHoliday() === $this) {
                $holidayApplicability->setHoliday(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HolidayRule>
     */
    public function getHolidayRules(): Collection
    {
        return $this->holidayRules;
    }

    public function addHolidayRule(HolidayRule $holidayRule): static
    {
        if (!$this->holidayRules->contains($holidayRule)) {
            $this->holidayRules->add($holidayRule);
            $holidayRule->setHoliday($this);
        }

        return $this;
    }

    public function removeHolidayRule(HolidayRule $holidayRule): static
    {
        if ($this->holidayRules->removeElement($holidayRule)) {
            // set the owning side to null (unless already changed)
            if ($holidayRule->getHoliday() === $this) {
                $holidayRule->setHoliday(null);
            }
        }

        return $this;
    }
}
