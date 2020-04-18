<?php

namespace Repositories;

class DbRound extends AbstractDbTable {

    /**
     * Nom de la table
     *
     * @var string
     */
    protected $tableName = 'rounds';
    protected $entityClassName = \Entities\Round::class;

    public function create(\Entities\AbstractEntity &$oRound): void {
        $query = 'INSERT INTO ' . $this->tableName . ' (id_game,num_round,dealer)
        VALUES (:id_game,:num_round,:dealer)';


        $values = array(
            ':id_game' => $oRound->getId_game(),
            ':num_round' => $oRound->getNum_round(),
            ':dealer' => $oRound->getDealer()
        );

        $this->db->setData($query, $values);

        // On récupère lid généré
        $oRound->setId($this->db->getLastInsertRowId());
    }

    public function update(\Entities\AbstractEntity $oRound): void {
        $query = 'UPDATE ' . $this->tableName . ' SET
                    id_game = :id_game,
                    num_round = :num_round,
                    points_ns = :points_ns,
                    points_we = :points_we,
                    trump_color = :trump_color,
                    dealer = :dealer,
                    taker = :taker,
                    id_current_turn = :id_current_turn
                WHERE id=:id';

        $values = array(
            ':id' =>  $oRound->getId(),
            ':id_game' => $oRound->getId_game(),
            ':num_round' => $oRound->getNum_round(),
            ':points_ns' => $oRound->getPoints_ns(),
            ':points_we' => $oRound->getPoints_we(),
            ':trump_color' => $oRound->getTrump(),
            ':dealer' => $oRound->getDealer(),
            ':taker' => $oRound->getTaker(),
            ':id_current_turn' => $oRound->getId_current_turn()
        );

        $this->db->setData($query, $values);




    }
}