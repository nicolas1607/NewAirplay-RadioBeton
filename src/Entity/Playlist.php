<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlaylistRepository::class)
 */
class Playlist
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $entry_date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $animator;

    /**
     * @ORM\Column(type="string", length=255 , nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Disc::class, mappedBy="playlist")
     */
    private $discs;

    public function __construct()
    {
        $this->discs = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntryDate(): ?\DateTimeInterface
    {
        return $this->entry_date;
    }

    public function setEntryDate(?\DateTimeInterface $entry_date): self
    {
        $this->entry_date = $entry_date;

        return $this;
    }

    public function getAnimator(): ?string
    {
        return $this->animator;
    }

    public function setAnimator(string $animator): self
    {
        $this->animator = $animator;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Disc[]
     */
    public function getDiscs(): Collection
    {
        return $this->discs;
    }

    public function addDisc(Disc $disc): self
    {
        if (!$this->discs->contains($disc)) {
            $this->discs[] = $disc;
            $disc->addPlaylist($this);
        }

        return $this;
    }

    public function removeDisc(Disc $disc): self
    {
        if ($this->discs->removeElement($disc)) {
            $disc->removePlaylist($this);
        }

        return $this;
    }

    

    
}
