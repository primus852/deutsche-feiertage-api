<?php

namespace App\Entity;

use App\Enum\HolidayRuleType;
use App\Mapping\EntityBase;
use App\Repository\RuleTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: RuleTypeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class RuleType extends EntityBase
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, HolidayRule>
     */
    #[ORM\OneToMany(targetEntity: HolidayRule::class, mappedBy: 'ruleType')]
    private Collection $holidayRules;

    #[ORM\Column(enumType: HolidayRuleType::class)]
    private ?HolidayRuleType $name = null;

    public function __construct()
    {
        $this->holidayRules = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
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
            $holidayRule->setRuleType($this);
        }

        return $this;
    }

    public function removeHolidayRule(HolidayRule $holidayRule): static
    {
        if ($this->holidayRules->removeElement($holidayRule)) {
            // set the owning side to null (unless already changed)
            if ($holidayRule->getRuleType() === $this) {
                $holidayRule->setRuleType(null);
            }
        }

        return $this;
    }

    public function getName(): ?HolidayRuleType
    {
        return $this->name;
    }

    public function setName(HolidayRuleType $name): static
    {
        $this->name = $name;

        return $this;
    }
}
