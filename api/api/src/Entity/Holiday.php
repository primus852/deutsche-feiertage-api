<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\HolidayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: HolidayRepository::class)]
#[ApiResource]
class Holiday
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid')]
    private ?Uuid $id;

    #[ORM\Column(type: 'integer')]
    private ?int $holidayDay;

    #[ORM\Column(type: 'integer')]
    private ?int $holidayMonth;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $holidayYear;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $holidayName;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isBundesweit;

    #[ORM\OneToMany(mappedBy: 'holiday', targetEntity: HolidayComment::class, orphanRemoval: true)]
    private $holidayComments;

    #[ORM\Column(type: 'boolean')]
    private $isBw;

    #[ORM\Column(type: 'boolean')]
    private $isBay;

    #[ORM\Column(type: 'boolean')]
    private $isBe;

    #[ORM\Column(type: 'boolean')]
    private $isBb;

    #[ORM\Column(type: 'boolean')]
    private $isHb;

    #[ORM\Column(type: 'boolean')]
    private $isHh;

    #[ORM\Column(type: 'boolean')]
    private $isHe;

    #[ORM\Column(type: 'boolean')]
    private $isMv;

    #[ORM\Column(type: 'boolean')]
    private $isNi;

    #[ORM\Column(type: 'boolean')]
    private $isNw;

    #[ORM\Column(type: 'boolean')]
    private $isRp;

    #[ORM\Column(type: 'boolean')]
    private $isSl;

    #[ORM\Column(type: 'boolean')]
    private $isSn;

    #[ORM\Column(type: 'boolean')]
    private $isSt;

    #[ORM\Column(type: 'boolean')]
    private $isSh;

    #[ORM\Column(type: 'boolean')]
    private $isTh;

    public function __construct()
    {
        $this->holidayComments = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getHolidayDay(): ?int
    {
        return $this->holidayDay;
    }

    public function setHolidayDay(int $holidayDay): self
    {
        $this->holidayDay = $holidayDay;

        return $this;
    }

    public function getHolidayMonth(): ?int
    {
        return $this->holidayMonth;
    }

    public function setHolidayMonth(int $holidayMonth): self
    {
        $this->holidayMonth = $holidayMonth;

        return $this;
    }

    public function getHolidayYear(): ?int
    {
        return $this->holidayYear;
    }

    public function setHolidayYear(?int $holidayYear): self
    {
        $this->holidayYear = $holidayYear;

        return $this;
    }

    public function getHolidayName(): ?string
    {
        return $this->holidayName;
    }

    public function setHolidayName(string $holidayName): self
    {
        $this->holidayName = $holidayName;

        return $this;
    }

    public function getIsBundesweit(): ?bool
    {
        return $this->isBundesweit;
    }

    public function setIsBundesweit(bool $isBundesweit): self
    {
        $this->isBundesweit = $isBundesweit;

        return $this;
    }

    /**
     * @return Collection<int, HolidayComment>
     */
    public function getHolidayComments(): Collection
    {
        return $this->holidayComments;
    }

    public function addHolidayComment(HolidayComment $holidayComment): self
    {
        if (!$this->holidayComments->contains($holidayComment)) {
            $this->holidayComments[] = $holidayComment;
            $holidayComment->setHoliday($this);
        }

        return $this;
    }

    public function removeHolidayComment(HolidayComment $holidayComment): self
    {
        if ($this->holidayComments->removeElement($holidayComment)) {
            // set the owning side to null (unless already changed)
            if ($holidayComment->getHoliday() === $this) {
                $holidayComment->setHoliday(null);
            }
        }

        return $this;
    }

    public function getIsBw(): ?bool
    {
        return $this->isBw;
    }

    public function setIsBw(bool $isBw): self
    {
        $this->isBw = $isBw;

        return $this;
    }

    public function getIsBay(): ?bool
    {
        return $this->isBay;
    }

    public function setIsBay(bool $isBay): self
    {
        $this->isBay = $isBay;

        return $this;
    }

    public function getIsBe(): ?bool
    {
        return $this->isBe;
    }

    public function setIsBe(bool $isBe): self
    {
        $this->isBe = $isBe;

        return $this;
    }

    public function getIsBb(): ?bool
    {
        return $this->isBb;
    }

    public function setIsBb(bool $isBb): self
    {
        $this->isBb = $isBb;

        return $this;
    }

    public function getIsHb(): ?bool
    {
        return $this->isHb;
    }

    public function setIsHb(bool $isHb): self
    {
        $this->isHb = $isHb;

        return $this;
    }

    public function getIsHh(): ?bool
    {
        return $this->isHh;
    }

    public function setIsHh(bool $isHh): self
    {
        $this->isHh = $isHh;

        return $this;
    }

    public function getIsHe(): ?bool
    {
        return $this->isHe;
    }

    public function setIsHe(bool $isHe): self
    {
        $this->isHe = $isHe;

        return $this;
    }

    public function getIsMv(): ?bool
    {
        return $this->isMv;
    }

    public function setIsMv(bool $isMv): self
    {
        $this->isMv = $isMv;

        return $this;
    }

    public function getIsNi(): ?bool
    {
        return $this->isNi;
    }

    public function setIsNi(bool $isNi): self
    {
        $this->isNi = $isNi;

        return $this;
    }

    public function getIsNw(): ?bool
    {
        return $this->isNw;
    }

    public function setIsNw(bool $isNw): self
    {
        $this->isNw = $isNw;

        return $this;
    }

    public function getIsRp(): ?bool
    {
        return $this->isRp;
    }

    public function setIsRp(bool $isRp): self
    {
        $this->isRp = $isRp;

        return $this;
    }

    public function getIsSl(): ?bool
    {
        return $this->isSl;
    }

    public function setIsSl(bool $isSl): self
    {
        $this->isSl = $isSl;

        return $this;
    }

    public function getIsSn(): ?bool
    {
        return $this->isSn;
    }

    public function setIsSn(bool $isSn): self
    {
        $this->isSn = $isSn;

        return $this;
    }

    public function getIsSt(): ?bool
    {
        return $this->isSt;
    }

    public function setIsSt(bool $isSt): self
    {
        $this->isSt = $isSt;

        return $this;
    }

    public function getIsSh(): ?bool
    {
        return $this->isSh;
    }

    public function setIsSh(bool $isSh): self
    {
        $this->isSh = $isSh;

        return $this;
    }

    public function getIsTh(): ?bool
    {
        return $this->isTh;
    }

    public function setIsTh(bool $isTh): self
    {
        $this->isTh = $isTh;

        return $this;
    }
}
