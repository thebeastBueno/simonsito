-- MySQL dump 10.16  Distrib 10.1.28-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: simonsito_db
-- ------------------------------------------------------
-- Server version	10.1.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `estudiantes`
--

DROP TABLE IF EXISTS `estudiantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estudiantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(255) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `lugar_nacimiento` varchar(255) DEFAULT NULL,
  `entidad_federal` varchar(255) DEFAULT NULL,
  `nacionalidad_nino` varchar(100) DEFAULT NULL,
  `nivel_grupo_id` int(11) DEFAULT NULL,
  `madre_id` int(11) DEFAULT NULL,
  `padre_id` int(11) DEFAULT NULL,
  `representante_id` int(11) DEFAULT NULL,
  `condiciones_medicas` text,
  `creadoPor` varchar(255) DEFAULT NULL,
  `fechaCreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `nivel_grupo_id` (`nivel_grupo_id`),
  KEY `madre_id` (`madre_id`),
  KEY `padre_id` (`padre_id`),
  KEY `representante_id` (`representante_id`),
  CONSTRAINT `estudiantes_ibfk_1` FOREIGN KEY (`nivel_grupo_id`) REFERENCES `niveles_grupos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `estudiantes_ibfk_2` FOREIGN KEY (`madre_id`) REFERENCES `madres` (`id`) ON DELETE SET NULL,
  CONSTRAINT `estudiantes_ibfk_3` FOREIGN KEY (`padre_id`) REFERENCES `padres` (`id`) ON DELETE SET NULL,
  CONSTRAINT `estudiantes_ibfk_4` FOREIGN KEY (`representante_id`) REFERENCES `representantes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudiantes`
--

LOCK TABLES `estudiantes` WRITE;
/*!40000 ALTER TABLE `estudiantes` DISABLE KEYS */;
INSERT INTO `estudiantes` VALUES (1,'kevin','2025-06-19','el roble','madrid','extrangero',1,1,1,1,'mojado','simonsito_admin','2025-06-02 02:13:44'),(2,'maron','2025-06-19','lomas','roma','china',1,1,1,1,'comida','simonsito_admin','2025-06-03 05:51:11');
/*!40000 ALTER TABLE `estudiantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inscripciones`
--

DROP TABLE IF EXISTS `inscripciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inscripciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_inscripcion` date DEFAULT NULL,
  `tipo_ingreso` enum('Nuevo Ingreso','Prosecucion') DEFAULT NULL,
  `estudiante_id` int(11) DEFAULT NULL,
  `creadoPor` varchar(255) DEFAULT NULL,
  `fechaCreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `nivel_grupo_id` int(11) DEFAULT NULL,
  `madre_id` int(11) DEFAULT NULL,
  `padre_id` int(11) DEFAULT NULL,
  `representante_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estudiante_id` (`estudiante_id`),
  CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inscripciones`
--

LOCK TABLES `inscripciones` WRITE;
/*!40000 ALTER TABLE `inscripciones` DISABLE KEYS */;
INSERT INTO `inscripciones` VALUES (2,NULL,NULL,1,'simonsito_admin','2025-06-03 06:13:58',2,5,3,2);
/*!40000 ALTER TABLE `inscripciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `madres`
--

DROP TABLE IF EXISTS `madres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `madres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `ci` varchar(50) DEFAULT NULL,
  `ocupacion` varchar(255) DEFAULT NULL,
  `nacionalidad` enum('V','E') DEFAULT NULL,
  `vive_con_nino` enum('Si','No') DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `trabaja` enum('Si','No') DEFAULT NULL,
  `lugar_trabajo` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `creadoPor` varchar(255) DEFAULT NULL,
  `fechaCreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `apellido` varchar(255) DEFAULT NULL,
  `cedula` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `madres`
--

LOCK TABLES `madres` WRITE;
/*!40000 ALTER TABLE `madres` DISABLE KEYS */;
INSERT INTO `madres` VALUES (1,'josefa','454545454','nada','V','Si','la calle','No',NULL,'4544445','simonsito_admin','2025-05-29 01:55:23',NULL,NULL),(2,'josefa','454545454','nada','V','Si','la calle','No',NULL,'4544445','simonsito_admin','2025-05-29 01:55:40',NULL,NULL),(5,'adriana',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'56568998','simonsito_admin','2025-06-03 05:57:46','rojaz','454878454');
/*!40000 ALTER TABLE `madres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `niveles_grupos`
--

DROP TABLE IF EXISTS `niveles_grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `niveles_grupos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_nivel_grupo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `niveles_grupos`
--

LOCK TABLES `niveles_grupos` WRITE;
/*!40000 ALTER TABLE `niveles_grupos` DISABLE KEYS */;
INSERT INTO `niveles_grupos` VALUES (1,'nivel-I Grupo II'),(2,'nivel-II Grupo-III');
/*!40000 ALTER TABLE `niveles_grupos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `padres`
--

DROP TABLE IF EXISTS `padres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `padres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `ci` varchar(50) DEFAULT NULL,
  `ocupacion` varchar(255) DEFAULT NULL,
  `nacionalidad` enum('V','E') DEFAULT NULL,
  `vive_con_nino` enum('Si','No') DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `trabaja` enum('Si','No') DEFAULT NULL,
  `lugar_trabajo` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `creadoPor` varchar(255) DEFAULT NULL,
  `fechaCreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `apellido` varchar(255) DEFAULT NULL,
  `cedula` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `padres`
--

LOCK TABLES `padres` WRITE;
/*!40000 ALTER TABLE `padres` DISABLE KEYS */;
INSERT INTO `padres` VALUES (1,'fghgd','fgdhf','fdhf','V','Si','hfdgf','No',NULL,'52342523452325','simonsito_admin','2025-05-29 01:56:31',NULL,NULL),(3,'omar',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'22222222','simonsito_admin','2025-06-03 06:01:48','aguilera','15225478');
/*!40000 ALTER TABLE `padres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `representantes`
--

DROP TABLE IF EXISTS `representantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `representantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `ci` varchar(50) DEFAULT NULL,
  `parentesco` varchar(100) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `ocupacion` varchar(255) DEFAULT NULL,
  `creadoPor` varchar(255) DEFAULT NULL,
  `fechaCreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `apellido` varchar(255) DEFAULT NULL,
  `cedula` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `representantes`
--

LOCK TABLES `representantes` WRITE;
/*!40000 ALTER TABLE `representantes` DISABLE KEYS */;
INSERT INTO `representantes` VALUES (1,'maria','5487963','madeastra','04128683517','la romano','bailar','simonsito_admin','2025-06-02 02:12:31',NULL,NULL),(2,'tobar',NULL,NULL,'12356478',NULL,NULL,'simonsito_admin','2025-06-03 06:05:53','agustini','56565445');
/*!40000 ALTER TABLE `representantes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-03  0:22:32
