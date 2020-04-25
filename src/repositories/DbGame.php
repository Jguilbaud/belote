<?php

namespace Repositories;

class DbGame extends AbstractDbTable {

    /**
     * Nom de la table
     *
     * @var string
     */
    protected $tableName = 'games';
    protected $entityClassName = \Entities\Game::class;

    public function create(\Entities\AbstractEntity &$oGame): void {
        $query = 'INSERT INTO ' . $this->tableName . ' (`hash`, `date`, `name_north`, `name_south`, `name_west`, `name_east`, `cards`,`step`)
        VALUES (:hash, :date, :name_north, :name_south, :name_west, :name_east,:cards,:step)';

        $values = array(
            ':hash' => $oGame->getHash(),
            ':date' => time(),
            ':name_north' => $oGame->getName_north(),
            ':name_south' => $oGame->getName_south(),
            ':name_west' => $oGame->getName_west(),
            ':name_east' => $oGame->getName_east(),
            ':cards' => json_encode(array_values($oGame->getCards())),
            ':step' => $oGame->getStep()
        );

        $this->db->setData($query, $values);

        // On récupère lid généré
        $oGame->setId($this->db->getLastInsertRowId());
    }

    public function update(\Entities\AbstractEntity $oGame): void {
        $query = 'UPDATE ' . $this->tableName . ' SET
                    name_north = :name_north,
                    name_south = :name_south,
                    name_west = :name_west,
                    name_east = :name_east,
                    total_points_ns = :total_points_ns,
                    total_points_we = :total_points_we,
                    cards = :cards,
                    id_current_round = :id_current_round,
                    step = :step,
                    current_player = :current_player
                WHERE id=:id';

        $values = array(
            ':id' => $oGame->getId(),
            ':name_north' => $oGame->getName_north(),
            ':name_south' => $oGame->getName_south(),
            ':name_west' => $oGame->getName_west(),
            ':name_east' => $oGame->getName_east(),
            ':cards' => json_encode(array_values($oGame->getCards())),
            ':id_current_round' => $oGame->getId_current_round(),
            ':total_points_ns' => $oGame->getTotal_points_ns(),
            ':total_points_we' => $oGame->getTotal_points_we(),
            ':step' => $oGame->getStep(),
            ':current_player' => $oGame->getCurrent_player()


        );

        $this->db->setData($query, $values);
    }

    public function findOneByHash(String $hash) : \Entities\Game {
        return $this->findOneByColumnAndValue('hash', $hash);
    }
}