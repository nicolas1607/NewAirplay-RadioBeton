<?php

namespace App\Entity;

use App\Repository\NationalityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NationalityRepository::class)
 */
class Nationality
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255 , nullable=true)
     */
    private $nationality;

    /**
     * @ORM\Column(type="string", length=255 , nullable=true)
     */
    private $lib;

    /**
     * @ORM\OneToMany(targetEntity=Disc::class, mappedBy="nationality")
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

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(string $nationality): self
    {
        $this->nationality = $nationality;

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
            $disc->setNationality($this);
        }

        return $this;
    }

    public function removeDisc(Disc $disc): self
    {
        if ($this->disc->removeElement($disc)) {
            // set the owning side to null (unless already changed)
            if ($disc->getNationality() === $this) {
                $disc->setNationality(null);
            }
        }

        return $this;
    }
}
