-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : sam. 18 avr. 2020 à 18:49
-- Version du serveur :  10.2.31-MariaDB
-- Version de PHP : 7.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `belote`
--

-- --------------------------------------------------------

--
-- Structure de la table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `hash` varchar(10) NOT NULL,
  `date` int(11) NOT NULL,
  `name_north` varchar(30) NOT NULL DEFAULT 'Nord',
  `name_south` varchar(30) NOT NULL DEFAULT 'Sud',
  `name_west` varchar(30) NOT NULL DEFAULT 'Ouest',
  `name_east` varchar(30) NOT NULL DEFAULT 'Est',
  `total_points_ns` int(11) NOT NULL DEFAULT 0,
  `total_points_we` int(11) NOT NULL DEFAULT 0,
  `cards` text NOT NULL,
  `id_current_round` int(11) NOT NULL DEFAULT 0,
  `step` varchar(30) NOT NULL DEFAULT 'join',
  `current_player` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `hands`
--

CREATE TABLE `hands` (
  `id` int(11) NOT NULL,
  `id_round` int(11) NOT NULL,
  `player` varchar(1) NOT NULL,
  `cards` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `rounds`
--

CREATE TABLE `rounds` (
  `id` int(11) NOT NULL,
  `id_game` int(11) NOT NULL,
  `num_round` tinyint(11) NOT NULL DEFAULT 1,
  `points_ns` int(11) NOT NULL DEFAULT 0 COMMENT 'nord-sud',
  `points_we` int(11) NOT NULL DEFAULT 0 COMMENT 'ouest-est',
  `trump_color` varchar(7) DEFAULT '',
  `dealer` varchar(1) NOT NULL,
  `taker` varchar(1) DEFAULT '',
  `id_current_turn` int(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `turns`
--

CREATE TABLE `turns` (
  `id` int(11) NOT NULL,
  `id_round` int(11) NOT NULL,
  `num_turn` tinyint(4) NOT NULL DEFAULT 0,
  `first_player` varchar(1) NOT NULL DEFAULT '',
  `card_n` varchar(3) NOT NULL DEFAULT '',
  `card_e` varchar(3) NOT NULL DEFAULT '',
  `card_s` varchar(3) NOT NULL DEFAULT '',
  `card_w` varchar(3) NOT NULL DEFAULT '',
  `winner` varchar(1) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hash` (`hash`);

--
-- Index pour la table `hands`
--
ALTER TABLE `hands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE( `id_round`, `player`); 

--
-- Index pour la table `rounds`
--
ALTER TABLE `rounds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_partie` (`id_game`,`num_round`);

--
-- Index pour la table `turns`
--
ALTER TABLE `turns`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `hands`
--
ALTER TABLE `hands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rounds`
--
ALTER TABLE `rounds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `turns`
--
ALTER TABLE `turns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
