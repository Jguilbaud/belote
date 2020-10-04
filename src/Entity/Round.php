<?php

namespace App\Entity;

use App\Repository\RoundRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoundRepository::class)
 */
class Round
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $game_id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $round_number;

    /**
     * @ORM\Column(type="integer")
     */
    private $points_ns;

    /**
     * @ORM\Column(type="integer")
     */
    private $points_we;

    /**
     * @ORM\Column(type="string", length=7, nullable=true)
     */
    private $trump_color;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $dealer;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $taker;

    /**
     * @ORM\Column(type="smallint")
     */
    private $current_turn_id;

    /**
     * @ORM\OneToMany(targetEntity=Turn::class, mappedBy="round", orphanRemoval=true)
     */
    private $turns;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class)
     */
    private $game;

    public function __construct()
    {
        $this->turns = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameId(): ?int
    {
        return $this->game_id;
    }

    public function setGameId(int $game_id): self
    {
        $this->game_id = $game_id;

        return $this;
    }

    public function getRoundNumber(): ?int
    {
        return $this->round_number;
    }

    public function setRoundNumber(int $round_number): self
    {
        $this->round_number = $round_number;

        return $this;
    }

    public function getPointNs(): ?int
    {
        return $this->point_ns;
    }

    public function setPointNs(int $point_ns): self
    {
        $this->point_ns = $point_ns;

        return $this;
    }

    public function getPointsWe(): ?int
    {
        return $this->points_we;
    }

    public function setPointsWe(int $points_we): self
    {
        $this->points_we = $points_we;

        return $this;
    }

    public function getTrumpColor(): ?string
    {
        return $this->trump_color;
    }

    public function setTrumpColor(?string $trump_color): self
    {
        $this->trump_color = $trump_color;

        return $this;
    }

    public function getDealer(): ?string
    {
        return $this->dealer;
    }

    public function setDealer(string $dealer): self
    {
        $this->dealer = $dealer;

        return $this;
    }

    public function getTaker(): ?string
    {
        return $this->taker;
    }

    public function setTaker(?string $taker): self
    {
        $this->taker = $taker;

        return $this;
    }

    public function getCurrentTurnId(): ?int
    {
        return $this->current_turn_id;
    }

    public function setCurrentTurnId(int $current_turn_id): self
    {
        $this->current_turn_id = $current_turn_id;

        return $this;
    }

    /**
     * @return Collection|Turn[]
     */
    public function getTurns(): Collection
    {
        return $this->turns;
    }

    public function addTurn(Turn $turn): self
    {
        if (!$this->turns->contains($turn)) {
            $this->turns[] = $turn;
            $turn->setRound($this);
        }

        return $this;
    }

    public function removeTurn(Turn $turn): self
    {
        if ($this->turns->contains($turn)) {
            $this->turns->removeElement($turn);
            // set the owning side to null (unless already changed)
            if ($turn->getRound() === $this) {
                $turn->setRound(null);
            }
        }

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }
}
