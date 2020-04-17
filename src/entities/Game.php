<?php

namespace Entities;

class Game extends AbstractEntity {
    protected int $id = 0;
    protected String $hash = '';
    protected int $date = 0;
    protected String $nom_nord = 'Nord';
    protected String $nom_sud = 'Sud';
    protected String $nom_ouest = 'Ouest';
    protected String $nom_est = 'Est';
    protected int $total_points_ns = 0;
    protected int $total_points_oe = 0;
    protected array $cartes = array();
    protected int $id_manche_courante = 0;

    public function populateObjectFromDb(array $dbRow, array $excludedParticularFields = array()) :void {
        $excludedParticularFields[] = 'cartes';
        parent::populateObjectFromDb($dbRow,$excludedParticularFields);
        $this->cartes = json_decode($dbRow['cartes'],true);
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
    public function getNom_nord() {
        return $this->nom_nord;
    }

    /**
     *
     * @param string $nom_nord
     */
    public function setNom_nord($nom_nord) {
        $this->nom_nord = $nom_nord;
    }

    /**
     *
     * @return string
     */
    public function getNom_sud() {
        return $this->nom_sud;
    }

    /**
     *
     * @param string $nom_sud
     */
    public function setNom_sud($nom_sud) {
        $this->nom_sud = $nom_sud;
    }

    /**
     *
     * @return string
     */
    public function getNom_ouest() {
        return $this->nom_ouest;
    }

    /**
     *
     * @param string $nom_ouest
     */
    public function setNom_ouest($nom_ouest) {
        $this->nom_ouest = $nom_ouest;
    }

    /**
     *
     * @return string
     */
    public function getNom_est() {
        return $this->nom_est;
    }

    /**
     *
     * @param string $nom_est
     */
    public function setNom_est($nom_est) {
        $this->nom_est = $nom_est;
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
    public function getTotal_points_oe() {
        return $this->total_points_oe;
    }

    /**
     *
     * @param int $total_points_oe
     */
    public function setTotal_points_oe($total_points_oe) {
        $this->total_points_oe = $total_points_oe;
    }

    /**
     *
     * @return multitype:
     */
    public function getCartes() {
        return $this->cartes;
    }

    /**
     *
     * @param multitype: $cartes
     */
    public function setCartes($cartes) {
        $this->cartes = $cartes;
    }



    /**
     * @return number
     */
    public function getId_manche_courante() {
        return $this->id_manche_courante;
    }

    /**
     * @param number $id_manche
     */
    public function setId_manche_courante($id_manche) {
        $this->id_manche_courante = $id_manche;
    }
}