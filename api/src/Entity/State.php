<?php

namespace App\Entity;

use App\Mapping\EntityBase;
use App\Repository\StateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: StateRepository::class)]
#[ORM\HasLifecycleCallbacks]
class State extends EntityBase
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 3)]
    private ?string $symbol = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    /**
     * @var Collection<int, HolidayApplicability>
     */
    #[ORM\OneToMany(targetEntity: HolidayApplicability::class, mappedBy: 'state', orphanRemoval: true)]
    private Collection $holidayApplicabilities;

    public function __construct()
    {
        $this->holidayApplicabilities = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): static
    {
        $this->symbol = $symbol;

        return $this;
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
            $holidayApplicability->setState($this);
        }

        return $this;
    }

    public function removeHolidayApplicability(HolidayApplicability $holidayApplicability): static
    {
        if ($this->holidayApplicabilities->removeElement($holidayApplicability)) {
            // set the owning side to null (unless already changed)
            if ($holidayApplicability->getState() === $this) {
                $holidayApplicability->setState(null);
            }
        }

        return $this;
    }
}
