<?php

namespace Repositories;

class DbHand extends AbstractDbTable {

    /**
     * Nom de la table
     *
     * @var string
     */
    protected $tableName = 'hands';
    protected $entityClassName = \Entities\Hand::class;

    public function create(\Entities\AbstractEntity &$oHand): void {
        $query = 'INSERT INTO ' . $this->tableName . ' (id_round,player,cards)
        VALUES (:id_round,:player,:cards)';

        $values = array(
            ':id_round' => $oHand->getId_round(),
            ':player' => $oHand->getPlayer(),
            ':cards' => json_encode(array_values($oHand->getCards()))
        );

        $this->db->setData($query, $values);

        // On récupère lid généré
        $oHand->setId($this->db->getLastInsertRowId());
    }

    public function update(\Entities\AbstractEntity $oHand): void {
        $query = 'UPDATE ' . $this->tableName . ' SET
                    id_round = :id_round,
                    player = :player,
                    cards = :cards
                WHERE id=:id';

        $values = array(
            ':id' =>  $oHand->getId(),
            ':id_round' => $oHand->getId_round(),
            ':player' => $oHand->getPlayer(),
            ':cards' => json_encode(array_values($oHand->getCards()))
        );

        $this->db->setData($query, $values);



    }

    public function findOneByRoundAndPlayer(int $idRound, String $player, String $columns = '*') {
        $result = \Services\Database::get()->getData('SELECT ' . $columns . ' FROM ' . $this->tableName . '
                                                                WHERE id_round=:id_round AND player=:player', array(
            ':id_round' => $idRound,
            ':player' => $player
        ));

        if (!$result) {
            throw new \Exceptions\RepositoryRowsNotFound();
        }
        return $this->transformRowToObject($result[0]);
    }
}