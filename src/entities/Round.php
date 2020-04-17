<?php

namespace Entities;

class Round extends AbstractEntity {
    protected int $id = 0;
    protected int $id_partie = 0;
    protected int $num_manche = 0;
    protected int $points_ns = 0;
    protected int $points_oe = 0;
    protected String $atout = '';
    protected String $donneur = '';
    protected String $preneur = '';
    protected int $id_tour_courant = 0;

    /**
     *
     * @return string
     */
    public function getDonneur() {
        return $this->donneur;
    }

    /**
     *
     * @param string $donneur
     */
    public function setDonneur($donneur) {
        $this->donneur = $donneur;
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
    public function setId(int $id) {
        $this->id = $id;
    }

    /**
     *
     * @return number
     */
    public function getId_partie() {
        return $this->id_partie;
    }

    /**
     *
     * @param number $id_partie
     */
    public function setId_partie(int $id_partie) {
        $this->id_partie = $id_partie;
    }

    /**
     *
     * @return number
     */
    public function getNum_manche() {
        return $this->num_manche;
    }

    /**
     *
     * @param number $num_manche
     */
    public function setNum_manche(int $num_manche) {
        $this->num_manche = $num_manche;
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
    public function getPoints_oe() {
        return $this->points_oe;
    }

    /**
     *
     * @param number $points_oe
     */
    public function setPoints_oe(int $points_oe) {
        $this->points_oe = $points_oe;
    }

    /**
     *
     * @return string
     */
    public function getAtout() {
        return $this->atout;
    }

    /**
     *
     * @param string $atout
     */
    public function setAtout(String $atout) {
        $this->atout = $atout;
    }

    /**
     *
     * @return string
     */
    public function getPreneur() {
        return $this->preneur;
    }

    /**
     *
     * @param string $preneur
     */
    public function setPreneur(String $preneur) {
        $this->preneur = $preneur;
    }
    /**
     * @return number
     */
    public function getId_tour_courant() {
        return $this->id_tour_courant;
    }

    /**
     * @param number $id_tour_courant
     */
    public function setId_tour_courant($id_tour_courant) {
        $this->id_tour_courant = $id_tour_courant;
    }

}
