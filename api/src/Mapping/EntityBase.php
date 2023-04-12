<?php

namespace App\Mapping;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

/**
 * @ORM\HasLifecycleCallbacks
 */
class EntityBase implements EntityBaseInterface
{
    #[ORM\Column(nullable: false)]
    protected DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: false)]
    protected DateTimeImmutable $updatedAt;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updatedTimestamps(): void
    {
        $DateTimeImmutableNow = new DateTimeImmutable('now');

        $this->setUpdatedAt($DateTimeImmutableNow);

        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($DateTimeImmutableNow);
        }
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt ?? null;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
