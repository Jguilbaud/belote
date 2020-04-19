<?php

namespace Entities;

class Round extends AbstractEntity {
    protected int $id = 0;
    protected int $id_game = 0;
    protected int $num_round = 0;
    protected int $points_ns = 0;
    protected int $points_we = 0;
    protected String $trump_color = '';
    protected String $dealer = '';
    protected String $taker = '';
    protected int $id_current_turn = 0;

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
    public function setId(int $id) {
        $this->id = $id;
    }

    /**
     *
     * @return number
     */
    public function getId_game() {
        return $this->id_game;
    }

    /**
     *
     * @param number $id_partie
     */
    public function setId_game(int $id_partie) {
        $this->id_game = $id_partie;
    }

    /**
     *
     * @return number
     */
    public function getNum_round() {
        return $this->num_round;
    }

    /**
     *
     * @param number $num_manche
     */
    public function setNum_round(int $num_manche) {
        $this->num_round = $num_manche;
    }

    /**
     *
     * @return number
     */
    public function getPoints_ns() {
        return $this->points_ns;
    }

    /**
     *
     * @param number $points_ns
     */
    public function setPoints_ns(int $points_ns) {
        $this->points_ns = $points_ns;
    }

    /**
     *
     * @return number
     */
    public function getPoints_we() {
        return $this->points_we;
    }

    /**
     *
     * @param number $points_oe
     */
    public function setPoints_we(int $points_oe) {
        $this->points_we = $points_oe;
    }

    /**
     *
     * @return string
     */
    public function getTrump_color() {
        return $this->trump_color;
    }

    /**
     *
     * @param string $atout
     */
    public function setTrump_color(String $atout) {
        $this->trump_color = $atout;
    }

    /**
     *
     * @return string
     */
    public function getTaker() {
        return $this->taker;
    }

    /**
     *
     * @param string $preneur
     */
    public function setTaker(String $preneur) {
        $this->taker = $preneur;
    }
    /**
     * @return number
     */
    public function getId_current_turn() {
        return $this->id_current_turn;
    }

    /**
     * @param number $id_tour_courant
     */
    public function setId_current_turn($id_tour_courant) {
        $this->id_current_turn = $id_tour_courant;
    }


    /**
     *
     * @return string
     */
    public function getDealer() {
        return $this->dealer;
    }

    /**
     *
     * @param string $donneur
     */
    public function setDealer($donneur) {
        $this->dealer = $donneur;
    }


}
