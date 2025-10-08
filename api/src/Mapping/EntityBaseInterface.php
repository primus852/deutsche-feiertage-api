<?php

namespace App\Mapping;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

/**
 * EntityBase Interface
 */
interface EntityBaseInterface
{
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updatedTimestamps(): void;

    /**
     * Get createdAt
     *
     * @return null|DateTimeImmutable
     */
    public function getCreatedAt(): ?DateTimeImmutable;

    /**
     * Set createdAt
     *
     * @param DateTimeImmutable $createdAt
     */
    public function setCreatedAt(DateTimeImmutable $createdAt);

    /**
     * Get updatedAt
     *
     * @return DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?DateTimeImmutable;

    /**
     * Set updatedAt
     *
     * @param DateTimeImmutable $updatedAt
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt);
}
