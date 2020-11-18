<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201018162042 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, hash, date, name_north, name_south, name_west, name_east, total_points_ns, total_points_we, cards_deck, id_current_round, step, current_player FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, hash VARCHAR(10) NOT NULL COLLATE BINARY, date INTEGER NOT NULL, name_north VARCHAR(30) NOT NULL COLLATE BINARY, name_south VARCHAR(30) DEFAULT NULL COLLATE BINARY, name_west VARCHAR(30) DEFAULT NULL COLLATE BINARY, name_east VARCHAR(30) DEFAULT NULL COLLATE BINARY, total_points_ns INTEGER NOT NULL, total_points_we INTEGER NOT NULL, cards_deck CLOB DEFAULT NULL COLLATE BINARY --(DC2Type:simple_array)
        , id_current_round INTEGER DEFAULT NULL, step VARCHAR(30) NOT NULL COLLATE BINARY, current_player VARCHAR(1) DEFAULT NULL)');
        $this->addSql('INSERT INTO game (id, hash, date, name_north, name_south, name_west, name_east, total_points_ns, total_points_we, cards_deck, id_current_round, step, current_player) SELECT id, hash, date, name_north, name_south, name_west, name_east, total_points_ns, total_points_we, cards_deck, id_current_round, step, current_player FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
        $this->addSql('DROP INDEX IDX_2762428FA6005CA0');
        $this->addSql('CREATE TEMPORARY TABLE __temp__hand AS SELECT id, round_id, player, cards FROM hand');
        $this->addSql('DROP TABLE hand');
        $this->addSql('CREATE TABLE hand (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, round_id INTEGER NOT NULL, player VARCHAR(1) NOT NULL COLLATE BINARY, cards CLOB DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_2762428FA6005CA0 FOREIGN KEY (round_id) REFERENCES round (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO hand (id, round_id, player, cards) SELECT id, round_id, player, cards FROM __temp__hand');
        $this->addSql('DROP TABLE __temp__hand');
        $this->addSql('CREATE INDEX IDX_2762428FA6005CA0 ON hand (round_id)');
        $this->addSql('DROP INDEX IDX_C5EEEA34E48FD905');
        $this->addSql('CREATE TEMPORARY TABLE __temp__round AS SELECT id, game_id, round_number, points_ns, points_we, trump_color, dealer, taker, current_turn_id FROM round');
        $this->addSql('DROP TABLE round');
        $this->addSql('CREATE TABLE round (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, game_id INTEGER NOT NULL, round_number SMALLINT NOT NULL, points_ns INTEGER NOT NULL, points_we INTEGER NOT NULL, trump_color VARCHAR(7) DEFAULT NULL COLLATE BINARY, dealer VARCHAR(1) NOT NULL COLLATE BINARY, taker VARCHAR(1) DEFAULT NULL COLLATE BINARY, current_turn_id SMALLINT NOT NULL, CONSTRAINT FK_C5EEEA34E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO round (id, game_id, round_number, points_ns, points_we, trump_color, dealer, taker, current_turn_id) SELECT id, game_id, round_number, points_ns, points_we, trump_color, dealer, taker, current_turn_id FROM __temp__round');
        $this->addSql('DROP TABLE __temp__round');
        $this->addSql('CREATE INDEX IDX_C5EEEA34E48FD905 ON round (game_id)');
        $this->addSql('DROP INDEX IDX_20201547A6005CA0');
        $this->addSql('CREATE TEMPORARY TABLE __temp__turn AS SELECT id, round_id, turn_number, first_player, card_n, card_e, card_s, card_w, winner FROM turn');
        $this->addSql('DROP TABLE turn');
        $this->addSql('CREATE TABLE turn (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, round_id INTEGER NOT NULL, turn_number SMALLINT NOT NULL, first_player VARCHAR(1) NOT NULL COLLATE BINARY, card_n VARCHAR(3) NOT NULL COLLATE BINARY, card_e VARCHAR(3) NOT NULL COLLATE BINARY, card_s VARCHAR(3) NOT NULL COLLATE BINARY, card_w VARCHAR(3) NOT NULL COLLATE BINARY, winner VARCHAR(1) NOT NULL COLLATE BINARY, CONSTRAINT FK_20201547A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO turn (id, round_id, turn_number, first_player, card_n, card_e, card_s, card_w, winner) SELECT id, round_id, turn_number, first_player, card_n, card_e, card_s, card_w, winner FROM __temp__turn');
        $this->addSql('DROP TABLE __temp__turn');
        $this->addSql('CREATE INDEX IDX_20201547A6005CA0 ON turn (round_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, hash, date, name_north, name_south, name_west, name_east, total_points_ns, total_points_we, cards_deck, id_current_round, step, current_player FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, hash VARCHAR(10) NOT NULL, date INTEGER NOT NULL, name_north VARCHAR(30) NOT NULL, name_south VARCHAR(30) DEFAULT NULL, name_west VARCHAR(30) DEFAULT NULL, name_east VARCHAR(30) DEFAULT NULL, total_points_ns INTEGER NOT NULL, total_points_we INTEGER NOT NULL, cards_deck CLOB DEFAULT NULL --(DC2Type:simple_array)
        , id_current_round INTEGER DEFAULT NULL, step VARCHAR(30) NOT NULL, current_player VARCHAR(1) NOT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO game (id, hash, date, name_north, name_south, name_west, name_east, total_points_ns, total_points_we, cards_deck, id_current_round, step, current_player) SELECT id, hash, date, name_north, name_south, name_west, name_east, total_points_ns, total_points_we, cards_deck, id_current_round, step, current_player FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
        $this->addSql('DROP INDEX IDX_2762428FA6005CA0');
        $this->addSql('CREATE TEMPORARY TABLE __temp__hand AS SELECT id, round_id, player, cards FROM hand');
        $this->addSql('DROP TABLE hand');
        $this->addSql('CREATE TABLE hand (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, round_id INTEGER NOT NULL, player VARCHAR(1) NOT NULL, cards CLOB DEFAULT NULL)');
        $this->addSql('INSERT INTO hand (id, round_id, player, cards) SELECT id, round_id, player, cards FROM __temp__hand');
        $this->addSql('DROP TABLE __temp__hand');
        $this->addSql('CREATE INDEX IDX_2762428FA6005CA0 ON hand (round_id)');
        $this->addSql('DROP INDEX IDX_C5EEEA34E48FD905');
        $this->addSql('CREATE TEMPORARY TABLE __temp__round AS SELECT id, game_id, round_number, points_ns, points_we, trump_color, dealer, taker, current_turn_id FROM round');
        $this->addSql('DROP TABLE round');
        $this->addSql('CREATE TABLE round (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, game_id INTEGER NOT NULL, round_number SMALLINT NOT NULL, points_ns INTEGER NOT NULL, points_we INTEGER NOT NULL, trump_color VARCHAR(7) DEFAULT NULL, dealer VARCHAR(1) NOT NULL, taker VARCHAR(1) DEFAULT NULL, current_turn_id SMALLINT NOT NULL)');
        $this->addSql('INSERT INTO round (id, game_id, round_number, points_ns, points_we, trump_color, dealer, taker, current_turn_id) SELECT id, game_id, round_number, points_ns, points_we, trump_color, dealer, taker, current_turn_id FROM __temp__round');
        $this->addSql('DROP TABLE __temp__round');
        $this->addSql('CREATE INDEX IDX_C5EEEA34E48FD905 ON round (game_id)');
        $this->addSql('DROP INDEX IDX_20201547A6005CA0');
        $this->addSql('CREATE TEMPORARY TABLE __temp__turn AS SELECT id, round_id, turn_number, first_player, card_n, card_e, card_s, card_w, winner FROM turn');
        $this->addSql('DROP TABLE turn');
        $this->addSql('CREATE TABLE turn (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, round_id INTEGER NOT NULL, turn_number SMALLINT NOT NULL, first_player VARCHAR(1) NOT NULL, card_n VARCHAR(3) NOT NULL, card_e VARCHAR(3) NOT NULL, card_s VARCHAR(3) NOT NULL, card_w VARCHAR(3) NOT NULL, winner VARCHAR(1) NOT NULL)');
        $this->addSql('INSERT INTO turn (id, round_id, turn_number, first_player, card_n, card_e, card_s, card_w, winner) SELECT id, round_id, turn_number, first_player, card_n, card_e, card_s, card_w, winner FROM __temp__turn');
        $this->addSql('DROP TABLE __temp__turn');
        $this->addSql('CREATE INDEX IDX_20201547A6005CA0 ON turn (round_id)');
    }
}
