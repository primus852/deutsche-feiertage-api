<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HolidayRepository")
 */
class Holiday
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $holidayDay;

    /**
     * @ORM\Column(type="integer")
     */
    private $holidayMonth;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $holidayYear;

    /**
     * @ORM\Column(type="boolean")
     */
    private $bundesweit;

    /**
     * @ORM\Column(type="boolean")
     */
    private $bw;

    /**
     * @ORM\Column(type="boolean")
     */
    private $bay;

    /**
     * @ORM\Column(type="boolean")
     */
    private $be;

    /**
     * @ORM\Column(type="boolean")
     */
    private $bb;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hb;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hh;

    /**
     * @ORM\Column(type="boolean")
     */
    private $he;

    /**
     * @ORM\Column(type="boolean")
     */
    private $mv;

    /**
     * @ORM\Column(type="boolean")
     */
    private $ni;

    /**
     * @ORM\Column(type="boolean")
     */
    private $nw;

    /**
     * @ORM\Column(type="boolean")
     */
    private $rp;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sl;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sn;

    /**
     * @ORM\Column(type="boolean")
     */
    private $st;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sh;

    /**
     * @ORM\Column(type="boolean")
     */
    private $th;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="holiday", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $holidayName;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getBundesweit(): ?bool
    {
        return $this->bundesweit;
    }

    public function setBundesweit(bool $bundesweit): self
    {
        $this->bundesweit = $bundesweit;

        return $this;
    }

    public function getBw(): ?bool
    {
        return $this->bw;
    }

    public function setBw(bool $bw): self
    {
        $this->bw = $bw;

        return $this;
    }

    public function getBay(): ?bool
    {
        return $this->bay;
    }

    public function setBay(bool $bay): self
    {
        $this->bay = $bay;

        return $this;
    }

    public function getBe(): ?bool
    {
        return $this->be;
    }

    public function setBe(bool $be): self
    {
        $this->be = $be;

        return $this;
    }

    public function getBb(): ?bool
    {
        return $this->bb;
    }

    public function setBb(bool $bb): self
    {
        $this->bb = $bb;

        return $this;
    }

    public function getHb(): ?bool
    {
        return $this->hb;
    }

    public function setHb(bool $hb): self
    {
        $this->hb = $hb;

        return $this;
    }

    public function getHh(): ?bool
    {
        return $this->hh;
    }

    public function setHh(bool $hh): self
    {
        $this->hh = $hh;

        return $this;
    }

    public function getHe(): ?bool
    {
        return $this->he;
    }

    public function setHe(bool $he): self
    {
        $this->he = $he;

        return $this;
    }

    public function getMv(): ?bool
    {
        return $this->mv;
    }

    public function setMv(bool $mv): self
    {
        $this->mv = $mv;

        return $this;
    }

    public function getNi(): ?bool
    {
        return $this->ni;
    }

    public function setNi(bool $ni): self
    {
        $this->ni = $ni;

        return $this;
    }

    public function getNw(): ?bool
    {
        return $this->nw;
    }

    public function setNw(bool $nw): self
    {
        $this->nw = $nw;

        return $this;
    }

    public function getRp(): ?bool
    {
        return $this->rp;
    }

    public function setRp(bool $rp): self
    {
        $this->rp = $rp;

        return $this;
    }

    public function getSl(): ?bool
    {
        return $this->sl;
    }

    public function setSl(bool $sl): self
    {
        $this->sl = $sl;

        return $this;
    }

    public function getSn(): ?bool
    {
        return $this->sn;
    }

    public function setSn(bool $sn): self
    {
        $this->sn = $sn;

        return $this;
    }

    public function getSt(): ?bool
    {
        return $this->st;
    }

    public function setSt(bool $st): self
    {
        $this->st = $st;

        return $this;
    }

    public function getSh(): ?bool
    {
        return $this->sh;
    }

    public function setSh(bool $sh): self
    {
        $this->sh = $sh;

        return $this;
    }

    public function getTh(): ?bool
    {
        return $this->th;
    }

    public function setTh(bool $th): self
    {
        $this->th = $th;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setHoliday($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getHoliday() === $this) {
                $comment->setHoliday(null);
            }
        }

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
