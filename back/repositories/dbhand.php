<?php

namespace Repositories;

class DbHand extends AbstractDbTable {

    /**
     * Nom de la table
     *
     * @var string
     */
    protected $tableName = 'mains';
    protected $entityModelClassName = \Models\Hand::class;

    public function create(\Models\AbstractModel &$oHand): void {
        $query = 'INSERT INTO ' . $this->tableName . ' (id_manche,joueur,cartes)
        VALUES (:id_manche,:joueur,:cartes)';

        $values = array(
            ':id_manche' => $oHand->getId_manche(),
            ':joueur' => $oHand->getJoueur(),
            ':cartes' => json_encode(array_values($oHand->getCartes()))
        );

        $this->db->setData($query, $values);

        // On récupère lid généré
        $oHand->setId($this->db->getLastInsertRowId());
    }

    public function update(\Models\AbstractModel $oHand): void {
        $query = 'UPDATE ' . $this->tableName . ' SET
                    id_manche = :id_manche,
                    joueur = :joueur,
                    cartes = :cartes
                WHERE id=:id';

        $values = array(
            ':id' =>  $oHand->getId(),
            ':id_manche' => $oHand->getId_manche(),
            ':joueur' => $oHand->getJoueur(),
            ':cartes' => json_encode(array_values($oHand->getCartes()))
        );

        $this->db->setData($query, $values);



    }

    public function findOneByRoundAndPlayer(int $idRound, String $player, String $columns = '*') {
        $result = \Services\Database::getInstance()->getData('SELECT ' . $columns . ' FROM ' . $this->tableName . '
                                                                WHERE id_manche=:id_manche AND joueur=:joueur', array(
            ':id_manche' => $idRound,
            ':joueur' => $player
        ));

        if (!$result) {
            throw new \Exceptions\RepositoryRowsNotFound();
        }
        return $this->transformRowToObject($result[0]);
    }
}