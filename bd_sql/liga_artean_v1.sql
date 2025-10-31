-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.12.0.7122
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `bd_sql` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bd_sql`;

CREATE TABLE IF NOT EXISTS `equipos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL DEFAULT '0',
  `estadio` varchar(100) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `equipos` (`id`, `nombre`, `estadio`) VALUES
	(1, 'Athletic Club', 'San Mamés'),
	(2, 'Real Sociedad', 'Reale Arena'),
	(3, 'Deportivo Alavés', 'Mendizorrotza'),
	(4, 'S.D. Eibar', 'Ipurua'),
	(5, 'Real Madrid', 'Santiago Bernabéu'),
	(6, 'FC Barcelona', 'Camp Nou');

CREATE TABLE IF NOT EXISTS `partidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jornada` int(11) NOT NULL,
  `id_equipo_local` int(11) NOT NULL,
  `id_equipo_visitante` int(11) NOT NULL,
  `resultado` char(1) NOT NULL,
  `estadio_partido` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_equipo_local` (`id_equipo_local`),
  KEY `id_equipo_visitante` (`id_equipo_visitante`),
  KEY `jornada` (`jornada`),
  CONSTRAINT `partidos_ibfk_1` FOREIGN KEY (`id_equipo_local`) REFERENCES `equipos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `partidos_ibfk_2` FOREIGN KEY (`id_equipo_visitante`) REFERENCES `equipos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`id_equipo_local` <> `id_equipo_visitante`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `partidos` (`id`, `jornada`, `id_equipo_local`, `id_equipo_visitante`, `resultado`, `estadio_partido`) VALUES
	(7, 1, 1, 5, '1', 'San Mamés'),
	(8, 1, 2, 6, 'X', 'Reale Arena'),
	(9, 1, 3, 4, '2', 'Mendizorrotza'),
	(10, 2, 5, 2, 'X', 'Santiago Bernabéu'),
	(11, 2, 6, 1, '1', 'Camp Nou'),
	(12, 2, 4, 3, '2', 'Ipurua');


