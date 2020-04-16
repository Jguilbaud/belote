<?php

namespace Models;

class Hand extends AbstractModel {
    protected int $id = 0;
    protected int $id_manche;
    protected String $joueur = '';
    protected array $cartes = array();


    public function populateObjectFromDb(array $dbRow, array $excludedParticularFields = array()) :void {
        $excludedParticularFields[] = 'cartes';
        parent::populateObjectFromDb($dbRow,$excludedParticularFields);
        $this->cartes = json_decode($dbRow['cartes'],true);
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
    public function getId_manche() {
        return $this->id_manche;
    }

    /**
     *
     * @param mixed $id_manche
     */
    public function setId_manche($id_manche) {
        $this->id_manche = $id_manche;
    }

    /**
     *
     * @return string
     */
    public function getJoueur() {
        return $this->joueur;
    }

    /**
     *
     * @param string $joueur
     */
    public function setJoueur($joueur) {
        $this->joueur = $joueur;
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
}