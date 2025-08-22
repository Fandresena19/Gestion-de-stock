/*
SQLyog Enterprise - MySQL GUI v8.1 
MySQL - 8.2.0 : Database - gestion_stock
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`gestion_stock` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `gestion_stock`;

/*Table structure for table `achat` */

DROP TABLE IF EXISTS `achat`;

CREATE TABLE `achat` (
  `id_achat` int NOT NULL AUTO_INCREMENT,
  `quantite_achete` int DEFAULT NULL,
  `prix_unitaire_achat` int DEFAULT NULL,
  `valeur_achat` int DEFAULT NULL,
  `date_achat` date DEFAULT NULL,
  `id_article` int DEFAULT NULL,
  `id_fournisseur` int DEFAULT NULL,
  `facture` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_achat`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `achat` */

insert  into `achat`(id_achat,quantite_achete,prix_unitaire_achat,valeur_achat,date_achat,id_article,id_fournisseur,facture) values (2,22,NULL,NULL,'2025-04-01',10,1,'123'),(3,50,100,5000,'2025-04-03',8,1,'41'),(4,4,200,800,'2025-04-04',10,1,'123'),(5,3,1000,3000,'2025-04-24',11,1,'41'),(6,10,500,5000,'2025-04-24',8,1,'22'),(7,29,1000,29000,'2025-04-25',11,1,'222'),(8,10,100,1000,'2025-06-18',8,1,'123');

/*Table structure for table `achat_immo` */

DROP TABLE IF EXISTS `achat_immo`;

CREATE TABLE `achat_immo` (
  `id_achat_immo` int NOT NULL AUTO_INCREMENT,
  `prix_unitaire_immo` int DEFAULT NULL,
  `date_achat_immo` date DEFAULT NULL,
  `id_immo` int DEFAULT NULL,
  `nom_immo` varchar(100) DEFAULT NULL,
  `id_fournisseur` int DEFAULT NULL,
  `facture` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_achat_immo`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `achat_immo` */

insert  into `achat_immo`(id_achat_immo,prix_unitaire_immo,date_achat_immo,id_immo,nom_immo,id_fournisseur,facture) values (1,2000,'2025-04-25',1,NULL,1,'555'),(2,1000,'2025-04-25',2,NULL,1,'444'),(3,100,'2025-06-18',1,NULL,1,'555'),(4,1200,'2025-07-07',3,NULL,1,'124'),(5,5000,'2025-07-07',4,NULL,1,'124');

/*Table structure for table `article` */

DROP TABLE IF EXISTS `article`;

CREATE TABLE `article` (
  `id_article` int NOT NULL AUTO_INCREMENT,
  `nom_article` varchar(255) DEFAULT NULL,
  `description_article` text,
  `quantite_en_stock` int DEFAULT NULL,
  `id_categorie` int DEFAULT NULL,
  PRIMARY KEY (`id_article`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `article` */

insert  into `article`(id_article,nom_article,description_article,quantite_en_stock,id_categorie) values (10,'fsdq','tsy aiko ty hoe inona fa de nosoratsoratako fotsiny tany no sady soratra tsy misy dikany akory',24,1),(8,'papier A4','Ram A4',20,2),(11,'imprimante','Machine',32,1);

/*Table structure for table `categorie` */

DROP TABLE IF EXISTS `categorie`;

CREATE TABLE `categorie` (
  `id_categorie` int NOT NULL AUTO_INCREMENT,
  `nom_categorie` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `categorie` */

insert  into `categorie`(id_categorie,nom_categorie) values (1,'materiel de bureau'),(2,'mobilier');

/*Table structure for table `categorie_immo` */

DROP TABLE IF EXISTS `categorie_immo`;

CREATE TABLE `categorie_immo` (
  `id_categorie_immo` int NOT NULL AUTO_INCREMENT,
  `nom_categorie_immo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_categorie_immo`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `categorie_immo` */

insert  into `categorie_immo`(id_categorie_immo,nom_categorie_immo) values (1,'corporelle'),(2,'incorporelle');

/*Table structure for table `fournisseur` */

DROP TABLE IF EXISTS `fournisseur`;

CREATE TABLE `fournisseur` (
  `id_fournisseur` int NOT NULL AUTO_INCREMENT,
  `nom_fournisseur` varchar(255) DEFAULT NULL,
  `contact_fournisseur` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_fournisseur`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `fournisseur` */

insert  into `fournisseur`(id_fournisseur,nom_fournisseur,contact_fournisseur) values (1,'Razaka','razaka@gmail.com');

/*Table structure for table `immobilisation` */

DROP TABLE IF EXISTS `immobilisation`;

CREATE TABLE `immobilisation` (
  `id_immo` int NOT NULL AUTO_INCREMENT,
  `nom_immo` varchar(255) DEFAULT NULL,
  `description_immo` text,
  `type_immo` varchar(100) DEFAULT NULL,
  `debut_service` date DEFAULT NULL,
  `duree_vie` int DEFAULT NULL,
  `id_categorie_immo` int DEFAULT NULL,
  PRIMARY KEY (`id_immo`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `immobilisation` */

insert  into `immobilisation`(id_immo,nom_immo,description_immo,type_immo,debut_service,duree_vie,id_categorie_immo) values (2,'IMP epson 350','rien','Imprimante',NULL,NULL,1),(4,'asus 4550','Ordinateur portable','Ordinateur','2025-07-10',5,1);

/*Table structure for table `mouvement` */

DROP TABLE IF EXISTS `mouvement`;

CREATE TABLE `mouvement` (
  `id_mouvement` int NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT NULL,
  `quantite_mouvement` int DEFAULT NULL,
  `prix_unitaire` float DEFAULT NULL,
  `valeur` float DEFAULT NULL,
  `quantite_en_stock` int DEFAULT NULL,
  `prix_unitaire_stock` int DEFAULT NULL,
  `valeur_stock` int DEFAULT NULL,
  `raison` text,
  `id_article` int DEFAULT NULL,
  PRIMARY KEY (`id_mouvement`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `mouvement` */

/*Table structure for table `sortie` */

DROP TABLE IF EXISTS `sortie`;

CREATE TABLE `sortie` (
  `id_sortie` int NOT NULL AUTO_INCREMENT,
  `quantite_sortie` int DEFAULT NULL,
  `prix_unitaire_sortie` int DEFAULT NULL,
  `valeur_sortie` int DEFAULT NULL,
  `raison_sortie` text,
  `date_sortie` date DEFAULT NULL,
  `id_article` int DEFAULT NULL,
  PRIMARY KEY (`id_sortie`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `sortie` */

insert  into `sortie`(id_sortie,quantite_sortie,prix_unitaire_sortie,valeur_sortie,raison_sortie,date_sortie,id_article) values (10,2,400,800,'vente','2025-04-03',10),(11,20,100,2000,'Rien','2025-04-10',8),(12,30,100,3000,'sdq','2025-04-09',8);

/*Table structure for table `sortie_immo` */

DROP TABLE IF EXISTS `sortie_immo`;

CREATE TABLE `sortie_immo` (
  `id_sortie_immo` int NOT NULL AUTO_INCREMENT,
  `prix_unitaire_immo` int DEFAULT NULL,
  `raison_sortie_immo` text,
  `date_sortie_immo` date DEFAULT NULL,
  `id_immo` int DEFAULT NULL,
  `nom_immo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_sortie_immo`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `sortie_immo` */

insert  into `sortie_immo`(id_sortie_immo,prix_unitaire_immo,raison_sortie_immo,date_sortie_immo,id_immo,nom_immo) values (10,4500,'Rien','2025-07-07',3,'hp 450'),(9,15000,'Rien','2025-07-04',1,'Ordinateur');

/*Table structure for table `utilisateur` */

DROP TABLE IF EXISTS `utilisateur`;

CREATE TABLE `utilisateur` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `nom_utilisateur` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `utilisateur` */

insert  into `utilisateur`(id_utilisateur,nom_utilisateur,mot_de_passe) values (1,'fah','$2y$10$QR0BtwuM.FTWyI3HpswLPuqP8hB4jDFR0NWoi/0ps7BTgKjvmBeQi');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
