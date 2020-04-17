<?php

namespace Repositories;

class DbRound extends AbstractDbTable {

    /**
     * Nom de la table
     *
     * @var string
     */
    protected $tableName = 'manches';
    protected $entityClassName = \Entities\Round::class;

    public function create(\Entities\AbstractEntity &$oRound): void {
        $query = 'INSERT INTO ' . $this->tableName . ' (id_partie,num_manche,donneur)
        VALUES (:id_partie,:num_manche,:donneur)';


        $values = array(
            ':id_partie' => $oRound->getId_partie(),
            ':num_manche' => $oRound->getNum_manche(),
            ':donneur' => $oRound->getDonneur()
        );

        $this->db->setData($query, $values);

        // On récupère lid généré
        $oRound->setId($this->db->getLastInsertRowId());
    }

    public function update(\Entities\AbstractEntity $oRound): void {
        $query = 'UPDATE ' . $this->tableName . ' SET
                    id_partie = :id_partie,
                    num_manche = :num_manche,
                    points_ns = :points_ns,
                    points_oe = :points_oe,
                    atout = :atout,
                    donneur = :donneur,
                    preneur = :preneur,
                    id_tour_courant = :id_tour_courant
                WHERE id=:id';

        $values = array(
            ':id' =>  $oRound->getId(),
            ':id_partie' => $oRound->getId_partie(),
            ':num_manche' => $oRound->getNum_manche(),
            ':points_ns' => $oRound->getPoints_ns(),
            ':points_oe' => $oRound->getPoints_oe(),
            ':atout' => $oRound->getAtout(),
            ':donneur' => $oRound->getDonneur(),
            ':preneur' => $oRound->getPreneur(),
            ':id_tour_courant' => $oRound->getId_tour_courant()
        );

        $this->db->setData($query, $values);




    }
}