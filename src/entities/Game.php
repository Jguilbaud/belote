<?php

namespace Entities;

class Game extends AbstractEntity {
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

    public function populateObjectFromDb(array $dbRow, array $excludedParticularFields = array()) :void {
        $excludedParticularFields[] = 'cards';
        parent::populateObjectFromDb($dbRow,$excludedParticularFields);
        $this->cards = json_decode($dbRow['cards'],true);
    }


    /**
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     *
     * @param int $id
     */
    public function setId($id) {
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
    public function setHash($hash) {
        $this->hash = $hash;
    }

    /**
     *
     * @return int
     */
    public function getDate() {
        return $this->date;
    }

    /**
     *
     * @param int $date
     */
    public function setDate($date) {
        $this->date = $date;
    }

    /**
     *
     * @return string
     */
    public function getName_north() {
        return $this->name_north;
    }

    /**
     *
     * @param string $nom_nord
     */
    public function setName_north($nom_nord) {
        $this->name_north = $nom_nord;
    }

    /**
     *
     * @return string
     */
    public function getName_south() {
        return $this->name_south;
    }

    /**
     *
     * @param string $nom_sud
     */
    public function setName_south($nom_sud) {
        $this->name_south = $nom_sud;
    }

    /**
     *
     * @return string
     */
    public function getName_west() {
        return $this->name_west;
    }

    /**
     *
     * @param string $nom_ouest
     */
    public function setName_west($nom_ouest) {
        $this->name_west = $nom_ouest;
    }

    /**
     *
     * @return string
     */
    public function getName_east() {
        return $this->name_east;
    }

    /**
     *
     * @param string $nom_est
     */
    public function setName_east($nom_est) {
        $this->name_east = $nom_est;
    }

    /**
     *
     * @return int
     */
    public function getTotal_points_ns() {
        return $this->total_points_ns;
    }

    /**
     *
     * @param int $total_points_ns
     */
    public function setTotal_points_ns($total_points_ns) {
        $this->total_points_ns = $total_points_ns;
    }

    /**
     *
     * @return int
     */
    public function getTotal_points_we() {
        return $this->total_points_we;
    }

    /**
     *
     * @param int $total_points_oe
     */
    public function setTotal_points_oe($total_points_oe) {
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
    public function setCards($cartes) {
        $this->cards = $cartes;
    }



    /**
     * @return number
     */
    public function getId_current_round() {
        return $this->id_current_round;
    }

    /**
     * @param number $id_manche
     */
    public function setId_round_courante($id_manche) {
        $this->id_current_round = $id_manche;
    }
}