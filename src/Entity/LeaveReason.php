<?php

namespace App\Entity;

use App\Repository\LeaveReasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LeaveReasonRepository::class)
 */
class LeaveReason
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sortie;

    /**
     * @ORM\Column(type="string", length=255 ,nullable=true)
     */
    private $lib;

    /**
     * @ORM\OneToMany(targetEntity=Disc::class, mappedBy="leaveReason")
     */
    private $disc;

    public function __construct()
    {
        $this->disc = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSortie(): ?string
    {
        return $this->sortie;
    }

    public function setSortie(string $sortie): self
    {
        $this->sortie = $sortie;

        return $this;
    }

    public function getLib(): ?string
    {
        return $this->lib;
    }

    public function setLib(string $lib): self
    {
        $this->lib = $lib;

        return $this;
    }

    /**
     * @return Collection|Disc[]
     */
    public function getDisc(): Collection
    {
        return $this->disc;
    }

    public function addDisc(Disc $disc): self
    {
        if (!$this->disc->contains($disc)) {
            $this->disc[] = $disc;
            $disc->setLeaveReason($this);
        }

        return $this;
    }

    public function removeDisc(Disc $disc): self
    {
        if ($this->disc->removeElement($disc)) {
            // set the owning side to null (unless already changed)
            if ($disc->getLeaveReason() === $this) {
                $disc->setLeaveReason(null);
            }
        }

        return $this;
    }
}
