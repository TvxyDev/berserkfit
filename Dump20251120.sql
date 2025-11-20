CREATE DATABASE  IF NOT EXISTS `berserkfit` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `berserkfit`;
-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: berserkfit
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `agua`
--

DROP TABLE IF EXISTS `agua`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agua` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `quantidade` decimal(5,2) NOT NULL,
  `data` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `data` (`data`),
  CONSTRAINT `fk_agua_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agua`
--

LOCK TABLES `agua` WRITE;
/*!40000 ALTER TABLE `agua` DISABLE KEYS */;
INSERT INTO `agua` VALUES (1,4,9.50,'2025-11-16');
/*!40000 ALTER TABLE `agua` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alimentacao`
--

DROP TABLE IF EXISTS `alimentacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alimentacao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `calorias` decimal(8,2) NOT NULL,
  `refeicao` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `data` (`data`),
  CONSTRAINT `fk_alimentacao_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alimentacao`
--

LOCK TABLES `alimentacao` WRITE;
/*!40000 ALTER TABLE `alimentacao` DISABLE KEYS */;
INSERT INTO `alimentacao` VALUES (1,5,155.00,'Café da Manhã','pao e agua','2025-11-16'),(2,5,155.00,'Café da Manhã','pao e agua','2025-11-16'),(3,5,155.00,'Café da Manhã','pao e agua','2025-11-16');
/*!40000 ALTER TABLE `alimentacao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `checklist_diario`
--

