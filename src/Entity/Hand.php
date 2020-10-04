<?php

namespace App\Entity;

use App\Repository\HandRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HandRepository::class)
 */
class Hand
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $player;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $cards;

    /**
     * @ORM\ManyToOne(targetEntity=Round::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $round;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?string
    {
        return $this->player;
    }

    public function setPlayer(string $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getCards(): ?string
    {
        return $this->cards;
    }

    public function setCards(?string $cards): self
    {
        $this->cards = $cards;

        return $this;
    }

    public function getRound(): ?Round
    {
        return $this->round;
    }

    public function setRound(?Round $round): self
    {
        $this->round = $round;

        return $this;
    }

}
