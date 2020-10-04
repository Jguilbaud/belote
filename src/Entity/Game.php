<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $hash;

    /**
     * @ORM\Column(type="integer")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name_north;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name_south;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name_west;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name_east;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_points_ns;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_points_we;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $cards_deck;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_current_round;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $step;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $current_player;

    
    public function __construct(){
        $this->date = time();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(int $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getNameNorth(): ?string
    {
        return $this->name_north;
    }

    public function setNameNorth(string $name_north): self
    {
        $this->name_north = $name_north;

        return $this;
    }

    public function getNameSouth(): ?string
    {
        return $this->name_south;
    }

    public function setNameSouth(string $name_south): self
    {
        $this->name_south = $name_south;

        return $this;
    }

    public function getNameWest(): ?string
    {
        return $this->name_west;
    }

    public function setNameWest(string $name_west): self
    {
        $this->name_west = $name_west;

        return $this;
    }

    public function getNameEast(): ?string
    {
        return $this->name_east;
    }

    public function setNameEast(string $name_east): self
    {
        $this->name_east = $name_east;

        return $this;
    }

    public function getTotalPointsNs(): ?int
    {
        return $this->total_points_ns;
    }

    public function setTotalPointsNs(int $total_points_ns): self
    {
        $this->total_points_ns = $total_points_ns;

        return $this;
    }

    public function getTotalPointsWe(): ?int
    {
        return $this->total_points_we;
    }

    public function setTotalPointsWe(int $total_points_we): self
    {
        $this->total_points_we = $total_points_we;

        return $this;
    }

    public function getCardsDeck(): ?string
    {
        return $this->cards_deck;
    }

    public function setCardsDeck(?string $cards_deck): self
    {
        $this->cards_deck = $cards_deck;

        return $this;
    }

    public function getIdCurrentRound(): ?int
    {
        return $this->id_current_round;
    }

    public function setIdCurrentRound(int $id_current_round): self
    {
        $this->id_current_round = $id_current_round;

        return $this;
    }

    public function getStep(): ?string
    {
        return $this->step;
    }

    public function setStep(string $step): self
    {
        $this->step = $step;

        return $this;
    }

    public function getCurrentPlayer(): ?string
    {
        return $this->current_player;
    }

    public function setCurrentPlayer(string $current_player): self
    {
        $this->current_player = $current_player;

        return $this;
    }
}
