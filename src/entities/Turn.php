<?php

namespace Entities;

class Turn extends AbstractEntity {
    protected int $id = 0;
    protected int $id_round = 0;
    protected int $num_turn = 0;
    protected String $first_player = '';
    protected String $card_n = '';
    protected String $card_e = '';
    protected String $card_s = '';
    protected String $card_w = '';
    protected String $winner = '';
    /**
     * @return string
     */
    public function getFirst_player() {
        return $this->first_player;
    }

    /**
     * @param string $premier_joueur
     */
    public function setFirst_player($premier_joueur) {
        $this->first_player = $premier_joueur;
    }

    /**
     * @return number
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param number $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return number
     */
    public function getId_round() {
        return $this->id_round;
    }

    /**
     * @param number $id_manche
     */
    public function setId_round($id_manche) {
        $this->id_round = $id_manche;
    }

    /**
     * @return number
     */
    public function getNum_turn() {
        return $this->num_turn;
    }

    /**
     * @param number $num_tour
     */
    public function setNum_turn($num_tour) {
        $this->num_turn = $num_tour;
    }

    /**
     * @return string
     */
    public function getCard_n() {
        return $this->card_n;
    }

    /**
     * @param string $card_n
     */
    public function setCard_n($card_n) {
        $this->card_n = $card_n;
    }

    /**
     * @return string
     */
    public function getCard_e() {
        return $this->card_e;
    }

    /**
     * @param string $card_e
     */
    public function setCard_e($card_e) {
        $this->card_e = $card_e;
    }

    /**
     * @return string
     */
    public function getCard_s() {
        return $this->card_s;
    }

    /**
     * @param string $card_s
     */
    public function setCard_s($card_s) {
        $this->card_s = $card_s;
    }

    /**
     * @return string
     */
    public function getCard_w() {
        return $this->card_w;
    }

    /**
     * @param string $card_w
     */
    public function setCard_w($card_w) {
        $this->card_w = $card_w;
    }

    /**
     * @return string
     */
    public function getWinner() {
        return $this->winner;
    }

    /**
     * @param string $vainqueur
     */
    public function setWinner($vainqueur) {
        $this->winner = $vainqueur;
    }

}