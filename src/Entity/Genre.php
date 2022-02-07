<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GenreRepository::class)
 */
class Genre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $genre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lib;

    /**
     * @ORM\OneToMany(targetEntity=Disc::class, mappedBy="genre")
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

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

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
            $disc->setGenre($this);
        }

        return $this;
    }

    public function removeDisc(Disc $disc): self
    {
        if ($this->disc->removeElement($disc)) {
            // set the owning side to null (unless already changed)
            if ($disc->getGenre() === $this) {
                $disc->setGenre(null);
            }
        }

        return $this;
    }
}
