<?php

namespace Entities;

abstract class AbstractEntity {

    /**
     * Surcharge de la méthode magique pour générer automatiquement les assesseurs
     * sous le format getNomPropriete pour le get de nomPropriete
     *
     * @param String $name
     * @param array $arguments
     */
    /*
     * public function __call($name, $arguments = array()) {
     * $matches = array();
     * if (preg_match('#^(set|get)(.*)$#', $name, $matches)) {
     * if ($matches[1] == 'set') {
     * $this->{'set' . ucfirst($matches[2])}($arguments[0]);
     * } else {
     * $this->{'get' . ucfirst($matches[2])}();
     * }
     * }
     * }
     */

    /**
     * Alimente les propriétés de l'objet à partir d'une ligne de résultat de la bdd
     * Surcharger cette méthode pour gérer les cas particulier après avoir fait un parent::populateObjectFromDb()
     *
     * @param Array $dbRow
     */
    public function populateObjectFromDb(array $dbRow, array $excludedParticularFields = array()): void {
        // on parcoure les résultats
        foreach ( $dbRow as $name => $value ) {
            if (property_exists($this, $name) && !in_array($name, $excludedParticularFields)) {
                $this->$name = $value;
            }
        }
    }
}