<?php

namespace Repositories;

class DbTurn extends AbstractDbTable {

    /**
     * Nom de la table
     *
     * @var string
     */
    protected $tableName = 'turns';
    protected $entityClassName = \Entities\Turn::class;

    public function create(\Entities\AbstractEntity &$oTurn): void {
        $query = 'INSERT INTO ' . $this->tableName . ' (id_round,num_turn,first_player)
        VALUES (:id_round,:num_turn,:first_player)';

        $values = array(
            ':id_round' => $oTurn->getId_round(),
            ':num_turn' => $oTurn->getNum_turn(),
            ':first_player' => strtolower($oTurn->getFirst_player())
        );

        $this->db->setData($query, $values);

        // On récupère lid généré
        $oTurn->setId($this->db->getLastInsertRowId());
    }

    public function update(\Entities\AbstractEntity $oTurn): void {
        $query = 'UPDATE ' . $this->tableName . ' SET
                    id_round = :id_round,
                    num_turn = :num_turn,
                    first_player = :first_player,
                    card_n = :card_n,
                    card_e = :card_e,
                    card_s = :card_s,
                    card_w = :card_w,
                    winner = :winner
                WHERE id=:id';

        $values = array(
            ':id' => $oTurn->getId(),
            ':id_round' => $oTurn->getId_round(),
            ':num_turn' => $oTurn->getNum_turn(),
            ':first_player' => strtolower($oTurn->getFirst_player()),
            ':winner' => strtolower($oTurn->getWinner()),
            ':card_n' => $oTurn->getCard_n(),
            ':card_e' => $oTurn->getCard_e(),
            ':card_s' => $oTurn->getCard_s(),
            ':card_w' => $oTurn->getCard_w()
        );

        $this->db->setData($query, $values);
    }
}