DROP TABLE IF EXISTS `checklist_diario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checklist_diario` (
  `id_checklist` int(11) NOT NULL AUTO_INCREMENT,
  `id_habito` int(11) NOT NULL,
  `data` date DEFAULT curdate(),
  `concluido` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id_checklist`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checklist_diario`
--

LOCK TABLES `checklist_diario` WRITE;
/*!40000 ALTER TABLE `checklist_diario` DISABLE KEYS */;
/*!40000 ALTER TABLE `checklist_diario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exercicio`
--

DROP TABLE IF EXISTS `exercicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exercicio` (
  `id_exercicio` int(11) NOT NULL AUTO_INCREMENT,
  `id_treino` int(11) NOT NULL,
  `nome_exercicio` varchar(100) NOT NULL,
  `series` int(11) DEFAULT NULL,
  `repeticoes` int(11) DEFAULT NULL,
  `grupo_muscular` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_exercicio`),
  KEY `exercicio_id_treino_foreign` (`id_treino`),
  CONSTRAINT `exercicio_id_treino_foreign` FOREIGN KEY (`id_treino`) REFERENCES `treino` (`id_treino`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercicio`
--

LOCK TABLES `exercicio` WRITE;
/*!40000 ALTER TABLE `exercicio` DISABLE KEYS */;
/*!40000 ALTER TABLE `exercicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habito`
--

DROP TABLE IF EXISTS `habito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habito` (
  `id_habito` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `descricao` varchar(150) DEFAULT NULL,
  `meta_diaria` varchar(100) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_habito`),
  KEY `habito_id_user_foreign` (`id_user`),
  CONSTRAINT `habito_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habito`
--

LOCK TABLES `habito` WRITE;
/*!40000 ALTER TABLE `habito` DISABLE KEYS */;
INSERT INTO `habito` VALUES (1,4,'Beber 3L de agua','5','Saude'),(2,5,'Beber 3.5L de água','3.5','Saúde'),(3,5,'Fazer 100 flexões','100','Exercício'),(4,5,'Fazer 200 polichinelos','200','Exercício'),(5,5,'Caminhar 15.000 passos','15000','Exercício'),(6,5,'Correr 10km','10','Exercício'),(7,5,'Treinar 60 minutos','60','Exercício'),(8,5,'Fazer abdominais (3 séries de 20)','60','Exercício'),(9,4,'1011','5','1515215');
/*!40000 ALTER TABLE `habito` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensagem_motivacional`
--

DROP TABLE IF EXISTS `mensagem_motivacional`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensagem_motivacional` (
  `id_mensagem` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `conteudo` text DEFAULT NULL,
  `meio_envio` varchar(50) DEFAULT NULL,
  `data_envio` datetime DEFAULT NULL,
  PRIMARY KEY (`id_mensagem`),
  KEY `mensagem_motivacional_id_user_foreign` (`id_user`),
  CONSTRAINT `mensagem_motivacional_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensagem_motivacional`
--

LOCK TABLES `mensagem_motivacional` WRITE;
/*!40000 ALTER TABLE `mensagem_motivacional` DISABLE KEYS */;
/*!40000 ALTER TABLE `mensagem_motivacional` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meta_usuario`
--

DROP TABLE IF EXISTS `meta_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meta_usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_meta` (`id_user`,`tipo`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `fk_meta_usuario_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meta_usuario`
--

LOCK TABLES `meta_usuario` WRITE;
/*!40000 ALTER TABLE `meta_usuario` DISABLE KEYS */;
INSERT INTO `meta_usuario` VALUES (1,4,'agua',10.00),(2,5,'agua',3.50);
/*!40000 ALTER TABLE `meta_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificacao`
--

DROP TABLE IF EXISTS `notificacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificacao` (
  `id_notificacao` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `mensagem` text DEFAULT NULL,
  `data_envio` datetime DEFAULT NULL,
  `enviada` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_notificacao`),
  KEY `notificacao_id_user_foreign` (`id_user`),
  CONSTRAINT `notificacao_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificacao`
--

LOCK TABLES `notificacao` WRITE;
/*!40000 ALTER TABLE `notificacao` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificacao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagamento`
--

DROP TABLE IF EXISTS `pagamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagamento` (
  `id_pagamento` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `tipo_plano` varchar(50) DEFAULT NULL,
  `valor_pago` decimal(10,2) DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `metodo_pagamento` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_pagamento`),
  KEY `pagamento_id_user_foreign` (`id_user`),
  CONSTRAINT `pagamento_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagamento`
--

LOCK TABLES `pagamento` WRITE;
/*!40000 ALTER TABLE `pagamento` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peso`
--

DROP TABLE IF EXISTS `peso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `peso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `peso` decimal(5,2) NOT NULL,
  `data` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `data` (`data`),
  CONSTRAINT `fk_peso_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peso`
--

LOCK TABLES `peso` WRITE;
/*!40000 ALTER TABLE `peso` DISABLE KEYS */;
INSERT INTO `peso` VALUES (1,4,65.00,'2025-11-16');
/*!40000 ALTER TABLE `peso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `treino`
--

DROP TABLE IF EXISTS `treino`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `treino` (
  `id_treino` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `nome_treino` varchar(100) DEFAULT NULL,
  `foco` varchar(100) DEFAULT NULL,
  `data_criacao` date DEFAULT NULL,
  `ficheiro_gerado` varchar(255) DEFAULT '(CURRENT_DATE)',
  PRIMARY KEY (`id_treino`),
  KEY `treino_id_user_foreign` (`id_user`),
  CONSTRAINT `treino_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `treino`
--

LOCK TABLES `treino` WRITE;
/*!40000 ALTER TABLE `treino` DISABLE KEYS */;
/*!40000 ALTER TABLE `treino` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `data_registo` date DEFAULT NULL,
  `tipo_plano` varchar(50) DEFAULT 'gratuito',
  `data_expiracao_plano` date DEFAULT NULL,
  `ddd` varchar(3) DEFAULT NULL,
  `telefone` varchar(9) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `genero` enum('Masculino','Feminino','Outro') DEFAULT NULL,
  `tipo_usuario` varchar(20) DEFAULT 'Usuario',
  `foto` varchar(255) DEFAULT NULL,
  `usercol` varchar(45) DEFAULT 'assets/fotos/default-user.png',
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `user_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Victor Santos','victorhugoribeiro2609@gmail.com','$2y$10$zmoJovE4ylINa/nnDWXQOuGkDexgPGIYWv0pptbZpTRpu91C0rhDy',NULL,'gratuito',NULL,NULL,NULL,NULL,NULL,'Usuario',NULL,'assets/img/defaultimg.png'),(3,'Victor Santos','victorribeirosantos2609@gmail.com','$2y$10$q4HIiNIK/csyy/llb5d4Gu8Q9dvXm5KOt5S3/cmTNna7PBRbN8E6G',NULL,'gratuito',NULL,NULL,NULL,NULL,NULL,'Usuario',NULL,'assets/img/defaultimg.png'),(4,'Victor Santos','geral@positivesphere.pt','$2y$10$JXoZJ1A/1UqUZH8fpYKYx.v0wN21v4ajuC5TMkyzQj2LUYBEfIO3u',NULL,'gratuito',NULL,'351','910877269','2006-09-26','Masculino','Admin','assets/fotos/1763241976_8c8dfea5d7c7f981e39c5d214a31530c.jpg','assets/img/defaultimg.png'),(5,'Victor Santos','demo@email.com','$2y$10$LRRHuQ0CLhyOV0UkZk.mcuNF7xKqsgM42Syknec2QUzZoHvf46U1W',NULL,'gratuito',NULL,'351','910877555','2015-09-26','Masculino','Usuario',NULL,'assets/fotos/default-user.png');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-20 20:48:39
