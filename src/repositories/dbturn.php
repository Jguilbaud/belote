<?php

namespace Repositories;

class DbTurn extends AbstractDbTable {

    /**
     * Nom de la table
     *
     * @var string
     */
    protected $tableName = 'tours';
    protected $entityModelClassName = \Models\Turn::class;

    public function create(\Models\AbstractModel &$oTurn): void {
        $query = 'INSERT INTO ' . $this->tableName . ' (id_manche,num_tour,premier_joueur)
        VALUES (:id_manche,:num_tour,:premier_joueur)';

        $values = array(
            ':id_manche' => $oTurn->getId_manche(),
            ':num_tour' => $oTurn->getNum_tour(),
            ':premier_joueur' => $oTurn->getPremier_joueur()
        );

        $this->db->setData($query, $values);

        // On récupère lid généré
        $oTurn->setId($this->db->getLastInsertRowId());
    }

    public function update(\Models\AbstractModel $oTurn): void {
        $query = 'UPDATE ' . $this->tableName . ' SET
                    id_manche = :id_manche,
                    num_tour = :num_tour,
                    premier_joueur = :premier_joueur,
                    carte_n = :carte_n,
                    carte_e = :carte_e,
                    carte_s = :carte_s,
                    carte_o = :carte_o,
                    vainqueur = :vainqueur
                WHERE id=:id';

        $values = array(
            ':id' => $oTurn->getId(),
            ':id_manche' => $oTurn->getId_manche(),
            ':num_tour' => $oTurn->getNum_tour(),
            ':premier_joueur' => $oTurn->getPremier_joueur(),
            ':vainqueur' => $oTurn->getVainqueur(),
            ':carte_n' => $oTurn->getCarte_n(),
            ':carte_e' => $oTurn->getCarte_e(),
            ':carte_s' => $oTurn->getCarte_s(),
            ':carte_o' => $oTurn->getCarte_o()
        );

        $this->db->setData($query, $values);
    }
}