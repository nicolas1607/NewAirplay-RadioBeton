<?php

namespace App\Entity;

use App\Repository\DiscRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DiscRepository::class)
 */
class Disc
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer" , nullable="true")
     */
    private $num_inventory;

    /**
     * @ORM\Column(type="string", length=255, nullable="true")
     */
    private $groupe;

    /**
     * @ORM\Column(type="string", length=255 , nullable="true")
     */
    private $album;

    /**
     * @ORM\Column(type="string", length=255, nullable="true")
     */
    private $label;

    /**
     * @ORM\Column(type="integer", nullable="true")
     */
    private $localisation;

    /**
     * @ORM\Column(type="datetime" , nullable="true")
     */
    private $arrival_date;

    /**
     * @ORM\Column(type="float", nullable="true")
     */
    private $cpt;

    /**
     * @ORM\Column(type="string", length=255 , nullable="true")
     */
    private $leave_name;

    /**
     * @ORM\Column(type="datetime", nullable="true")
     */
    private $leave_date;

    /**
     * @ORM\Column(type="boolean",nullable="true")
     */
    private $disk_sem;

    /**
     * @ORM\Column(type="boolean", nullable="true")
     */
    private $concert;

    /**
     * @ORM\Column(type="boolean", nullable="true")
     */
    private $aucard;

    /**
     * @ORM\Column(type="boolean", nullable="true")
     */
    private $ferarock;

    /**
     * @ORM\ManyToOne(targetEntity=Genre::class, inversedBy="disc")
     */
    private $genre;

    /**
     * @ORM\ManyToOne(targetEntity=Language::class, inversedBy="disc")
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity=LeaveReason::class, inversedBy="disc")
     */
    private $leaveReason;

    /**
     * @ORM\ManyToOne(targetEntity=Nationality::class, inversedBy="disc")
     */
    private $nationality;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="disc")
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity=Playlist::class, inversedBy="discs")
     */
    private $playlist;


    public function __construct()
    {
        $this->playlistHasDiscs = new ArrayCollection();
        $this->playlist = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumInventory(): ?int
    {
        return $this->num_inventory;
    }

    public function setNumInventory(int $num_inventory): self
    {
        $this->num_inventory = $num_inventory;

        return $this;
    }

    public function getGroupe(): ?string
    {
        return $this->groupe;
    }

    public function setGroupe(string $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function getAlbum(): ?string
    {
        return $this->album;
    }

    public function setAlbum(string $album): self
    {
        $this->album = $album;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLocalisation(): ?int
    {
        return $this->localisation;
    }

    public function setLocalisation(?int $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getArrivalDate(): ?\DateTimeInterface
    {
        return $this->arrival_date;
    }

    public function setArrivalDate(\DateTimeInterface $arrival_date): self
    {
        $this->arrival_date = $arrival_date;

        return $this;
    }

    public function getCpt(): ?float
    {
        return $this->cpt;
    }

    public function setCpt(?float $cpt): self
    {
        $this->cpt = $cpt;

        return $this;
    }

    public function getLeaveName(): ?string
    {
        return $this->leave_name;
    }

    public function setLeaveName(string $leave_name): self
    {
        $this->leave_name = $leave_name;

        return $this;
    }

    public function getLeaveDate(): ?\DateTimeInterface
    {
        return $this->leave_date;
    }

    public function setLeaveDate(?\DateTimeInterface $leave_date): self
    {
        $this->leave_date = $leave_date;

        return $this;
    }

    public function getDiskSem(): ?bool
    {
        return $this->disk_sem;
    }

    public function setDiskSem(?bool $disk_sem): self
    {
        $this->disk_sem = $disk_sem;

        return $this;
    }

    public function getConcert(): ?bool
    {
        return $this->concert;
    }

    public function setConcert(?bool $concert): self
    {
        $this->concert = $concert;

        return $this;
    }

    public function getAucard(): ?bool
    {
        return $this->aucard;
    }

    public function setAucard(?bool $aucard): self
    {
        $this->aucard = $aucard;

        return $this;
    }

    public function getFerarock(): ?bool
    {
        return $this->ferarock;
    }

    public function setFerarock(?bool $ferarock): self
    {
        $this->ferarock = $ferarock;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getLeaveReason(): ?LeaveReason
    {
        return $this->leaveReason;
    }

    public function setLeaveReason(?LeaveReason $leaveReason): self
    {
        $this->leaveReason = $leaveReason;

        return $this;
    }

    public function getNationality(): ?Nationality
    {
        return $this->nationality;
    }

    public function setNationality(?Nationality $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Playlist[]
     */
    public function getPlaylist(): Collection
    {
        return $this->playlist;
    }

    public function addPlaylist(Playlist $playlist): self
    {
        if (!$this->playlist->contains($playlist)) {
            $this->playlist[] = $playlist;
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): self
    {
        $this->playlist->removeElement($playlist);

        return $this;
    }
}
