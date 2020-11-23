<?php
namespace App\Entity;

use App\Repository\GameRepository;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\PlayerPosition;

/**
 *
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
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
     * @ORM\Column(type="string", length=10)
     */
    private $hash;

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $date;

    /**
     *
     * @ORM\Column(type="string", length=30)
     */
    private $name_north;

    /**
     *
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $name_south;

    /**
     *
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $name_west;

    /**
     *
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $name_east;

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $total_points_ns = 0;

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $total_points_we = 0;

    /**
     *
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $cards_deck = array(
        'sa',
        's10',
        'sk',
        'sq',
        'sj',
        's9',
        's8',
        's7',
        'ha',
        'h10',
        'hk',
        'hq',
        'hj',
        'h9',
        'h8',
        'h7',
        'ca',
        'c10',
        'ck',
        'cq',
        'cj',
        'c9',
        'c8',
        'c7',
        'da',
        'd10',
        'dk',
        'dq',
        'dj',
        'd9',
        'd8',
        'd7'
    );

    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id_current_round;

    /**
     *
     * @ORM\Column(type="string", length=30)
     */
    private $step = self::STEP_JOIN;

    const STEP_JOIN = 'join';

    const STEP_CUT_DECK = 'cutdeck';

    /**
     * Etape de choix de l'atout de la carte proposée
     * @var string
     */
    const STEP_CHOOSE_TRUMP = 'choosetrump';

    /**
     * Etape de choix libre de l'atout
     * @var string
     */
    const STEP_CHOOSE_TRUMP_2 = 'choosetrump2';

    const STEP_PLAY_CARD = 'playcard';

    const STEP_FINISHED = 'finished';


    /**
     *
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $current_player;

    public function __construct()
    {
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

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('current_player', new PlayerPosition());
    }
}
