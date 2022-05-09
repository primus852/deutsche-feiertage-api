<?php

namespace App\Entity;

use App\Repository\HolidayCommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: HolidayCommentRepository::class)]
class HolidayComment
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid')]
    private ?Uuid $id;

    #[ORM\Column(type: 'text')]
    private ?string $comment;

    #[ORM\ManyToOne(targetEntity: Holiday::class, inversedBy: 'holidayComments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Holiday $holiday;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getHoliday(): ?Holiday
    {
        return $this->holiday;
    }

    public function setHoliday(?Holiday $holiday): self
    {
        $this->holiday = $holiday;

        return $this;
    }
}
