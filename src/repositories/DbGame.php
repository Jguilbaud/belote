<?php

namespace Repositories;

class DbGame extends AbstractDbTable {

    /**
     * Nom de la table
     *
     * @var string
     */
    protected $tableName = 'parties';
    protected $entityClassName = \Entities\Game::class;

    public function create(\Entities\AbstractEntity &$oGame): void {
        $query = 'INSERT INTO ' . $this->tableName . ' (`hash`, `date`, `nom_nord`, `nom_sud`, `nom_ouest`, `nom_est`, `cartes`)
        VALUES (:hash, :date, :nom_nord, :nom_sud, :nom_ouest, :nom_est,:cartes)';

        $values = array(
            ':hash' => $oGame->getHash(),
            ':date' => time(),
            ':nom_nord' => $oGame->getNom_nord(),
            ':nom_sud' => $oGame->getNom_sud(),
            ':nom_ouest' => $oGame->getNom_ouest(),
            ':nom_est' => $oGame->getNom_est(),
            ':cartes' => json_encode(array_values($oGame->getCartes()))
        );

        $this->db->setData($query, $values);

        // On récupère lid généré
        $oGame->setId($this->db->getLastInsertRowId());
    }

    public function update(\Entities\AbstractEntity $oGame): void {
        $query = 'UPDATE ' . $this->tableName . ' SET
                    nom_nord = :nom_nord,
                    nom_sud = :nom_sud,
                    nom_ouest = :nom_ouest,
                    nom_est = :nom_est,
                    total_points_ns = :total_points_ns,
                    total_points_oe = :total_points_oe,
                    cartes = :cartes,
                    id_manche_courante = :id_manche_courante
                WHERE id=:id';

        $values = array(
            ':id' => $oGame->getId(),
            ':nom_nord' => $oGame->getNom_nord(),
            ':nom_sud' => $oGame->getNom_sud(),
            ':nom_ouest' => $oGame->getNom_ouest(),
            ':nom_est' => $oGame->getNom_est(),
            ':cartes' => json_encode(array_values($oGame->getCartes())),
            ':id_manche_courante' => $oGame->getId_manche_courante(),
            ':total_points_ns' => $oGame->getTotal_points_oe(),
            ':total_points_oe' => $oGame->getTotal_points_oe()
        );

        $this->db->setData($query, $values);
    }

    public function findOneByHash(String $hash) {
        return $this->findOneByColumnAndValue('hash', $hash);
    }
}