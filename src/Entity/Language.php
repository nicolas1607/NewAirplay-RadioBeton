<?php

namespace App\Entity;

use App\Repository\LanguageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LanguageRepository::class)
 */
class Language
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
    private $language;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lib;

    /**
     * @ORM\OneToMany(targetEntity=Disc::class, mappedBy="language")
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

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

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
            $disc->setLanguage($this);
        }

        return $this;
    }

    public function removeDisc(Disc $disc): self
    {
        if ($this->disc->removeElement($disc)) {
            // set the owning side to null (unless already changed)
            if ($disc->getLanguage() === $this) {
                $disc->setLanguage(null);
            }
        }

        return $this;
    }
}
