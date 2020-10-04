<?php
namespace App\Entity;

use App\Repository\TurnRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass=TurnRepository::class)
 */
class Turn
{

    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="smallint")
     */
    private $turn_number;

    /**
     *
     * @ORM\Column(type="string", length=1)
     */
    private $first_player;

    /**
     *
     * @ORM\Column(type="string", length=3)
     */
    private $card_n;

    /**
     *
     * @ORM\Column(type="string", length=3)
     */
    private $card_e;

    /**
     *
     * @ORM\Column(type="string", length=3)
     */
    private $card_s;

    /**
     *
     * @ORM\Column(type="string", length=3)
     */
    private $card_w;

    /**
     *
     * @ORM\Column(type="string", length=1)
     */
    private $winner;

    /**
     *
     * @ORM\ManyToOne(targetEntity=Round::class, inversedBy="turns")
     * @ORM\JoinColumn(nullable=false)
     */
    private $round;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTurnNumber(): ?int
    {
        return $this->turn_number;
    }

    public function setTurnNumber(int $turn_number): self
    {
        $this->turn_number = $turn_number;

        return $this;
    }

    public function getFirstPlayer(): ?string
    {
        return $this->first_player;
    }

    public function setFirstPlayer(string $first_player): self
    {
        $this->first_player = $first_player;

        return $this;
    }

    public function getCardN(): ?string
    {
        return $this->card_n;
    }

    public function setCardN(string $card_n): self
    {
        $this->card_n = $card_n;

        return $this;
    }

    public function getCardE(): ?string
    {
        return $this->card_e;
    }

    public function setCardE(string $card_e): self
    {
        $this->card_e = $card_e;

        return $this;
    }

    public function getCardS(): ?string
    {
        return $this->card_s;
    }

    public function setCardS(string $card_s): self
    {
        $this->card_s = $card_s;

        return $this;
    }

    public function getCardW(): ?string
    {
        return $this->card_w;
    }

    public function setCardW(string $card_w): self
    {
        $this->card_w = $card_w;

        return $this;
    }

    public function getWinner(): ?string
    {
        return $this->winner;
    }

    public function setWinner(string $winner): self
    {
        $this->winner = $winner;

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
