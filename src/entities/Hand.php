<?php

namespace Entities;

class Hand extends AbstractEntity {
    protected int $id = 0;
    protected int $id_round;
    protected String $player = '';
    protected array $cards = array();


    public function populateObjectFromDb(array $dbRow, array $excludedParticularFields = array()) :void {
        $excludedParticularFields[] = 'cards';
        parent::populateObjectFromDb($dbRow,$excludedParticularFields);
        $this->cards = json_decode($dbRow['cards'],true);
    }

    /**
     *
     * @return number
     */
    public function getId() {
        return $this->id;
    }

    /**
     *
     * @param number $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     *
     * @return mixed
     */
    public function getId_round() {
        return $this->id_round;
    }

    /**
     *
     * @param mixed $id_manche
     */
    public function setId_round($id_manche) {
        $this->id_round = $id_manche;
    }

    /**
     *
     * @return string
     */
    public function getPlayer() {
        return $this->player;
    }

    /**
     *
     * @param string $joueur
     */
    public function setPlayer($joueur) {
        $this->player = $joueur;
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
}