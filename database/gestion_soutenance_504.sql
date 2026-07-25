-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : sam. 25 juil. 2026 à 12:56
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_soutenance_504`
--

-- --------------------------------------------------------

--
-- Structure de la table `etudiant`
--

CREATE TABLE `etudiant` (
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenoms` varchar(150) NOT NULL,
  `niveau` varchar(10) NOT NULL,
  `parcours` varchar(50) NOT NULL,
  `adr_email` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `etudiant`
--

INSERT INTO `etudiant` (`matricule`, `nom`, `prenoms`, `niveau`, `parcours`, `adr_email`) VALUES
('2767', 'GISA', 'mainty', 'M2', 'GB', 'gisa@gmail.com'),
('3666T', 'ANJARA', 'MAMAFANJARA', 'L2', 'IG', 'ANJARA@GMAIL.COM'),
('4444', 'YTTYTY', 'GGGG', 'M2', 'GB', 'TYTYT@gmail.com'),
('681 H_TOL', 'RAZAFINDRAVAHY', 'MIANGOLANARIVO', 'L2', 'IG', 'rivoshaiman@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `organisme`
--

CREATE TABLE `organisme` (
  `idorg` int(11) NOT NULL,
  `design` varchar(255) NOT NULL,
  `lieu` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `organisme`
--

INSERT INTO `organisme` (`idorg`, `design`, `lieu`) VALUES
(1, 'rdi', 'fianarantsoa'),
(3, 'ASASA', 'DIEGO'),
(4, ' ADS', ' FIANARANTSOA'),
(5, 'bajaja', ' FIANARANTSOA'),
(6, 'BRED', 'DIEGO');

-- --------------------------------------------------------

--
-- Structure de la table `professeur`
--

CREATE TABLE `professeur` (
  `idprof` varchar(20) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenoms` varchar(150) NOT NULL,
  `civilite` varchar(10) NOT NULL,
  `grade` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `professeur`
--

INSERT INTO `professeur` (`idprof`, `nom`, `prenoms`, `civilite`, `grade`) VALUES
('166', 'TATA', 'TETE', 'Mme', 'Docteur HDR'),
('19', 'RAMBELOSON', 'zaza', 'Mme', 'Docteur HDR'),
('P005', 'RAMBELOSON', 'Gilop', 'Mlle', 'Docteur HDR'),
('p099', 'Shaiman', 'GILBERT', 'Mlle', 'Docteur en Informatique'),
('p1788', 'RZAKANDRAINY', 'jojo', 'Mr', 'Doctorant en Informatique'),
('P222', 'tibo', 'papay', 'Mme', 'Docteur HDR');

-- --------------------------------------------------------

--
-- Structure de la table `soutenir`
--

CREATE TABLE `soutenir` (
  `id` int(11) NOT NULL,
  `matricule` varchar(20) NOT NULL,
  `idorg` int(11) NOT NULL,
  `annee_univ` varchar(20) NOT NULL,
  `note` decimal(4,2) NOT NULL,
  `president` varchar(255) NOT NULL,
  `examinateur` varchar(255) NOT NULL,
  `rapporteur_int` varchar(255) NOT NULL,
  `rapporteur_ext` varchar(255) NOT NULL,
  `date_soutenance` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `soutenir`
--

INSERT INTO `soutenir` (`id`, `matricule`, `idorg`, `annee_univ`, `note`, `president`, `examinateur`, `rapporteur_int`, `rapporteur_ext`, `date_soutenance`) VALUES
(3, '3666T', 4, '2025-2026', 18.15, 'Shaiman GILBERT', 'TATA TETE', 'RAMBELOSON Gilop', 'Shaiman GILBERT', '2026-06-12'),
(4, '681 H_TOL', 5, '2025-2026', 19.75, 'RZAKANDRAINY jojo', 'RAMBELOSON zaza', 'Shaiman GILBERT', 'TATA TETE', '2026-07-20'),
(5, '2767', 6, '2025-2026', 18.75, 'tibo papay', 'RAMBELOSON zaza', 'RZAKANDRAINY jojo', 'Shaiman GILBERT', '2026-07-12');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `etudiant`
--
ALTER TABLE `etudiant`
  ADD PRIMARY KEY (`matricule`);

--
-- Index pour la table `organisme`
--
ALTER TABLE `organisme`
  ADD PRIMARY KEY (`idorg`);

--
-- Index pour la table `professeur`
--
ALTER TABLE `professeur`
  ADD PRIMARY KEY (`idprof`);

--
-- Index pour la table `soutenir`
--
ALTER TABLE `soutenir`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_soutenir_etudiant` (`matricule`),
  ADD KEY `fk_soutenir_organisme` (`idorg`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `organisme`
--
ALTER TABLE `organisme`
  MODIFY `idorg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `soutenir`
--
ALTER TABLE `soutenir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `soutenir`
--
ALTER TABLE `soutenir`
  ADD CONSTRAINT `fk_soutenir_etudiant` FOREIGN KEY (`matricule`) REFERENCES `etudiant` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_soutenir_organisme` FOREIGN KEY (`idorg`) REFERENCES `organisme` (`idorg`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
