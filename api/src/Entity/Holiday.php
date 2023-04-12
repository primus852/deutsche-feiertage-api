<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\DTO\Holiday\HolidayAddRequest;
use App\DTO\Holiday\HolidayAddResponse;
use App\Mapping\EntityBase;
use App\Repository\HolidayRepository;
use App\State\Holiday\HolidayProcessor;
use App\State\Holiday\HolidayProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: HolidayRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/add-holiday',
            openapiContext: [
                'tags' => ['Holiday [Admin]']
            ],
            normalizationContext: [
                'groups' => 'read:admin'
            ],
            denormalizationContext: [
                'groups' => 'write:admin'
            ],
            input: HolidayAddRequest::class,
            output: HolidayAddResponse::class
        ),
    ],
    formats: ["json", "jsonld"],
    provider: HolidayProvider::class,
    processor: HolidayProcessor::class
)]
#[ORM\HasLifecycleCallbacks]
class Holiday extends EntityBase
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['read:admin'])]
    private ?Uuid $id = null;

    #[ORM\Column]
    private ?int $holidayDay = null;

    #[ORM\Column]
    private ?int $holidayMonth = null;

    #[ORM\Column(nullable: true)]
    private ?int $holidayYear = null;

    #[ORM\Column]
    private ?bool $isGeneral = null;

    #[ORM\Column]
    private ?bool $bw = null;

    #[ORM\Column]
    private ?bool $bay = null;

    #[ORM\Column]
    private ?bool $be = null;

    #[ORM\Column]
    private ?bool $bb = null;

    #[ORM\Column]
    private ?bool $hb = null;

    #[ORM\Column]
    private ?bool $hh = null;

    #[ORM\Column]
    private ?bool $he = null;

    #[ORM\Column]
    private ?bool $mv = null;

    #[ORM\Column]
    private ?bool $ni = null;

    #[ORM\Column]
    private ?bool $nw = null;

    #[ORM\Column]
    private ?bool $rp = null;

    #[ORM\Column]
    private ?bool $sl = null;

    #[ORM\Column]
    private ?bool $sn = null;

    #[ORM\Column]
    private ?bool $st = null;

    #[ORM\Column]
    private ?bool $sh = null;

    #[ORM\Column]
    private ?bool $th = null;

    #[ORM\Column(length: 255)]
    private ?string $holidayName = null;

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

    public function getIsGeneral(): ?bool
    {
        return $this->isGeneral;
    }

    public function setIsGeneral(bool $isGeneral): self
    {
        $this->isGeneral = $isGeneral;

        return $this;
    }

    public function isBw(): ?bool
    {
        return $this->bw;
    }

    public function setBw(bool $bw): self
    {
        $this->bw = $bw;

        return $this;
    }

    public function isBay(): ?bool
    {
        return $this->bay;
    }

    public function setBay(bool $bay): self
    {
        $this->bay = $bay;

        return $this;
    }

    public function isBe(): ?bool
    {
        return $this->be;
    }

    public function setBe(bool $be): self
    {
        $this->be = $be;

        return $this;
    }

    public function isBb(): ?bool
    {
        return $this->bb;
    }

    public function setBb(bool $bb): self
    {
        $this->bb = $bb;

        return $this;
    }

    public function isHb(): ?bool
    {
        return $this->hb;
    }

    public function setHb(bool $hb): self
    {
        $this->hb = $hb;

        return $this;
    }

    public function isHh(): ?bool
    {
        return $this->hh;
    }

    public function setHh(bool $hh): self
    {
        $this->hh = $hh;

        return $this;
    }

    public function isHe(): ?bool
    {
        return $this->he;
    }

    public function setHe(bool $he): self
    {
        $this->he = $he;

        return $this;
    }

    public function isMv(): ?bool
    {
        return $this->mv;
    }

    public function setMv(bool $mv): self
    {
        $this->mv = $mv;

        return $this;
    }

    public function isNi(): ?bool
    {
        return $this->ni;
    }

    public function setNi(bool $ni): self
    {
        $this->ni = $ni;

        return $this;
    }

    public function isNw(): ?bool
    {
        return $this->nw;
    }

    public function setNw(bool $nw): self
    {
        $this->nw = $nw;

        return $this;
    }

    public function isRp(): ?bool
    {
        return $this->rp;
    }

    public function setRp(bool $rp): self
    {
        $this->rp = $rp;

        return $this;
    }

    public function isSl(): ?bool
    {
        return $this->sl;
    }

    public function setSl(bool $sl): self
    {
        $this->sl = $sl;

        return $this;
    }

    public function isSn(): ?bool
    {
        return $this->sn;
    }

    public function setSn(bool $sn): self
    {
        $this->sn = $sn;

        return $this;
    }

    public function isSt(): ?bool
    {
        return $this->st;
    }

    public function setSt(bool $st): self
    {
        $this->st = $st;

        return $this;
    }

    public function isSh(): ?bool
    {
        return $this->sh;
    }

    public function setSh(bool $sh): self
    {
        $this->sh = $sh;

        return $this;
    }

    public function isTh(): ?bool
    {
        return $this->th;
    }

    public function setTh(bool $th): self
    {
        $this->th = $th;

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
}
