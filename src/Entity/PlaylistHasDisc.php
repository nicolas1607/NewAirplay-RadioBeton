<?php

namespace App\Entity;

use App\Repository\PlaylistHasDiscRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlaylistHasDiscRepository::class)
 */
class PlaylistHasDisc
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Disc::class, inversedBy="playlistHasDiscs")
     */
    private $disc;

    /**
     * @ORM\ManyToOne(targetEntity=Playlist::class, inversedBy="playlistHasDiscs")
     */
    private $playlist;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDisc(): ?Disc
    {
        return $this->disc;
    }

    public function setDisc(?Disc $disc): self
    {
        $this->disc = $disc;

        return $this;
    }

    public function getPlaylist(): ?Playlist
    {
        return $this->playlist;
    }

    public function setPlaylist(?Playlist $playlist): self
    {
        $this->playlist = $playlist;

        return $this;
    }
}
