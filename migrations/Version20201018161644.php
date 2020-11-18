<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201018161644 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, hash VARCHAR(10) NOT NULL, date INTEGER NOT NULL, name_north VARCHAR(30) NOT NULL, name_south VARCHAR(30) DEFAULT NULL, name_west VARCHAR(30) DEFAULT NULL, name_east VARCHAR(30) DEFAULT NULL, total_points_ns INTEGER NOT NULL, total_points_we INTEGER NOT NULL, cards_deck CLOB DEFAULT NULL --(DC2Type:simple_array)
        , id_current_round INTEGER DEFAULT NULL, step VARCHAR(30) NOT NULL, current_player VARCHAR(1) NOT NULL)');
        $this->addSql('CREATE TABLE hand (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, round_id INTEGER NOT NULL, player VARCHAR(1) NOT NULL, cards CLOB DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_2762428FA6005CA0 ON hand (round_id)');
        $this->addSql('CREATE TABLE round (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, game_id INTEGER NOT NULL, round_number SMALLINT NOT NULL, points_ns INTEGER NOT NULL, points_we INTEGER NOT NULL, trump_color VARCHAR(7) DEFAULT NULL, dealer VARCHAR(1) NOT NULL, taker VARCHAR(1) DEFAULT NULL, current_turn_id SMALLINT NOT NULL)');
        $this->addSql('CREATE INDEX IDX_C5EEEA34E48FD905 ON round (game_id)');
        $this->addSql('CREATE TABLE turn (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, round_id INTEGER NOT NULL, turn_number SMALLINT NOT NULL, first_player VARCHAR(1) NOT NULL, card_n VARCHAR(3) NOT NULL, card_e VARCHAR(3) NOT NULL, card_s VARCHAR(3) NOT NULL, card_w VARCHAR(3) NOT NULL, winner VARCHAR(1) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_20201547A6005CA0 ON turn (round_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE hand');
        $this->addSql('DROP TABLE round');
        $this->addSql('DROP TABLE turn');
    }
}
