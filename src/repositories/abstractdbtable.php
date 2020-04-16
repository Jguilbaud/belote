<?php

namespace Repositories;

/**
 * Classe abstraite pour les classes liées à une table de la base
 *
 * @author johan
 *
 */
abstract class AbstractDbTable extends \StaticAccessClass {

    protected \Services\Database $db;


    protected $oModel = null;

    /**
     * Nom de la table
     *
     * @var string
     */
    protected $tableName = '';

    /**
     * Champs à récupérer lors d'un select
     *
     * @var string
     */
    protected $sqlFields = '*';
    protected $entityModelClassName = 'AbstractModel';


    /**
     *
     * @var String Nom de de la séquence auto incrémentée pour pouvoir récupérer le dernier ID inséré
     */
    protected $sequenceName;

    public function __construct() {
        $this->db = \Services\Database::getInstance();
        $this->sequenceName = $this->tableName . '_id_seq';
    }

    protected function findOneByColumnAndValue(String $columnName, String $searchedValue, String $columns = '*') {
        $result = \Services\Database::getInstance()->getData('SELECT ' . $columns . ' FROM ' . $this->tableName . ' WHERE ' . $columnName . '=:value', array(
            ':value' => $searchedValue
        ));

        if (!$result) {
            throw new \Exceptions\RepositoryRowsNotFound();
        }
        return $this->transformRowToObject($result[0]);
    }

    protected function transformRowsToObjects(\SplFixedArray $sqlRowsResults): \SplFixedArray {
        // On transforme les lignes (tableaux) en objets, note arraywalk ne fonctionne pas avec SplFixedArray
        for($i = 0; $i < $sqlRowsResults->getSize(); $i++) {
            $sqlRowsResults->offsetSet($i, $this->transformRowToObject($sqlRowsResults->offsetGet($i)));
        }
        return $sqlRowsResults;
    }

    protected function transformRowToObject(array $sqlRowResult): \Models\AbstractModel {
        $oObject = new $this->entityModelClassName();
        $oObject->populateObjectFromDb($sqlRowResult);
        return $oObject;
    }

    public function findAll(array $columns = array(), array $filters = array(), String $order = null, String $groupBy = null, $filtersTypes = null, bool $distinct = false) {
        // Formatage SQL des colonnes
        $sqlColumns = '*';
        if (count($columns) > 0) {
            $aColumns = array();

            foreach ( $columns as $columnName ) {
                $aColumns[] = $columnName;
            }
            $sqlColumns = implode(',', $aColumns);
        }

        // Formatage SQL des filtres WHERE
        $sqlWhereValues = array();
        $whereClause = array();
        $sqlWhereClause = '';
        if (count($filters) > 0) {
            foreach ( $filters as $filterName => $filterValue ) {
                $whereClause[$filterName] = $filterName . ' = :' . strtolower($filterName);
                $sqlWhereValues[':' . strtolower($filterName)] = $filterValue;
            }

            $sqlWhereClause = ' WHERE ' . implode(' AND ', $whereClause);
        }

        // Formatage SQL du distinct
        $sqlDistinct = '';
        if ($distinct) {
            $sqlDistinct = 'DISTINCT ';
        }
        $results = \Services\Database::getInstance()->getData('SELECT ' . $sqlDistinct . $sqlColumns . ' FROM ' . $this->tableName . $sqlWhereClause, $sqlWhereValues, $filtersTypes);
        if (!$results) {
            throw new \Exceptions\RepositoryRowsNotFound();
        }

        // On transforme les lignes (tableaux) en objets et on retourne le résultat
        return $this->transformRowsToObjects($results);
    }

    /**
     * Permet de récupérer une ligne en BDD à partir de son id
     *
     * @param String $hash
     * @throws \Exceptions\BeloteException
     * @return object
     */
    public function findOneById(int $id, String $columns = '*') {
        return $this->findOneByColumnAndValue('id', $id, $columns);
    }

    public function getTableName(): String {
        return $this->tableName;
    }

    abstract public function create(\Models\AbstractModel &$object): void;

    abstract public function update(\Models\AbstractModel $object): void;

    public function remove(\Models\AbstractModel $object): void {
        \Services\Database::getInstance()->getData('DELETE FROM ' . $this->tableName . ' WHERE id=:id', array(
            ':id' => $object->getId()
        ));
    }
}