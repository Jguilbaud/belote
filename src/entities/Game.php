<?php

namespace Entities;

class Game extends AbstractEntity {

    const STEP_JOIN = 'join';

    const STEP_CUT_DECK = 'cut';

    const STEP_CHOOSE_TRUMP = 'choosetrump';

    const STEP_PLAY_CARD = 'play';

    const STEP_FINISHED = 'finished';

    protected int $id = 0;
    protected String $hash = '';
    protected int $date = 0;
    protected ?String $name_north = '';
    protected ?String $name_south = '';
    protected ?String $name_west = '';
    protected ?String $name_east = '';
    protected int $total_points_ns = 0;
    protected int $total_points_we = 0;
    protected array $cards = array();
    protected int $id_current_round = 0;
    protected String $step = self::STEP_JOIN;
    protected String $current_player = null;

    public function populateObjectFromDb(array $dbRow, array $excludedParticularFields = array()): void {
        $excludedParticularFields[] = 'cards';
        parent::populateObjectFromDb($dbRow, $excludedParticularFields);
        $this->cards = json_decode($dbRow['cards'], true);
    }

    /**
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     *
     * @param int $id
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     *
     * @return string
     */
    public function getHash() {
        return $this->hash;
    }

    /**
     *
     * @param string $hash
     */
    public function setHash(String $hash): void {
        $this->hash = $hash;
    }

    /**
     *
     * @return int
     */
    public function getDate(): int {
        return $this->date;
    }

    /**
     *
     * @param int $date
     */
    public function setDate(int $date): void {
        $this->date = $date;
    }

    /**
     *
     * @return string
     */
    public function getName_north(): String {
        return $this->name_north;
    }

    /**
     *
     * @param string $nom_nord
     */
    public function setName_north(String $nom_nord): void {
        $this->name_north = $nom_nord;
    }

    /**
     *
     * @return string
     */
    public function getName_south(): String {
        return $this->name_south;
    }

    /**
     *
     * @param string $nom_sud
     */
    public function setName_south(String $nom_sud): void {
        $this->name_south = $nom_sud;
    }

    /**
     *
     * @return string
     */
    public function getName_west(): String {
        return $this->name_west;
    }

    /**
     *
     * @param string $nom_ouest
     */
    public function setName_west(String $nom_ouest): void {
        $this->name_west = $nom_ouest;
    }

    /**
     *
     * @return string
     */
    public function getName_east(): String {
        return $this->name_east;
    }

    /**
     *
     * @param string $nom_est
     */
    public function setName_east(String $nom_est): void {
        $this->name_east = $nom_est;
    }

    /**
     *
     * @return int
     */
    public function getTotal_points_ns(): int {
        return $this->total_points_ns;
    }

    /**
     *
     * @param int $total_points_ns
     */
    public function setTotal_points_ns(int $total_points_ns): void {
        $this->total_points_ns = $total_points_ns;
    }

    /**
     *
     * @return int
     */
    public function getTotal_points_we(): int {
        return $this->total_points_we;
    }

    /**
     *
     * @param int $total_points_oe
     */
    public function setTotal_points_we(int $total_points_oe): void {
        $this->total_points_we = $total_points_oe;
    }

    /**
     *
     * @return multitype:
     */
    public function getCards() {
        return $this->cards;
    }

    /**
     *
     * @param multitype: $cartes
     */
    public function setCards($cartes): void {
        $this->cards = $cartes;
    }

    /**
     *
     * @return number
     */
    public function getId_current_round(): int {
        return $this->id_current_round;
    }

    /**
     *
     * @param int $id_current_round
     */
    public function setId_current_round(int $id_current_round): void {
        $this->id_current_round = $id_current_round;
    }

    /**
     *
     * @return string
     */
    public function getStep(): String {
        return $this->step;
    }

    /**
     *
     * @param string $step
     */
    public function setStep(String $step): void {
        $this->step = $step;
    }


    /**
     * @return mixed
     */
    public function getCurrent_player() {
        return $this->current_player;
    }

    /**
     * @param mixed $currentPlayer
     */
    public function setCurrent_player($currentPlayer) {
        $this->current_player = $currentPlayer;
    }

}