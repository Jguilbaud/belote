<?php

namespace Models;

class Turn extends AbstractModel {
    protected int $id = 0;
    protected int $id_manche = 0;
    protected int $num_tour = 0;
    protected String $premier_joueur = '';
    protected String $carte_n = '';
    protected String $carte_e = '';
    protected String $carte_s = '';
    protected String $carte_o = '';
    protected String $vainqueur = '';
    /**
     * @return string
     */
    public function getPremier_joueur() {
        return $this->premier_joueur;
    }

    /**
     * @param string $premier_joueur
     */
    public function setPremier_joueur($premier_joueur) {
        $this->premier_joueur = $premier_joueur;
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
    public function getId_manche() {
        return $this->id_manche;
    }

    /**
     * @param number $id_manche
     */
    public function setId_manche($id_manche) {
        $this->id_manche = $id_manche;
    }

    /**
     * @return number
     */
    public function getNum_tour() {
        return $this->num_tour;
    }

    /**
     * @param number $num_tour
     */
    public function setNum_tour($num_tour) {
        $this->num_tour = $num_tour;
    }

    /**
     * @return string
     */
    public function getCarte_n() {
        return $this->carte_n;
    }

    /**
     * @param string $carte_n
     */
    public function setCarte_n($carte_n) {
        $this->carte_n = $carte_n;
    }

    /**
     * @return string
     */
    public function getCarte_e() {
        return $this->carte_e;
    }

    /**
     * @param string $carte_e
     */
    public function setCarte_e($carte_e) {
        $this->carte_e = $carte_e;
    }

    /**
     * @return string
     */
    public function getCarte_s() {
        return $this->carte_s;
    }

    /**
     * @param string $carte_s
     */
    public function setCarte_s($carte_s) {
        $this->carte_s = $carte_s;
    }

    /**
     * @return string
     */
    public function getCarte_o() {
        return $this->carte_o;
    }

    /**
     * @param string $carte_o
     */
    public function setCarte_o($carte_o) {
        $this->carte_o = $carte_o;
    }

    /**
     * @return string
     */
    public function getVainqueur() {
        return $this->vainqueur;
    }

    /**
     * @param string $vainqueur
     */
    public function setVainqueur($vainqueur) {
        $this->vainqueur = $vainqueur;
    }

}