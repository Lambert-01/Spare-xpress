-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sparedb
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_activity_logs`
--

DROP TABLE IF EXISTS `admin_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1049 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_activity_logs`
--

LOCK TABLES `admin_activity_logs` WRITE;
/*!40000 ALTER TABLE `admin_activity_logs` DISABLE KEYS */;
INSERT INTO `admin_activity_logs` VALUES (1,1,'login','Admin login successful','::1',NULL,'2025-12-08 23:06:50'),(2,1,'login','Admin login successful','::1',NULL,'2025-12-09 06:17:26'),(1000,1,'login','Admin login successful','::1',NULL,'2025-12-15 08:23:34'),(1001,1,'login','Admin login successful','::1',NULL,'2025-12-16 08:25:58'),(1002,1,'login','Admin login successful','::1',NULL,'2025-12-16 10:00:14'),(1003,1,'login','Admin login successful','::1',NULL,'2025-12-19 17:58:32'),(1004,1,'login','Admin login successful','::1',NULL,'2025-12-21 08:06:24'),(1005,1,'login','Admin login successful','::1',NULL,'2025-12-22 08:38:46'),(1006,1,'login','Admin login successful','::1',NULL,'2025-12-22 20:13:01'),(1007,1,'login','Admin login successful','::1',NULL,'2025-12-23 07:27:12'),(1008,1,'login','Admin login successful','::1',NULL,'2025-12-23 14:53:39'),(1009,1,'login','Admin login successful','::1',NULL,'2025-12-23 15:34:32'),(1010,1,'login','Admin login successful','::1',NULL,'2025-12-23 16:49:28'),(1011,1,'login','Admin login successful','::1',NULL,'2025-12-24 09:11:37'),(1012,1,'login','Admin login successful','::1',NULL,'2025-12-24 11:16:09'),(1013,1,'login','Admin login successful','::1',NULL,'2025-12-24 18:14:50'),(1014,1,'login','Admin login successful','::1',NULL,'2025-12-27 18:32:44'),(1015,1,'login','Admin login successful','::1',NULL,'2025-12-28 06:44:55'),(1016,1,'login','Admin login successful','::1',NULL,'2025-12-29 18:49:04'),(1017,1,'login','Admin login successful','::1',NULL,'2025-12-29 19:17:18'),(1018,1,'login','Admin login successful','::1',NULL,'2025-12-30 15:00:34'),(1019,1,'login','Admin login successful','::1',NULL,'2025-12-30 17:04:29'),(1020,1,'login','Admin login successful','::1',NULL,'2026-01-01 16:53:12'),(1021,1,'login','Admin login successful','::1',NULL,'2026-01-02 00:10:11'),(1022,1,'login','Admin login successful','::1',NULL,'2026-01-03 09:02:39'),(1023,1,'login','Admin login successful','::1',NULL,'2026-01-03 10:32:39'),(1024,1,'login','Admin login successful','::1',NULL,'2026-01-04 22:06:52'),(1025,1,'login','Admin login successful','::1',NULL,'2026-01-05 06:48:59'),(1026,1,'login','Admin login successful','::1',NULL,'2026-01-05 08:40:43'),(1027,1,'login','Admin login successful','::1',NULL,'2026-01-05 09:18:51'),(1028,1,'login','Admin login successful','::1',NULL,'2026-01-05 09:52:23'),(1029,1,'login','Admin login successful','::1',NULL,'2026-01-05 15:46:11'),(1030,1,'login','Admin login successful','::1',NULL,'2026-01-06 12:47:00'),(1031,1,'login','Admin login successful','::1',NULL,'2026-01-06 16:21:41'),(1032,1,'login','Admin login successful','::1',NULL,'2026-01-07 06:32:09'),(1033,1,'login','Admin login successful','::1',NULL,'2026-01-07 09:45:33'),(1034,1,'login','Admin login successful','::1',NULL,'2026-01-09 14:25:22'),(1035,1,'login','Admin login successful','::1',NULL,'2026-01-12 14:33:22'),(1036,1,'login','Admin login successful','::1',NULL,'2026-01-12 15:29:30'),(1037,1,'login','Admin login successful','::1',NULL,'2026-01-16 08:50:02'),(1038,1,'login','Admin login successful','::1',NULL,'2026-02-06 09:09:16'),(1039,1,'login','Admin login successful','::1',NULL,'2026-02-06 09:09:16'),(1040,1,'login','Admin login successful','::1',NULL,'2026-02-06 13:47:14'),(1041,1,'login','Admin login successful','::1',NULL,'2026-02-23 16:42:46'),(1042,1,'login','Admin login successful','::1',NULL,'2026-02-23 23:52:38'),(1043,1,'login','Admin login successful','::1',NULL,'2026-02-28 09:32:01'),(1044,1,'login','Admin login successful','::1',NULL,'2026-03-12 08:30:26'),(1045,1,'login','Admin login successful','::1',NULL,'2026-03-12 13:40:54'),(1046,1,'login','Admin login successful','::1',NULL,'2026-05-14 11:06:10'),(1047,1,'login','Admin login successful','::1',NULL,'2026-05-17 07:57:13'),(1048,1,'login','Admin login successful','::1',NULL,'2026-05-20 11:46:28');
/*!40000 ALTER TABLE `admin_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('super_admin','manager','staff','viewer') DEFAULT 'staff',
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `avatar` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `email_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin','admin@sparexpress.rw','$2y$10$XqSTsDRepev15LkHqinmr.NkG.kEy7rYXc/VWQ2Nl2F4VHIk57WOW','System Administrator','super_admin',NULL,NULL,NULL,0,NULL,NULL,NULL,0,NULL,'active',0,'2025-12-08 23:05:16','2025-12-08 23:05:16');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','0192023a7bbd73250516f069df18b500');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `analytics_events`
--

DROP TABLE IF EXISTS `analytics_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `analytics_events` (
  `id` int(11) NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `event_category` varchar(50) DEFAULT NULL,
  `event_action` varchar(100) DEFAULT NULL,
  `event_label` varchar(255) DEFAULT NULL,
  `event_value` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Admin or customer ID',
  `session_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `page_url` varchar(500) DEFAULT NULL,
  `referrer_url` varchar(500) DEFAULT NULL,
  `additional_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`additional_data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_event_type` (`event_type`),
  KEY `idx_event_category` (`event_category`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analytics_events`
--

LOCK TABLES `analytics_events` WRITE;
/*!40000 ALTER TABLE `analytics_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `analytics_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_keys`
--

DROP TABLE IF EXISTS `api_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_keys` (
  `id` int(11) NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `key_type` enum('public','private','webhook') DEFAULT 'private',
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of allowed permissions' CHECK (json_valid(`permissions`)),
  `rate_limit` int(11) DEFAULT 1000 COMMENT 'Requests per hour',
  `is_active` tinyint(1) DEFAULT 1,
  `expires_at` timestamp NULL DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Admin user ID',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_key` (`api_key`),
  KEY `created_by` (`created_by`),
  KEY `idx_api_key` (`api_key`),
  KEY `idx_key_type` (`key_type`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_keys`
--

LOCK TABLES `api_keys` WRITE;
/*!40000 ALTER TABLE `api_keys` DISABLE KEYS */;
/*!40000 ALTER TABLE `api_keys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories_enhanced`
--

DROP TABLE IF EXISTS `categories_enhanced`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories_enhanced` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `category_image` varchar(255) DEFAULT NULL,
  `icon_class` varchar(50) DEFAULT 'bi-grid',
  `description` text DEFAULT NULL,
  `display_priority` enum('low','medium','high') DEFAULT 'medium',
  `display_order` int(11) DEFAULT 0,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_slug` (`slug`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_display_order` (`display_order`),
  CONSTRAINT `categories_enhanced_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories_enhanced` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories_enhanced`
--

LOCK TABLES `categories_enhanced` WRITE;
/*!40000 ALTER TABLE `categories_enhanced` DISABLE KEYS */;
INSERT INTO `categories_enhanced` VALUES (1,'Engine Parts','engine-parts',NULL,NULL,'bi-gear','Engine components and parts for all vehicle types','high',0,NULL,NULL,NULL,1,'2026-01-06 15:33:07','2026-01-06 15:33:07'),(2,'Brake System','brake-system',NULL,NULL,'bi-stop-circle','Brake pads, rotors, calipers and brake system components','high',0,NULL,NULL,NULL,1,'2026-01-06 15:33:07','2026-01-06 15:33:07'),(3,'Electrical System','electrical-system',NULL,NULL,'bi-lightning','Batteries, alternators, starters and electrical components','medium',0,NULL,NULL,NULL,1,'2026-01-06 15:33:07','2026-01-06 15:33:07'),(4,'Body Parts','body-parts',NULL,NULL,'bi-car-front','Body panels, bumpers, lights and exterior components','medium',0,NULL,NULL,NULL,1,'2026-01-06 15:33:07','2026-01-06 15:33:07'),(5,'Belts & Hoses','belts-hoses',NULL,NULL,'bi-link','Drive belts, hoses and cooling system components','medium',0,NULL,NULL,NULL,1,'2026-01-06 15:33:07','2026-01-06 15:33:07'),(6,'Suspension & Steering','suspension-steering',NULL,NULL,'bi-car-front-fill','Shocks, struts, control arms and steering components','medium',0,NULL,NULL,NULL,1,'2026-01-06 15:33:07','2026-01-06 15:33:07'),(7,'Transmission','transmission',NULL,NULL,'bi-gear-wide-connected','Transmission parts and components','medium',0,NULL,NULL,NULL,1,'2026-01-06 15:33:07','2026-01-06 15:33:07'),(8,'Exhaust System','exhaust-system',NULL,NULL,'bi-cloud','Mufflers, pipes and exhaust components','low',0,NULL,NULL,NULL,1,'2026-01-06 15:33:07','2026-01-06 15:33:07'),(9,'Fuel System','fuel-system',NULL,NULL,'bi-fuel-pump','Fuel pumps, injectors and fuel system parts','medium',0,NULL,NULL,NULL,1,'2026-01-06 15:33:07','2026-01-06 15:33:07'),(10,'Air Conditioning','air-conditioning',NULL,NULL,'bi-snow','AC compressors, condensers and HVAC components','low',0,NULL,NULL,NULL,1,'2026-01-06 15:33:07','2026-01-06 15:33:07'),(11,'Pistons & Rings','pistons-rings',1,NULL,'bi-circle','Pistons, piston rings and related components','medium',0,NULL,NULL,NULL,1,'2026-01-06 15:33:07','2026-01-06 15:33:07'),(12,'Valves & Timing','valves-timing',1,NULL,'bi-clock','Valves, timing belts, chains and camshafts','medium',0,NULL,NULL,NULL,1,'2026-01-06 15:33:07','2026-01-06 15:33:07'),(13,'Oil System','oil-system',1,NULL,'bi-droplet','Oil pumps, filters and lubrication components','medium',0,NULL,NULL,NULL,1,'2026-01-06 15:33:07','2026-01-06 15:33:07'),(14,'Cooling System','cooling-system',1,NULL,'bi-thermometer','Radiators, water pumps and cooling components','medium',0,NULL,NULL,NULL,1,'2026-01-06 15:33:07','2026-01-06 15:33:07');
/*!40000 ALTER TABLE `categories_enhanced` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `last_message` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_updated_at` (`updated_at`),
  CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `customers_enhanced` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversations_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversations`
--

LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
INSERT INTO `conversations` VALUES (4,2,NULL,'hello','2026-01-12 15:34:44'),(5,1,NULL,'hello','2026-02-06 13:56:45');
/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(50) NOT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_phone` (`customer_phone`),
  KEY `idx_email` (`customer_email`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers_enhanced`
--

DROP TABLE IF EXISTS `customers_enhanced`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers_enhanced` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_number` varchar(20) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone_secondary` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `address_line1` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Rwanda',
  `preferred_language` varchar(10) DEFAULT 'en',
  `marketing_emails` tinyint(1) DEFAULT 1,
  `sms_notifications` tinyint(1) DEFAULT 1,
  `whatsapp_notifications` tinyint(1) DEFAULT 1,
  `customer_status` enum('active','inactive','suspended','blacklisted') DEFAULT 'active',
  `customer_type` enum('individual','business','dealer') DEFAULT 'individual',
  `credit_limit` decimal(12,2) DEFAULT 0.00,
  `current_balance` decimal(12,2) DEFAULT 0.00,
  `total_orders` int(11) DEFAULT 0,
  `total_spent` decimal(12,2) DEFAULT 0.00,
  `average_order_value` decimal(12,2) DEFAULT 0.00,
  `last_order_date` timestamp NULL DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `phone_verified` tinyint(1) DEFAULT 0,
  `identity_verified` tinyint(1) DEFAULT 0,
  `verification_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of verification document URLs' CHECK (json_valid(`verification_documents`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_number` (`customer_number`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_customer_number` (`customer_number`),
  KEY `idx_email` (`email`),
  KEY `idx_phone` (`phone`),
  KEY `idx_customer_status` (`customer_status`),
  KEY `idx_customer_type` (`customer_type`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_last_order_date` (`last_order_date`),
  FULLTEXT KEY `idx_search` (`first_name`,`last_name`,`email`,`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers_enhanced`
--

LOCK TABLES `customers_enhanced` WRITE;
/*!40000 ALTER TABLE `customers_enhanced` DISABLE KEYS */;
INSERT INTO `customers_enhanced` VALUES (1,'CUST-20260112-5307','Lambert','NDACYAYISABA','nlambert833@gmail.com','+250790311401','$2y$10$59yxaqDv1cE70MFAHJlX7OpJprdwrf77h3Mhic/pDAlKHf1J8A1Ry',NULL,NULL,NULL,NULL,'Nyamirambo, Kigali',NULL,NULL,NULL,NULL,'Rwanda','en',1,1,1,'active','individual',0.00,0.00,0,0.00,0.00,NULL,0,0,0,NULL,'2026-01-12 15:33:17','2026-05-20 11:48:46','2026-05-20 11:48:46',NULL,NULL),(2,'CUST-20260112-6376','MUKASA',' Christian','nlambert338@gmail.com','+250792030786','$2y$10$a2c5v8ZA6E7A0SUxXOdrMOploVzNwTG9QJsKqAfSOpwFEhbkeSGEa',NULL,NULL,NULL,NULL,'kigali',NULL,NULL,NULL,NULL,'Rwanda','en',1,1,1,'active','individual',0.00,0.00,0,0.00,0.00,NULL,0,0,0,NULL,'2026-01-12 15:33:51','2026-01-12 15:34:59','2026-01-12 15:34:59',NULL,NULL);
/*!40000 ALTER TABLE `customers_enhanced` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_transactions`
--

DROP TABLE IF EXISTS `inventory_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_transactions` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `transaction_type` enum('stock_in','stock_out','adjustment','return','damage','transfer') NOT NULL,
  `quantity` int(11) NOT NULL,
  `previous_stock` int(11) DEFAULT NULL,
  `new_stock` int(11) DEFAULT NULL,
  `reference_type` enum('order','purchase','adjustment','return','transfer') DEFAULT 'adjustment',
  `reference_id` int(11) DEFAULT NULL COMMENT 'Order ID, Purchase ID, etc.',
  `notes` text DEFAULT NULL,
  `location` varchar(100) DEFAULT 'main_warehouse',
  `performed_by` int(11) DEFAULT NULL COMMENT 'Admin user ID',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `performed_by` (`performed_by`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_transaction_type` (`transaction_type`),
  KEY `idx_reference_type` (`reference_type`),
  KEY `idx_location` (`location`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_transactions`
--

LOCK TABLES `inventory_transactions` WRITE;
/*!40000 ALTER TABLE `inventory_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `sender_type` enum('admin','client') NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_conversation_id` (`conversation_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_sender_type` (`sender_type`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (10,4,'admin','hello',NULL,'2026-01-12 15:34:44'),(11,5,'admin','hello',NULL,'2026-02-06 13:56:45');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `type` enum('message','system') NOT NULL DEFAULT 'message',
  `reference_id` int(11) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_type` (`user_id`,`type`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_is_read` (`is_read`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (3,6,'system',6,1,'2026-01-05 10:10:52'),(4,1,'system',1,1,'2026-01-06 12:58:49'),(6,0,'system',0,1,'2026-01-06 16:22:22'),(8,0,'message',3,1,'2026-01-07 08:51:03'),(9,0,'message',3,1,'2026-01-07 08:55:42'),(10,1,'system',1,1,'2026-01-12 15:33:17'),(11,2,'system',2,1,'2026-01-12 15:33:51'),(12,2,'message',4,1,'2026-01-12 15:34:44'),(13,1,'message',5,1,'2026-02-06 13:56:45');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `on_demand_requests_enhanced`
--

DROP TABLE IF EXISTS `on_demand_requests_enhanced`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `on_demand_requests_enhanced` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_number` varchar(20) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `vehicle_brand` varchar(255) DEFAULT NULL,
  `vehicle_model` varchar(255) DEFAULT NULL,
  `vehicle_year` int(11) DEFAULT NULL,
  `vehicle_vin` varchar(17) DEFAULT NULL,
  `vehicle_mileage` int(11) DEFAULT NULL,
  `part_name` varchar(500) NOT NULL,
  `part_description` text DEFAULT NULL,
  `part_category` varchar(255) DEFAULT NULL,
  `part_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of part images' CHECK (json_valid(`part_images`)),
  `quantity` int(11) DEFAULT 1,
  `estimated_price_min` decimal(10,2) DEFAULT NULL,
  `estimated_price_max` decimal(10,2) DEFAULT NULL,
  `quoted_price` decimal(10,2) DEFAULT NULL,
  `final_price` decimal(10,2) DEFAULT NULL,
  `request_status` enum('pending','reviewing','sourcing','quoted','approved','ordered','received','ready','delivered','cancelled') DEFAULT 'pending',
  `priority_level` enum('low','normal','high','urgent') DEFAULT 'normal',
  `assigned_to` int(11) DEFAULT NULL COMMENT 'Admin user ID',
  `estimated_delivery_date` date DEFAULT NULL,
  `actual_delivery_date` timestamp NULL DEFAULT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `supplier_contact` varchar(255) DEFAULT NULL,
  `urgency_reason` text DEFAULT NULL,
  `alternative_parts` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `customer_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_number` (`request_number`),
  KEY `customer_id` (`customer_id`),
  KEY `request_status` (`request_status`),
  KEY `priority_level` (`priority_level`),
  KEY `assigned_to` (`assigned_to`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `on_demand_requests_enhanced`
--

LOCK TABLES `on_demand_requests_enhanced` WRITE;
/*!40000 ALTER TABLE `on_demand_requests_enhanced` DISABLE KEYS */;
/*!40000 ALTER TABLE `on_demand_requests_enhanced` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items_enhanced`
--

DROP TABLE IF EXISTS `order_items_enhanced`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items_enhanced` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(100) DEFAULT NULL,
  `product_brand` varchar(255) DEFAULT NULL,
  `product_model` varchar(255) DEFAULT NULL,
  `vehicle_year` int(11) DEFAULT NULL,
  `product_category` varchar(255) DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(12,2) NOT NULL,
  `product_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Selected product options/variants' CHECK (json_valid(`product_options`)),
  `customizations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Custom product customizations' CHECK (json_valid(`customizations`)),
  `fulfilled` tinyint(1) DEFAULT 0,
  `fulfilled_date` timestamp NULL DEFAULT NULL,
  `fulfillment_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  KEY `fulfilled` (`fulfilled`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items_enhanced`
--

LOCK TABLES `order_items_enhanced` WRITE;
/*!40000 ALTER TABLE `order_items_enhanced` DISABLE KEYS */;
INSERT INTO `order_items_enhanced` VALUES (1,1,2246,'Honda Civic AC Compressor',NULL,'Honda','Civic',2016,NULL,'/img/no-image.png',0.00,1,0.00,0.00,0.00,NULL,NULL,0,NULL,NULL,'2025-12-24 11:07:25'),(2,1,2244,'Honda Civic VTEC Solenoid',NULL,'Honda','Civic',2019,NULL,'/img/no-image.png',0.00,1,0.00,0.00,0.00,NULL,NULL,0,NULL,NULL,'2025-12-24 11:07:25'),(3,1,2249,'Honda Civic Rear Brake Pads',NULL,'Honda','Civic',2018,NULL,'/img/no-image.png',0.00,1,0.00,0.00,0.00,NULL,NULL,0,NULL,NULL,'2025-12-24 11:07:25'),(4,1,2249,'Honda Civic Rear Brake Pads',NULL,'Honda','Civic',2023,NULL,'/img/no-image.png',0.00,1,0.00,0.00,0.00,NULL,NULL,0,NULL,NULL,'2025-12-24 11:07:25'),(5,2,2246,'Honda Civic AC Compressor',NULL,'Honda','Civic',2017,NULL,'/img/no-image.png',0.00,1,0.00,0.00,0.00,NULL,NULL,0,NULL,NULL,'2025-12-24 11:10:13'),(6,2,2246,'Honda Civic AC Compressor',NULL,'Honda','Civic',2019,NULL,'/img/no-image.png',0.00,1,0.00,0.00,0.00,NULL,NULL,0,NULL,NULL,'2025-12-24 11:10:13'),(7,3,2245,'Honda Civic Rear Control Arm Bushings',NULL,'Honda','Civic',2018,NULL,'/img/no-image.png',0.00,1,0.00,0.00,0.00,NULL,NULL,0,NULL,NULL,'2025-12-24 11:11:53'),(8,4,2246,'Honda Civic AC Compressor',NULL,'Honda','Civic',2018,NULL,'/img/no-image.png',0.00,1,0.00,0.00,0.00,NULL,NULL,0,NULL,NULL,'2025-12-24 11:14:13'),(9,4,2246,'Honda Civic AC Compressor',NULL,'Honda','Civic',2023,NULL,'/img/no-image.png',0.00,1,0.00,0.00,0.00,NULL,NULL,0,NULL,NULL,'2025-12-24 11:14:13'),(10,5,2244,'Honda Civic VTEC Solenoid',NULL,'Honda','Civic',2015,NULL,'/uploads/products/honda-civic-vtec-solenoid_main.png',15000.00,1,0.00,0.00,15000.00,NULL,NULL,0,NULL,NULL,'2026-02-06 13:50:52'),(11,6,5,'MIRROR COROLLA 01-05 (3 LINE) KL-TO001',NULL,'Toyota','Corolla',2017,NULL,'/img/no-image.png',0.00,1,0.00,0.00,0.00,NULL,NULL,0,NULL,NULL,'2026-03-12 09:32:14'),(12,6,5,'MIRROR COROLLA 01-05 (3 LINE) KL-TO001',NULL,'Toyota','Corolla',2019,NULL,'/img/no-image.png',0.00,1,0.00,0.00,0.00,NULL,NULL,0,NULL,NULL,'2026-03-12 09:32:14'),(13,7,5,'MIRROR COROLLA 01-05 (3 LINE) KL-TO001',NULL,'Toyota','Corolla',2017,NULL,'/img/no-image.png',0.00,1,0.00,0.00,0.00,NULL,NULL,0,NULL,NULL,'2026-03-12 11:51:29'),(14,7,5,'MIRROR COROLLA 01-05 (3 LINE) KL-TO001',NULL,'Toyota','Corolla',2019,NULL,'/img/no-image.png',0.00,1,0.00,0.00,0.00,NULL,NULL,0,NULL,NULL,'2026-03-12 11:51:29'),(15,8,49,'Toyota Corolla Side Mirror 2001-2005 3-Line LH',NULL,'Toyota','Corolla',2022,NULL,'/img/no-image.png',75000.00,1,0.00,0.00,75000.00,NULL,NULL,0,NULL,NULL,'2026-05-17 08:46:20'),(16,8,49,'Toyota Corolla Side Mirror 2001-2005 3-Line LH',NULL,'Toyota','Corolla',2020,NULL,'/img/no-image.png',75000.00,1,0.00,0.00,75000.00,NULL,NULL,0,NULL,NULL,'2026-05-17 08:46:20');
/*!40000 ALTER TABLE `order_items_enhanced` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_notes`
--

DROP TABLE IF EXISTS `order_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `on_demand_id` int(11) DEFAULT NULL,
  `note_type` enum('internal','customer','packing','issue','refund') DEFAULT 'internal',
  `note_content` text NOT NULL,
  `is_visible_to_customer` tinyint(1) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL COMMENT 'Admin user ID',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `on_demand_id` (`on_demand_id`),
  KEY `note_type` (`note_type`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_notes`
--

LOCK TABLES `order_notes` WRITE;
/*!40000 ALTER TABLE `order_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_on_demand`
--

DROP TABLE IF EXISTS `order_on_demand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_on_demand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_number` varchar(20) NOT NULL COMMENT 'SOD-000001 format',
  `customer_id` int(11) DEFAULT NULL,
  `vehicle_brand` varchar(255) DEFAULT NULL,
  `vehicle_model` varchar(255) DEFAULT NULL,
  `vehicle_year` int(11) DEFAULT NULL,
  `part_name` varchar(500) NOT NULL,
  `part_description` text DEFAULT NULL,
  `part_image` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `estimated_price_min` decimal(10,2) DEFAULT NULL,
  `estimated_price_max` decimal(10,2) DEFAULT NULL,
  `quoted_price` decimal(10,2) DEFAULT NULL,
  `request_status` enum('pending','sourcing','quoted','waiting_approval','ordered','shipped','delivered','cancelled') DEFAULT 'pending',
  `delivery_date` date DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_number` (`request_number`),
  KEY `customer_id` (`customer_id`),
  KEY `request_status` (`request_status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_on_demand`
--

LOCK TABLES `order_on_demand` WRITE;
/*!40000 ALTER TABLE `order_on_demand` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_on_demand` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_requests`
--

DROP TABLE IF EXISTS `order_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vehicle_brand` varchar(255) NOT NULL,
  `vehicle_model` varchar(255) NOT NULL,
  `year` int(4) DEFAULT NULL,
  `chassis_number` varchar(255) DEFAULT NULL,
  `vehicle_plate` varchar(50) DEFAULT NULL,
  `part_name` varchar(255) NOT NULL,
  `part_category` varchar(255) NOT NULL,
  `part_description` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of uploaded image filenames' CHECK (json_valid(`images`)),
  `customer_name` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `province_district` varchar(100) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `order_type` enum('normal','urgent') NOT NULL DEFAULT 'normal',
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `estimated_cost` decimal(10,2) DEFAULT NULL,
  `actual_cost` decimal(10,2) DEFAULT NULL,
  `deposit_amount` decimal(10,2) DEFAULT NULL,
  `deposit_status` enum('pending','paid','refunded') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_order_type` (`order_type`),
  KEY `idx_customer_email` (`email`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_requests`
--

LOCK TABLES `order_requests` WRITE;
/*!40000 ALTER TABLE `order_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_timeline`
--

DROP TABLE IF EXISTS `order_timeline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_timeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `user_id` int(11) DEFAULT NULL,
  `user_type` enum('customer','admin') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_timeline`
--

LOCK TABLES `order_timeline` WRITE;
/*!40000 ALTER TABLE `order_timeline` DISABLE KEYS */;
INSERT INTO `order_timeline` VALUES (1,6,'processing','{\"description\":\"Order status changed to Processing\",\"tracking_number\":null,\"carrier_name\":null}',1,'admin','2026-03-12 10:33:32'),(2,6,'delivered','{\"description\":\"Order status changed to Delivered\",\"tracking_number\":null,\"carrier_name\":null}',1,'admin','2026-03-12 10:53:56'),(3,6,'cancelled','{\"description\":\"Order updated by admin\",\"tracking_number\":null,\"carrier_name\":null}',1,'admin','2026-03-12 11:13:14'),(4,7,'confirmed','{\"description\":\"Order updated by admin\",\"tracking_number\":null,\"carrier_name\":null}',1,'admin','2026-03-12 14:34:50');
/*!40000 ALTER TABLE `order_timeline` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_tracking`
--

DROP TABLE IF EXISTS `order_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `status` enum('pending','processing','ready','shipped','delivered','cancelled','failed') NOT NULL,
  `status_description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `tracking_number` varchar(255) DEFAULT NULL,
  `courier_name` varchar(255) DEFAULT NULL,
  `estimated_delivery` date DEFAULT NULL,
  `actual_delivery` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Admin user ID',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_tracking`
--

LOCK TABLES `order_tracking` WRITE;
/*!40000 ALTER TABLE `order_tracking` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_tracking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(20) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customer_info`)),
  `vehicle_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vehicle_info`)),
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`items`)),
  `pricing` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pricing`)),
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
  `priority` enum('low','normal','high','urgent') DEFAULT 'normal',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_reference` varchar(255) DEFAULT NULL,
  `shipping_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`shipping_info`)),
  `tracking_number` varchar(100) DEFAULT NULL,
  `shipping_carrier` varchar(100) DEFAULT NULL,
  `estimated_delivery` date DEFAULT NULL,
  `actual_delivery` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'RWF',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `idx_order_number` (`order_number`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_status` (`status`),
  KEY `idx_priority` (`priority`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_estimated_delivery` (`estimated_delivery`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders_enhanced`
--

DROP TABLE IF EXISTS `orders_enhanced`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders_enhanced` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(20) NOT NULL COMMENT 'SPX-000001 format',
  `customer_id` int(11) DEFAULT NULL,
  `order_type` enum('stock','on_demand','emergency','bulk') DEFAULT 'stock',
  `order_status` enum('pending','confirmed','processing','ready','packed','shipped','out_for_delivery','delivered','cancelled','refunded','failed') DEFAULT 'pending',
  `payment_status` enum('unpaid','partial','paid','refunded','failed') DEFAULT 'unpaid',
  `payment_method` enum('cash','momo','bank','card','paypal') DEFAULT 'cash',
  `transaction_id` varchar(255) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(12,2) DEFAULT 0.00,
  `shipping_address` text DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_sector` varchar(100) DEFAULT NULL,
  `shipping_carrier` varchar(255) DEFAULT NULL,
  `tracking_number` varchar(255) DEFAULT NULL,
  `shipping_method` enum('standard','express','pickup') DEFAULT 'standard',
  `delivery_notes` text DEFAULT NULL,
  `special_instructions` text DEFAULT NULL,
  `priority_level` enum('low','normal','high','urgent') DEFAULT 'normal',
  `status_updated_at` timestamp NULL DEFAULT NULL,
  `status_updated_by` int(11) DEFAULT NULL COMMENT 'Admin user ID',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `customer_id` (`customer_id`),
  KEY `order_status` (`order_status`),
  KEY `payment_status` (`payment_status`),
  KEY `priority_level` (`priority_level`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders_enhanced`
--

LOCK TABLES `orders_enhanced` WRITE;
/*!40000 ALTER TABLE `orders_enhanced` DISABLE KEYS */;
INSERT INTO `orders_enhanced` VALUES (5,'SPX-20260206-2345',1,'stock','pending','unpaid','momo',NULL,15000.00,0.00,5000.00,0.00,20000.00,'Nyamirambo, Kigali\r\ncoder, kigali, Kigali Province','kigali',NULL,NULL,NULL,'standard','dsffdhffhghg','','normal',NULL,NULL,'2026-02-06 13:50:52','2026-02-06 13:50:52'),(6,'SPX-20260312-3256',1,'stock','cancelled','refunded','momo',NULL,0.00,0.00,5000.00,0.00,5000.00,'Nyamirambo, Kigali\r\nKN 7 Ave, Kigali, kigali, Kigali Province','kigali',NULL,NULL,NULL,'standard','first orders','','normal','2026-03-12 11:13:14',1,'2026-03-12 09:32:14','2026-03-12 11:13:14'),(7,'SPX-20260312-6040',1,'stock','confirmed','paid','momo',NULL,0.00,0.00,5000.00,0.00,5000.00,'Nyamirambo, Kigali\r\nKN 7 Ave, Kigali, kigali, Kigali Province','kigali',NULL,NULL,NULL,'standard','first orders','','normal','2026-03-12 14:34:50',1,'2026-03-12 11:51:28','2026-03-12 14:34:50'),(8,'SPX-20260517-7639',1,'stock','pending','unpaid','momo',NULL,150000.00,0.00,5000.00,0.00,155000.00,'Nyamirambo, Kigali\r\ncoder, kigali, Kigali Province','kigali',NULL,NULL,NULL,'standard','ewwwwwwwwwwww','','normal',NULL,NULL,'2026-05-17 08:46:20','2026-05-17 08:46:20');
/*!40000 ALTER TABLE `orders_enhanced` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `on_demand_id` int(11) DEFAULT NULL,
  `payment_method` enum('cash','momo','bank','card','paypal') NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `payment_notes` text DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL COMMENT 'Admin user ID',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `on_demand_id` (`on_demand_id`),
  KEY `payment_status` (`payment_status`),
  KEY `payment_date` (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `price_requests`
--

DROP TABLE IF EXISTS `price_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `price_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `customer_name` varchar(255) NOT NULL,
  `phone_number` varchar(32) NOT NULL,
  `car_model` varchar(255) NOT NULL,
  `status` enum('pending','quoted','approved','deposit_paid','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `quoted_price` decimal(12,2) DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'RWF',
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_customer_id` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price_requests`
--

LOCK TABLES `price_requests` WRITE;
/*!40000 ALTER TABLE `price_requests` DISABLE KEYS */;
INSERT INTO `price_requests` VALUES (1,1,0,'brake pads  fronts  seats',2,'Nicole Kalisa Umutoni','+250728196767','toyota corolaa','pending',NULL,'RWF',NULL,'2026-03-12 14:43:14',NULL),(2,NULL,0,'brake pads  fronts',133,'mukasa christian','+250790311401','toyota corolaa','pending',NULL,'RWF',NULL,'2026-03-12 15:40:38',NULL),(3,NULL,47,'Toyota Corolla Side Mirror 2005-2007 5-Line Lamp Black LH',1,'Lambert NDACYAYISABA','+250790311401','Corolla','pending',NULL,'RWF',NULL,'2026-05-17 10:09:46',NULL),(4,NULL,48,'Toyota Corolla Side Mirror 2005-2007 5-Line Lamp Black RH',1,'Lambert NDACYAYISABA','+250790311401','Corolla','pending',NULL,'RWF',NULL,'2026-05-20 13:47:50',NULL);
/*!40000 ALTER TABLE `price_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_tag_relations`
--

DROP TABLE IF EXISTS `product_tag_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_tag_relations` (
  `product_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_tag_relations`
--

LOCK TABLES `product_tag_relations` WRITE;
/*!40000 ALTER TABLE `product_tag_relations` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_tag_relations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_tags`
--

DROP TABLE IF EXISTS `product_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_tags` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `tag_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_tags`
--

LOCK TABLES `product_tags` WRITE;
/*!40000 ALTER TABLE `product_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `model_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `regular_price` decimal(10,2) DEFAULT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `wholesale_price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `stock_status` enum('in_stock','low_stock','out_of_stock') DEFAULT 'out_of_stock',
  `low_stock_threshold` int(11) DEFAULT 5,
  `manage_stock` tinyint(1) DEFAULT 1,
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dimensions`)),
  `warranty_period` int(11) DEFAULT NULL,
  `condition` enum('new','used','refurbished') DEFAULT 'new',
  `documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`documents`)),
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `seo_keywords` text DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `view_count` int(11) DEFAULT 0,
  `sales_count` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
  `status` enum('draft','pending','published','archived') DEFAULT 'draft',
  `visibility` enum('public','private','password_protected') DEFAULT 'public',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_sku` (`sku`),
  KEY `idx_slug` (`slug`),
  KEY `idx_brand` (`brand_id`),
  KEY `idx_model` (`model_id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_status` (`status`),
  KEY `idx_featured` (`featured`),
  KEY `idx_stock_status` (`stock_status`),
  KEY `idx_published_at` (`published_at`),
  FULLTEXT KEY `idx_search` (`name`,`description`,`seo_keywords`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `vehicle_brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`model_id`) REFERENCES `vehicle_models` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products_enhanced`
--

DROP TABLE IF EXISTS `products_enhanced`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products_enhanced` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL,
  `model_id` int(11) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `gallery_images` text DEFAULT NULL COMMENT 'JSON array of image paths',
  `price` decimal(10,2) NOT NULL,
  `regular_price` decimal(10,2) DEFAULT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `wholesale_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `low_stock_threshold` int(11) DEFAULT 5,
  `manage_stock` tinyint(1) DEFAULT 1,
  `backorders_allowed` tinyint(1) DEFAULT 0,
  `price_request_only` tinyint(1) NOT NULL DEFAULT 0,
  `product_condition` enum('new','used','refurbished') DEFAULT 'new',
  `visibility` enum('public','private','password_protected') DEFAULT 'public',
  `warranty_period` varchar(255) DEFAULT NULL,
  `warranty_type` enum('manufacturer','dealer','none') DEFAULT 'none',
  `warranty_details` text DEFAULT NULL,
  `compatible_models` text DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `specifications` text DEFAULT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `sales_count` int(11) DEFAULT 0,
  `view_count` int(11) DEFAULT 0,
  `stock_status` enum('in_stock','out_of_stock') DEFAULT 'in_stock',
  `condition` enum('new','used') DEFAULT 'new',
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `sku` (`sku`),
  KEY `idx_brand_id` (`brand_id`),
  KEY `idx_model_id` (`model_id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_stock_status` (`stock_status`),
  KEY `idx_is_featured` (`is_featured`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_products_price_request_only` (`price_request_only`),
  FULLTEXT KEY `idx_search` (`product_name`,`description`,`short_description`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products_enhanced`
--

LOCK TABLES `products_enhanced` WRITE;
/*!40000 ALTER TABLE `products_enhanced` DISABLE KEYS */;
INSERT INTO `products_enhanced` VALUES (1,1,3,6,'REAR LAMP REVO (S AFRICA TYPE WHITE)',NULL,'TY-RVR16W-L','REAR LAMP REVO (S AFRICA TYPE WHITE)',NULL,NULL,NULL,2700.00,NULL,NULL,NULL,2,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(2,1,3,6,'REAR LAMP REVO (S AFRICA TYPE WHITE)',NULL,'TY-RVR16W-R','REAR LAMP REVO (S AFRICA TYPE WHITE)',NULL,NULL,NULL,2700.00,NULL,NULL,NULL,20,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(3,1,1,6,'MIRROR COROLLA 2005-07 (5 LINE LAMP BLK ELC)',NULL,'AS-2632-504L','MIRROR COROLLA 2005-07 (5 LINE LAMP BLK ELC)',NULL,NULL,NULL,20250.00,NULL,NULL,NULL,3,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(4,1,1,6,'MIRROR COROLLA 2005-07 (LAMP 5 LINE BLK ELC)',NULL,'AS-2632-504R','MIRROR COROLLA 2005-07 (LAMP 5 LINE BLK ELC)',NULL,NULL,NULL,20250.00,NULL,NULL,NULL,3,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(5,1,1,6,'MIRROR COROLLA 01-05 (3 LINE) KL-TO001',NULL,'AS-2632-503L','MIRROR COROLLA 01-05 (3 LINE) KL-TO001',NULL,NULL,NULL,14040.00,NULL,NULL,NULL,1,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-03-12 11:51:29'),(6,1,1,6,'MIRROR COROLLA 01-05 (3 LINE) KL-TO001',NULL,'AS-2632-503R','MIRROR COROLLA 01-05 (3 LINE) KL-TO001',NULL,NULL,NULL,14040.00,NULL,NULL,NULL,5,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(7,1,1,6,'MIRROR COROLLA 08-10 LED 5 LINE (TY-12D00)',NULL,'AS-2632-501L','MIRROR COROLLA 08-10 LED 5 LINE (TY-12D00)',NULL,NULL,NULL,21600.00,NULL,NULL,NULL,2,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(8,1,1,6,'MIRROR COROLLA 08-10 LED 5 LINE',NULL,'AS-2632-501R','MIRROR COROLLA 08-10 LED 5 LINE',NULL,NULL,NULL,21600.00,NULL,NULL,NULL,2,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(9,1,1,6,'MIRROR COROLLA 2008- (3 LINE BLK)',NULL,'AS-2632-502L','MIRROR COROLLA 2008- (3 LINE BLK)',NULL,NULL,NULL,17550.00,NULL,NULL,NULL,2,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(10,1,1,6,'MIRROR COROLLA 2008- (3 LINE BLK)',NULL,'AS-2632-502R','MIRROR COROLLA 2008- (3 LINE BLK)',NULL,NULL,NULL,17550.00,NULL,NULL,NULL,2,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(11,1,1,4,'Toyota Corolla Rear Lamp 2008-2009 LH','toyota-corolla-rear-lamp-2008-2009-c212-19q3-l','C212-19Q3-L','Toyota Corolla Rear Lamp 2008-2009 LH. PDF car model: Corolla. Part number: C212-19Q3-L. Wholesale price: 38,000 Rwf. Retail price: 55,000 Rwf.','Corolla C212-19Q3-L',NULL,NULL,55000.00,55000.00,NULL,38000.00,5,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"C212-19Q3-L\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":38000,\"retail_price_rwf\":55000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(12,1,1,4,'Toyota Corolla Rear Lamp 2008-2009 RH','toyota-corolla-rear-lamp-2008-2009-c212-19q3-r','C212-19Q3-R','Toyota Corolla Rear Lamp 2008-2009 RH. PDF car model: Corolla. Part number: C212-19Q3-R. Wholesale price: 38,000 Rwf. Retail price: 55,000 Rwf.','Corolla C212-19Q3-R',NULL,NULL,55000.00,55000.00,NULL,38000.00,5,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"C212-19Q3-R\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":38000,\"retail_price_rwf\":55000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(13,1,1,4,'Toyota Corolla Fog Lamp 2002 LH','toyota-corolla-fog-lamp-2002-c212-2022-l','C212-2022-L','Toyota Corolla Fog Lamp 2002 LH. PDF car model: Corolla. Part number: C212-2022-L. Wholesale price: 20,000 Rwf. Retail price: 28,000 Rwf.','Corolla C212-2022-L',NULL,NULL,28000.00,28000.00,NULL,20000.00,2,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"C212-2022-L\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":20000,\"retail_price_rwf\":28000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(14,1,1,4,'Toyota Corolla Fog Lamp 2002 RH','toyota-corolla-fog-lamp-2002-c212-2022-r','C212-2022-R','Toyota Corolla Fog Lamp 2002 RH. PDF car model: Corolla. Part number: C212-2022-R. Wholesale price: 20,000 Rwf. Retail price: 28,000 Rwf.','Corolla C212-2022-R',NULL,NULL,28000.00,28000.00,NULL,20000.00,2,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"C212-2022-R\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":20000,\"retail_price_rwf\":28000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(15,1,18,4,'Toyota Yaris Rear Lamp 2006-2007 LH','toyota-yaris-rear-lamp-2006-2007-19-3113-l','19-3113-L','Toyota Yaris Rear Lamp 2006-2007 LH. PDF car model: Yaris. Part number: 19-3113-L. Wholesale price: 52,000 Rwf. Retail price: 72,000 Rwf.','Yaris 19-3113-L',NULL,NULL,72000.00,72000.00,NULL,52000.00,4,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Yaris\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"19-3113-L\",\"car_model\":\"Yaris\",\"wholesale_price_rwf\":52000,\"retail_price_rwf\":72000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(16,1,18,4,'Toyota Yaris Rear Lamp 2006-2007 RH','toyota-yaris-rear-lamp-2006-2007-19-3113-r','19-3113-R','Toyota Yaris Rear Lamp 2006-2007 RH. PDF car model: Yaris. Part number: 19-3113-R. Wholesale price: 52,000 Rwf. Retail price: 72,000 Rwf.','Yaris 19-3113-R',NULL,NULL,72000.00,72000.00,NULL,52000.00,4,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Yaris\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"19-3113-R\",\"car_model\":\"Yaris\",\"wholesale_price_rwf\":52000,\"retail_price_rwf\":72000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(17,1,1,6,'FOG LAMP COROLLA 2003-04 ALTIS/CAMRY 04',NULL,'TY4027D','FOG LAMP COROLLA 2003-04 ALTIS/CAMRY 04',NULL,NULL,NULL,22410.00,NULL,NULL,NULL,14,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(18,1,1,4,'Toyota Corolla RunX Head Lamp 2001 LH','toyota-corolla-runx-head-lamp-2001-c212-11d1-l','C212-11D1-L','Toyota Corolla RunX Head Lamp 2001 LH. PDF car model: Corolla. Part number: C212-11D1-L. Wholesale price: 100,000 Rwf. Retail price: 135,000 Rwf.','Corolla C212-11D1-L',NULL,NULL,135000.00,135000.00,NULL,100000.00,2,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla RunX\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"C212-11D1-L\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":100000,\"retail_price_rwf\":135000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(19,1,1,4,'Toyota Corolla RunX Head Lamp 2001 RH','toyota-corolla-runx-head-lamp-2001-c212-11d1-r','C212-11D1-R','Toyota Corolla RunX Head Lamp 2001 RH. PDF car model: Corolla. Part number: C212-11D1-R. Wholesale price: 100,000 Rwf. Retail price: 135,000 Rwf.','Corolla C212-11D1-R',NULL,NULL,135000.00,135000.00,NULL,100000.00,2,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla RunX\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"C212-11D1-R\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":100000,\"retail_price_rwf\":135000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(20,1,2,4,'Toyota Camry Rear Lamp 2012 USA Type LH','toyota-camry-rear-lamp-2012-usa-type-at-89002-l','AT-89002-L','Toyota Camry Rear Lamp 2012 USA Type LH. PDF car model: Camry. Part number: AT-89002-L. Wholesale price: 68,000 Rwf. Retail price: 92,000 Rwf.','Camry AT-89002-L',NULL,NULL,92000.00,92000.00,NULL,68000.00,2,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Camry\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AT-89002-L\",\"car_model\":\"Camry\",\"wholesale_price_rwf\":68000,\"retail_price_rwf\":92000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(21,1,2,4,'Toyota Camry Rear Lamp 2012 USA Type RH','toyota-camry-rear-lamp-2012-usa-type-at-89002-r','AT-89002-R','Toyota Camry Rear Lamp 2012 USA Type RH. PDF car model: Camry. Part number: AT-89002-R. Wholesale price: 70,000 Rwf. Retail price: 95,000 Rwf.','Camry AT-89002-R',NULL,NULL,95000.00,95000.00,NULL,70000.00,2,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Camry\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AT-89002-R\",\"car_model\":\"Camry\",\"wholesale_price_rwf\":70000,\"retail_price_rwf\":95000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(22,1,2,4,'Toyota Camry Head Lamp 2012 USA White LH','toyota-camry-head-lamp-2012-usa-white-at-89001b-l','AT-89001B-L','Toyota Camry Head Lamp 2012 USA White LH. PDF car model: Camry. Part number: AT-89001B-L. Wholesale price: 120,000 Rwf. Retail price: 160,000 Rwf.','Camry AT-89001B-L',NULL,NULL,160000.00,160000.00,NULL,120000.00,1,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Camry\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AT-89001B-L\",\"car_model\":\"Camry\",\"wholesale_price_rwf\":120000,\"retail_price_rwf\":160000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(23,1,2,4,'Toyota Camry Head Lamp 2012 USA White RH','toyota-camry-head-lamp-2012-usa-white-at-89001b-r','AT-89001B-R','Toyota Camry Head Lamp 2012 USA White RH. PDF car model: Camry. Part number: AT-89001B-R. Wholesale price: 120,000 Rwf. Retail price: 160,000 Rwf.','Camry AT-89001B-R',NULL,NULL,160000.00,160000.00,NULL,120000.00,1,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Camry\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AT-89001B-R\",\"car_model\":\"Camry\",\"wholesale_price_rwf\":120000,\"retail_price_rwf\":160000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(24,1,2,6,'H LAMP CAMRY 2010 USA T (312-11B5) CHM',NULL,'AT-22669-11L','H LAMP CAMRY 2010 USA T (312-11B5) CHM',NULL,NULL,NULL,31050.00,NULL,NULL,NULL,1,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(25,1,2,6,'H LAMP CAMRY 2010 USA T (312-11B5) CHM',NULL,'AT-22669-11R','H LAMP CAMRY 2010 USA T (312-11B5) CHM',NULL,NULL,NULL,31050.00,NULL,NULL,NULL,1,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(26,1,225,4,'Toyota Avensis Head Lamp 1998-2000 LH','toyota-avensis-head-lamp-1998-2000-c212-1187-l','C212-1187-L','Toyota Avensis Head Lamp 1998-2000 LH. PDF car model: Avensis. Part number: C212-1187-L. Wholesale price: 60,000 Rwf. Retail price: 82,000 Rwf.','Avensis C212-1187-L',NULL,NULL,82000.00,82000.00,NULL,60000.00,5,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Avensis\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"C212-1187-L\",\"car_model\":\"Avensis\",\"wholesale_price_rwf\":60000,\"retail_price_rwf\":82000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(27,1,225,4,'Toyota Avensis Head Lamp 1998-2000 RH','toyota-avensis-head-lamp-1998-2000-c212-1187-r','C212-1187-R','Toyota Avensis Head Lamp 1998-2000 RH. PDF car model: Avensis. Part number: C212-1187-R. Wholesale price: 60,000 Rwf. Retail price: 82,000 Rwf.','Avensis C212-1187-R',NULL,NULL,82000.00,82000.00,NULL,60000.00,5,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Avensis\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"C212-1187-R\",\"car_model\":\"Avensis\",\"wholesale_price_rwf\":60000,\"retail_price_rwf\":82000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(28,1,225,4,'Toyota Avensis Corolla Corner Lamp 1998 LH','toyota-avensis-corolla-corner-lamp-1998-c212-15c7-l','C212-15C7-L','Toyota Avensis Corolla Corner Lamp 1998 LH. PDF car model: Avensis/Corolla. Part number: C212-15C7-L. Wholesale price: 12,000 Rwf. Retail price: 17,000 Rwf.','Avensis/Corolla C212-15C7-L',NULL,NULL,17000.00,17000.00,NULL,12000.00,10,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Avensis\",\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"C212-15C7-L\",\"car_model\":\"Avensis/Corolla\",\"wholesale_price_rwf\":12000,\"retail_price_rwf\":17000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(29,1,225,4,'Toyota Avensis Corolla Corner Lamp 1998 RH','toyota-avensis-corolla-corner-lamp-1998-c212-15c7-r','C212-15C7-R','Toyota Avensis Corolla Corner Lamp 1998 RH. PDF car model: Avensis/Corolla. Part number: C212-15C7-R. Wholesale price: 12,000 Rwf. Retail price: 17,000 Rwf.','Avensis/Corolla C212-15C7-R',NULL,NULL,17000.00,17000.00,NULL,12000.00,10,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Avensis\",\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"C212-15C7-R\",\"car_model\":\"Avensis/Corolla\",\"wholesale_price_rwf\":12000,\"retail_price_rwf\":17000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(30,1,1,4,'Toyota Corolla Corner Lamp 1998-2000 USA LH','toyota-corolla-corner-lamp-1998-2000-usa-c312-1533l-as','C312-1533L-AS','Toyota Corolla Corner Lamp 1998-2000 USA LH. PDF car model: Corolla. Part number: C312-1533L-AS. Wholesale price: 16,500 Rwf. Retail price: 23,000 Rwf.','Corolla C312-1533L-AS',NULL,NULL,23000.00,23000.00,NULL,16500.00,10,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"C312-1533L-AS\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":16500,\"retail_price_rwf\":23000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(31,1,1,4,'Toyota Corolla Corner Lamp 1998-2000 USA RH','toyota-corolla-corner-lamp-1998-2000-usa-c312-1533r-as','C312-1533R-AS','Toyota Corolla Corner Lamp 1998-2000 USA RH. PDF car model: Corolla. Part number: C312-1533R-AS. Wholesale price: 16,500 Rwf. Retail price: 23,000 Rwf.','Corolla C312-1533R-AS',NULL,NULL,23000.00,23000.00,NULL,16500.00,10,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"C312-1533R-AS\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":16500,\"retail_price_rwf\":23000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(32,1,1,4,'Toyota Corolla Corner Lamp 1992 AE100 LH','toyota-corolla-corner-lamp-1992-ae100-c212-1575-l','C212-1575-L','Toyota Corolla Corner Lamp 1992 AE100 LH. PDF car model: Corolla. Part number: C212-1575-L. Wholesale price: 11,500 Rwf. Retail price: 16,000 Rwf.','Corolla C212-1575-L',NULL,NULL,16000.00,16000.00,NULL,11500.00,10,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla AE100\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"C212-1575-L\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":11500,\"retail_price_rwf\":16000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(33,1,1,4,'Toyota Corolla Corner Lamp 1992 AE100 RH','toyota-corolla-corner-lamp-1992-ae100-c212-1575-r','C212-1575-R','Toyota Corolla Corner Lamp 1992 AE100 RH. PDF car model: Corolla. Part number: C212-1575-R. Wholesale price: 11,500 Rwf. Retail price: 16,000 Rwf.','Corolla C212-1575-R',NULL,NULL,16000.00,16000.00,NULL,11500.00,10,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla AE100\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"C212-1575-R\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":11500,\"retail_price_rwf\":16000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(34,1,1,4,'Toyota Corolla Side Mirror 2014 7-Line Folding LH','toyota-corolla-side-mirror-2014-7-line-folding-as-2632-508-l','AS-2632-508-L','Toyota Corolla Side Mirror 2014 7-Line Folding LH. PDF car model: Corolla. Part number: AS-2632-508-L. Wholesale price: 120,000 Rwf. Retail price: 160,000 Rwf.','Corolla AS-2632-508-L',NULL,NULL,160000.00,160000.00,NULL,120000.00,1,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AS-2632-508-L\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":120000,\"retail_price_rwf\":160000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(35,1,1,4,'Toyota Corolla Side Mirror 2014 7-Line Folding RH','toyota-corolla-side-mirror-2014-7-line-folding-as-2632-508-r','AS-2632-508-R','Toyota Corolla Side Mirror 2014 7-Line Folding RH. PDF car model: Corolla. Part number: AS-2632-508-R. Wholesale price: 120,000 Rwf. Retail price: 160,000 Rwf.','Corolla AS-2632-508-R',NULL,NULL,160000.00,160000.00,NULL,120000.00,1,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AS-2632-508-R\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":120000,\"retail_price_rwf\":160000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(36,1,5,6,'MIRROR RAV4 2014 LED 5 LINE YT-7737',NULL,'AS-2632-320L','MIRROR RAV4 2014 LED 5 LINE YT-7737',NULL,NULL,NULL,29700.00,NULL,NULL,NULL,1,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(37,1,5,6,'MIRROR RAV4 2014 LED 5 LINE YT-7737',NULL,'AS-2632-320R','MIRROR RAV4 2014 LED 5 LINE YT-7737',NULL,NULL,NULL,29700.00,NULL,NULL,NULL,1,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(38,1,1,6,'R LAMP RX330 2004 (312-1947)',NULL,'AT-2211-121L','R LAMP RX330 2004 (312-1947)',NULL,NULL,NULL,22950.00,NULL,NULL,NULL,1,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(39,1,1,6,'R LAMP RX330 2004 (312-1947)',NULL,'AT-2211-121R','R LAMP RX330 2004 (312-1947)',NULL,NULL,NULL,22950.00,NULL,NULL,NULL,1,5,1,0,0,'new','public',NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-02-28 11:20:07'),(40,1,14,4,'Toyota RAV4 Tailgate Gas Spring 2013-2015 LH','toyota-rav4-tailgate-gas-spring-2013-2015-68960-0r010-th','68960-0R010-TH','Toyota RAV4 Tailgate Gas Spring 2013-2015 LH. PDF car model: RAV4. Part number: 68960-0R010-TH. Wholesale price: 27,000 Rwf. Retail price: 40,000 Rwf.','RAV4 68960-0R010-TH',NULL,NULL,40000.00,40000.00,NULL,27000.00,5,2,1,0,0,'new','public',NULL,'none',NULL,'[\"RAV4\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"68960-0R010-TH\",\"car_model\":\"RAV4\",\"wholesale_price_rwf\":27000,\"retail_price_rwf\":40000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(41,1,14,4,'Toyota RAV4 Tailgate Gas Spring 2013-2015 RH','toyota-rav4-tailgate-gas-spring-2013-2015-68950-0r010-th','68950-0R010-TH','Toyota RAV4 Tailgate Gas Spring 2013-2015 RH. PDF car model: RAV4. Part number: 68950-0R010-TH. Wholesale price: 27,000 Rwf. Retail price: 40,000 Rwf.','RAV4 68950-0R010-TH',NULL,NULL,40000.00,40000.00,NULL,27000.00,5,2,1,0,0,'new','public',NULL,'none',NULL,'[\"RAV4\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"68950-0R010-TH\",\"car_model\":\"RAV4\",\"wholesale_price_rwf\":27000,\"retail_price_rwf\":40000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(42,8,106,4,'Hyundai H1 Tailgate Gas Spring 2012-2018 LH','hyundai-h1-tailgate-gas-spring-2012-2018-81770-4h030-gk','81770-4H030-GK','Hyundai H1 Tailgate Gas Spring 2012-2018 LH. PDF car model: Hyundai H1. Part number: 81770-4H030-GK. Wholesale price: 27,000 Rwf. Retail price: 40,000 Rwf.','Hyundai H1 81770-4H030-GK',NULL,NULL,40000.00,40000.00,NULL,27000.00,5,2,1,0,0,'new','public',NULL,'none',NULL,'[\"H1\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"81770-4H030-GK\",\"car_model\":\"Hyundai H1\",\"wholesale_price_rwf\":27000,\"retail_price_rwf\":40000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(43,8,106,4,'Hyundai H1 Tailgate Gas Spring 2012-2018 RH','hyundai-h1-tailgate-gas-spring-2012-2018-81780-4h030-gk','81780-4H030-GK','Hyundai H1 Tailgate Gas Spring 2012-2018 RH. PDF car model: Hyundai H1. Part number: 81780-4H030-GK. Wholesale price: 27,000 Rwf. Retail price: 40,000 Rwf.','Hyundai H1 81780-4H030-GK',NULL,NULL,40000.00,40000.00,NULL,27000.00,5,2,1,0,0,'new','public',NULL,'none',NULL,'[\"H1\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"81780-4H030-GK\",\"car_model\":\"Hyundai H1\",\"wholesale_price_rwf\":27000,\"retail_price_rwf\":40000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(44,1,1,4,'Toyota Corolla Fog Lamp 2001-2003 Set CASP','toyota-corolla-fog-lamp-2001-2003-set-casp-p-212-2022-rl','P-212-2022-R/L','Toyota Corolla Fog Lamp 2001-2003 Set CASP. PDF car model: Corolla. Part number: P-212-2022-R/L. Wholesale price: 78,000 Rwf. Retail price: 105,000 Rwf.','Corolla P-212-2022-R/L',NULL,NULL,105000.00,105000.00,NULL,78000.00,3,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"P-212-2022-R/L\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":78000,\"retail_price_rwf\":105000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(45,1,16,4,'Toyota Hiace Tail Lamp 2005-2013 Noble C10 LH','toyota-hiace-tail-lamp-2005-2013-noble-c10-p-212-19k2l-cn','P-212-19K2L-CN','Toyota Hiace Tail Lamp 2005-2013 Noble C10 LH. PDF car model: Hiace. Part number: P-212-19K2L-CN. Wholesale price: 36,000 Rwf. Retail price: 52,000 Rwf.','Hiace P-212-19K2L-CN',NULL,NULL,52000.00,52000.00,NULL,36000.00,3,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Hiace\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"P-212-19K2L-CN\",\"car_model\":\"Hiace\",\"wholesale_price_rwf\":36000,\"retail_price_rwf\":52000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(46,1,16,4,'Toyota Hiace Tail Lamp 2005-2013 Noble C10 RH','toyota-hiace-tail-lamp-2005-2013-noble-c10-p-212-19k2r-cn','P-212-19K2R-CN','Toyota Hiace Tail Lamp 2005-2013 Noble C10 RH. PDF car model: Hiace. Part number: P-212-19K2R-CN. Wholesale price: 36,000 Rwf. Retail price: 52,000 Rwf.','Hiace P-212-19K2R-CN',NULL,NULL,52000.00,52000.00,NULL,36000.00,3,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Hiace\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"P-212-19K2R-CN\",\"car_model\":\"Hiace\",\"wholesale_price_rwf\":36000,\"retail_price_rwf\":52000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-02-28 11:20:07','2026-05-14 14:27:20'),(47,1,1,4,'Toyota Corolla Side Mirror 2005-2007 5-Line Lamp Black LH','toyota-corolla-side-mirror-2005-2007-5-line-lamp-black-lh','AS-2632-504-L','Toyota Corolla Side Mirror 2005-2007 5-Line Lamp Black LH. PDF car model: Corolla. Part number: AS-2632-504-L. Wholesale price: 72,000 Rwf. Retail price: 98,000 Rwf.','Corolla AS-2632-504-L',NULL,'[]',98000.00,98000.00,NULL,72000.00,3,2,1,0,1,'new','public','','none','','[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AS-2632-504-L\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":72000,\"retail_price_rwf\":98000}','','','',0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-17 08:08:48'),(48,1,1,4,'Toyota Corolla Side Mirror 2005-2007 5-Line Lamp Black RH','toyota-corolla-side-mirror-2005-2007-5-line-lamp-black-rh','AS-2632-504-R','Toyota Corolla Side Mirror 2005-2007 5-Line Lamp Black RH. PDF car model: Corolla. Part number: AS-2632-504-R. Wholesale price: 72,000 Rwf. Retail price: 98,000 Rwf.','Corolla AS-2632-504-R',NULL,'[]',98000.00,98000.00,NULL,72000.00,3,2,1,0,1,'new','public','','none','','[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AS-2632-504-R\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":72000,\"retail_price_rwf\":98000}','','','',0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-20 11:47:28'),(49,1,1,4,'Toyota Corolla Side Mirror 2001-2005 3-Line LH','toyota-corolla-side-mirror-2001-2005-3-line-as-2632-503-l','AS-2632-503-L','Toyota Corolla Side Mirror 2001-2005 3-Line LH. PDF car model: Corolla. Part number: AS-2632-503-L. Wholesale price: 55,000 Rwf. Retail price: 75,000 Rwf.','Corolla AS-2632-503-L',NULL,NULL,75000.00,75000.00,NULL,55000.00,3,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AS-2632-503-L\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":55000,\"retail_price_rwf\":75000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-17 08:46:20'),(50,1,1,4,'Toyota Corolla Side Mirror 2001-2005 3-Line RH','toyota-corolla-side-mirror-2001-2005-3-line-as-2632-503-r','AS-2632-503-R','Toyota Corolla Side Mirror 2001-2005 3-Line RH. PDF car model: Corolla. Part number: AS-2632-503-R. Wholesale price: 55,000 Rwf. Retail price: 75,000 Rwf.','Corolla AS-2632-503-R',NULL,NULL,75000.00,75000.00,NULL,55000.00,5,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AS-2632-503-R\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":55000,\"retail_price_rwf\":75000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-14 14:25:25'),(51,1,1,4,'Toyota Corolla Side Mirror 2008-2010 LED 5-Line LH','toyota-corolla-side-mirror-2008-2010-led-5-line-as-2632-501-l','AS-2632-501-L','Toyota Corolla Side Mirror 2008-2010 LED 5-Line LH. PDF car model: Corolla. Part number: AS-2632-501-L. Wholesale price: 75,000 Rwf. Retail price: 100,000 Rwf.','Corolla AS-2632-501-L',NULL,NULL,100000.00,100000.00,NULL,75000.00,2,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AS-2632-501-L\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":75000,\"retail_price_rwf\":100000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-14 14:25:25'),(52,1,1,4,'Toyota Corolla Side Mirror 2008-2010 LED 5-Line RH','toyota-corolla-side-mirror-2008-2010-led-5-line-as-2632-501-r','AS-2632-501-R','Toyota Corolla Side Mirror 2008-2010 LED 5-Line RH. PDF car model: Corolla. Part number: AS-2632-501-R. Wholesale price: 75,000 Rwf. Retail price: 100,000 Rwf.','Corolla AS-2632-501-R',NULL,NULL,100000.00,100000.00,NULL,75000.00,2,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AS-2632-501-R\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":75000,\"retail_price_rwf\":100000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-14 14:25:25'),(53,1,1,4,'Toyota Corolla Side Mirror 2008 3-Line Black LH','toyota-corolla-side-mirror-2008-3-line-black-as-2632-502-l','AS-2632-502-L','Toyota Corolla Side Mirror 2008 3-Line Black LH. PDF car model: Corolla. Part number: AS-2632-502-L. Wholesale price: 65,000 Rwf. Retail price: 88,000 Rwf.','Corolla AS-2632-502-L',NULL,NULL,88000.00,88000.00,NULL,65000.00,2,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AS-2632-502-L\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":65000,\"retail_price_rwf\":88000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-14 14:25:25'),(54,1,1,4,'Toyota Corolla Side Mirror 2008 3-Line Black RH','toyota-corolla-side-mirror-2008-3-line-black-as-2632-502-r','AS-2632-502-R','Toyota Corolla Side Mirror 2008 3-Line Black RH. PDF car model: Corolla. Part number: AS-2632-502-R. Wholesale price: 65,000 Rwf. Retail price: 88,000 Rwf.','Corolla AS-2632-502-R',NULL,NULL,88000.00,88000.00,NULL,65000.00,2,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AS-2632-502-R\",\"car_model\":\"Corolla\",\"wholesale_price_rwf\":65000,\"retail_price_rwf\":88000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-14 14:25:25'),(55,1,1,4,'Toyota Corolla Altis Camry Fog Lamp 2003-2004 Set','toyota-corolla-altis-camry-fog-lamp-2003-2004-ty40270','TY40270','Toyota Corolla Altis Camry Fog Lamp 2003-2004 Set. PDF car model: Corolla/Altis/Camry. Part number: TY40270. Wholesale price: 72,000 Rwf. Retail price: 95,000 Rwf.','Corolla/Altis/Camry TY40270',NULL,NULL,95000.00,95000.00,NULL,72000.00,14,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Corolla\",\"Altis\",\"Camry\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"TY40270\",\"car_model\":\"Corolla/Altis/Camry\",\"wholesale_price_rwf\":72000,\"retail_price_rwf\":95000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-14 14:25:25'),(56,1,2,4,'Toyota Camry Head Lamp 2010 USA LH','toyota-camry-head-lamp-2010-usa-at-22669-11-l','AT-22669-11-L','Toyota Camry Head Lamp 2010 USA LH. PDF car model: Camry. Part number: AT-22669-11-L. Wholesale price: 100,000 Rwf. Retail price: 135,000 Rwf.','Camry AT-22669-11-L',NULL,NULL,135000.00,135000.00,NULL,100000.00,1,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Camry\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AT-22669-11-L\",\"car_model\":\"Camry\",\"wholesale_price_rwf\":100000,\"retail_price_rwf\":135000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-14 14:25:25'),(57,1,2,4,'Toyota Camry Head Lamp 2010 USA RH','toyota-camry-head-lamp-2010-usa-rh','AT-22669-11-R','Toyota Camry Head Lamp 2010 USA RH. PDF car model: Camry. Part number: AT-22669-11-R. Wholesale price: 100,000 Rwf. Retail price: 135,000 Rwf.','Camry AT-22669-11-R',NULL,'[]',135000.00,135000.00,NULL,100000.00,1,2,1,0,1,'new','public','','none','','[\"Camry\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AT-22669-11-R\",\"car_model\":\"Camry\",\"wholesale_price_rwf\":100000,\"retail_price_rwf\":135000}','','','',0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-14 16:56:20'),(58,1,14,4,'Toyota RAV4 Side Mirror 2014 LED 5-Line LH','toyota-rav4-side-mirror-2014-led-5-line-as-2632-320-l','AS-2632-320-L','Toyota RAV4 Side Mirror 2014 LED 5-Line LH. PDF car model: RAV4. Part number: AS-2632-320-L. Wholesale price: 105,000 Rwf. Retail price: 140,000 Rwf.','RAV4 AS-2632-320-L',NULL,NULL,140000.00,140000.00,NULL,105000.00,1,2,1,0,0,'new','public',NULL,'none',NULL,'[\"RAV4\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AS-2632-320-L\",\"car_model\":\"RAV4\",\"wholesale_price_rwf\":105000,\"retail_price_rwf\":140000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-14 14:25:25'),(59,1,14,4,'Toyota RAV4 Side Mirror 2014 LED 5-Line RH','toyota-rav4-side-mirror-2014-led-5-line-as-2632-320-r','AS-2632-320-R','Toyota RAV4 Side Mirror 2014 LED 5-Line RH. PDF car model: RAV4. Part number: AS-2632-320-R. Wholesale price: 105,000 Rwf. Retail price: 140,000 Rwf.','RAV4 AS-2632-320-R',NULL,NULL,140000.00,140000.00,NULL,105000.00,1,2,1,0,0,'new','public',NULL,'none',NULL,'[\"RAV4\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AS-2632-320-R\",\"car_model\":\"RAV4\",\"wholesale_price_rwf\":105000,\"retail_price_rwf\":140000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-14 14:25:25'),(60,1,227,4,'Toyota Hilux Vigo/Revo Rear Lamp Hilux Revo S.Africa Type LH','toyota-hilux-vigo-revo-rear-lamp-hilux-revo-safrica-type-ty-rvr16ww-l','TY-RVR16WW-L','Toyota Hilux Vigo/Revo Rear Lamp Hilux Revo S.Africa Type LH. PDF car model: Hilux Vigo/Revo. Part number: TY-RVR16WW-L. Wholesale price: 55,000 Rwf. Retail price: 80,000 Rwf.','Hilux Vigo/Revo TY-RVR16WW-L',NULL,NULL,80000.00,80000.00,NULL,55000.00,10,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Hilux Vigo/Revo\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"TY-RVR16WW-L\",\"car_model\":\"Hilux Vigo/Revo\",\"wholesale_price_rwf\":55000,\"retail_price_rwf\":80000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-14 14:27:20'),(61,1,227,4,'Toyota Hilux Vigo/Revo Rear Lamp Hilux Revo S.Africa Type RH','toyota-hilux-vigo-revo-rear-lamp-hilux-revo-safrica-type-ty-rvr16ww-r','TY-RVR16WW-R','Toyota Hilux Vigo/Revo Rear Lamp Hilux Revo S.Africa Type RH. PDF car model: Hilux Vigo/Revo. Part number: TY-RVR16WW-R. Wholesale price: 55,000 Rwf. Retail price: 80,000 Rwf.','Hilux Vigo/Revo TY-RVR16WW-R',NULL,NULL,80000.00,80000.00,NULL,55000.00,10,2,1,0,0,'new','public',NULL,'none',NULL,'[\"Hilux Vigo/Revo\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"TY-RVR16WW-R\",\"car_model\":\"Hilux Vigo/Revo\",\"wholesale_price_rwf\":55000,\"retail_price_rwf\":80000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-14 14:27:20'),(62,14,228,4,'Lexus RX330 Rear Lamp 2004 LH','lexus-rx330-rear-lamp-2004-at-2211-121-l','AT-2211-121-L','Lexus RX330 Rear Lamp 2004 LH. PDF car model: Lexus RX330. Part number: AT-2211-121-L. Wholesale price: 68,000 Rwf. Retail price: 92,000 Rwf.','Lexus RX330 AT-2211-121-L',NULL,NULL,92000.00,92000.00,NULL,68000.00,1,2,1,0,0,'new','public',NULL,'none',NULL,'[\"RX330\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AT-2211-121-L\",\"car_model\":\"Lexus RX330\",\"wholesale_price_rwf\":68000,\"retail_price_rwf\":92000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-14 14:27:20'),(63,14,228,4,'Lexus RX330 Rear Lamp 2004 RH','lexus-rx330-rear-lamp-2004-at-2211-121-r','AT-2211-121-R','Lexus RX330 Rear Lamp 2004 RH. PDF car model: Lexus RX330. Part number: AT-2211-121-R. Wholesale price: 68,000 Rwf. Retail price: 92,000 Rwf.','Lexus RX330 AT-2211-121-R',NULL,NULL,92000.00,92000.00,NULL,68000.00,1,2,1,0,0,'new','public',NULL,'none',NULL,'[\"RX330\"]','[\"pdf-import\",\"lighting\",\"exterior\"]','{\"source\":\"spare xpress pricing and images - Inventory + Images.pdf\",\"part_number\":\"AT-2211-121-R\",\"car_model\":\"Lexus RX330\",\"wholesale_price_rwf\":68000,\"retail_price_rwf\":92000}',NULL,NULL,NULL,0,0,'in_stock','new',0,1,'2026-05-14 14:25:25','2026-05-14 14:27:20');
/*!40000 ALTER TABLE `products_enhanced` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `request_timeline`
--

DROP TABLE IF EXISTS `request_timeline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request_timeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `status_description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_request_timeline_request_id` (`request_id`),
  KEY `idx_request_timeline_created_at` (`created_at`),
  CONSTRAINT `request_timeline_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `order_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request_timeline`
--

LOCK TABLES `request_timeline` WRITE;
/*!40000 ALTER TABLE `request_timeline` DISABLE KEYS */;
/*!40000 ALTER TABLE `request_timeline` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_logs`
--

DROP TABLE IF EXISTS `system_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `log_type` enum('backup','maintenance','error','security','performance') NOT NULL,
  `log_level` enum('debug','info','warning','error','critical') DEFAULT 'info',
  `message` text NOT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Additional context data' CHECK (json_valid(`context`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `performed_by` int(11) DEFAULT NULL COMMENT 'Admin user ID',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_logs`
--

LOCK TABLES `system_logs` WRITE;
/*!40000 ALTER TABLE `system_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_group` varchar(50) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','integer','float','boolean','json','array') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` VALUES (1,'general','site_name','SPARE XPRESS LTD','string','Website name',0,'2025-12-08 18:37:58','2025-12-08 18:37:58'),(2,'general','site_description','Professional Auto-Parts Management Platform','string','Website description',0,'2025-12-08 18:37:58','2025-12-08 18:37:58'),(3,'general','currency','RWF','string','Default currency',0,'2025-12-08 18:37:58','2025-12-08 18:37:58'),(4,'general','timezone','Africa/Kigali','string','System timezone',0,'2025-12-08 18:37:58','2025-12-08 18:37:58'),(5,'orders','default_tax_rate','18.00','float','Default tax rate percentage',0,'2025-12-08 18:37:58','2025-12-08 18:37:58'),(6,'orders','low_stock_threshold','5','integer','Low stock alert threshold',0,'2025-12-08 18:37:58','2025-12-08 18:37:58'),(7,'notifications','email_enabled','true','boolean','Enable email notifications',0,'2025-12-08 18:37:58','2025-12-08 18:37:58'),(8,'notifications','sms_enabled','true','boolean','Enable SMS notifications',0,'2025-12-08 18:37:58','2025-12-08 18:37:58'),(9,'notifications','whatsapp_enabled','true','boolean','Enable WhatsApp notifications',0,'2025-12-08 18:37:58','2025-12-08 18:37:58'),(10,'security','session_timeout','3600','integer','Session timeout in seconds',0,'2025-12-08 18:37:58','2025-12-08 18:37:58'),(11,'security','max_login_attempts','5','integer','Maximum login attempts before lockout',0,'2025-12-08 18:37:58','2025-12-08 18:37:58'),(12,'api','rate_limit_per_hour','1000','integer','API rate limit per hour',0,'2025-12-08 18:37:58','2025-12-08 18:37:58'),(0,'notifications','email_enabled','false','string',NULL,0,'2026-01-05 09:29:57','2026-01-05 09:29:57'),(0,'notifications','sms_enabled','false','string',NULL,0,'2026-01-05 09:29:57','2026-01-05 09:29:57'),(0,'notifications','whatsapp_enabled','false','string',NULL,0,'2026-01-05 09:29:57','2026-01-05 09:29:57'),(0,'notifications','admin_panel_enabled','true','string',NULL,0,'2026-01-05 09:29:57','2026-01-05 09:29:57'),(0,'notifications','auto_refresh_interval','30','string',NULL,0,'2026-01-05 09:29:57','2026-01-05 09:29:57'),(0,'notification_triggers','trigger_new_order_admin','true','string',NULL,0,'2026-01-05 09:29:57','2026-01-05 09:29:57'),(0,'notification_triggers','trigger_payment_received','true','string',NULL,0,'2026-01-05 09:29:57','2026-01-05 09:29:57'),(0,'notification_triggers','trigger_low_stock_alert','true','string',NULL,0,'2026-01-05 09:29:57','2026-01-05 09:29:57'),(0,'notification_triggers','trigger_out_of_stock_alert','true','string',NULL,0,'2026-01-05 09:29:57','2026-01-05 09:29:57'),(0,'notification_triggers','trigger_system_backup','true','string',NULL,0,'2026-01-05 09:29:57','2026-01-05 09:29:57'),(0,'notification_triggers','trigger_security_alert','true','string',NULL,0,'2026-01-05 09:29:57','2026-01-05 09:29:57');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle_brands`
--

DROP TABLE IF EXISTS `vehicle_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicle_brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `founded_year` year(4) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `manufacturer_type` enum('OEM','Aftermarket','Both') DEFAULT 'OEM',
  `status` enum('active','inactive','discontinued') DEFAULT 'active',
  `featured` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `total_models` int(11) DEFAULT 0,
  `total_products` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_status` (`status`),
  KEY `idx_featured` (`featured`),
  KEY `idx_country` (`country`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_brands`
--

LOCK TABLES `vehicle_brands` WRITE;
/*!40000 ALTER TABLE `vehicle_brands` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicle_brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle_brands_enhanced`
--

DROP TABLE IF EXISTS `vehicle_brands_enhanced`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicle_brands_enhanced` (
  `id` int(11) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `logo_image` varchar(255) DEFAULT NULL,
  `brand_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `founded_year` int(11) DEFAULT NULL,
  `manufacturer_details` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_brands_enhanced`
--

LOCK TABLES `vehicle_brands_enhanced` WRITE;
/*!40000 ALTER TABLE `vehicle_brands_enhanced` DISABLE KEYS */;
INSERT INTO `vehicle_brands_enhanced` VALUES (1,'Toyota','toyota','uploads/brands/toyota_logo.svg','uploads/brands/toyota_brand.jpg','Leading Japanese automaker','Japan',1937,NULL,NULL,NULL,NULL,1,1,NULL,NULL,'2025-12-08 17:11:39','2026-01-09 14:40:04'),(2,'Honda','honda','uploads/brands/honda_logo.svg','uploads/brands/honda_brand.jpg','Innovative automotive manufacturer','Japan',1948,NULL,NULL,NULL,NULL,1,2,NULL,NULL,'2025-12-08 17:11:39','2026-01-09 14:40:04'),(3,'Nissan','nissan',NULL,NULL,'Global automotive brand','Japan',1932,NULL,NULL,NULL,NULL,1,3,NULL,NULL,'2025-12-08 17:11:39','2025-12-16 11:50:24'),(4,'BMW','bmw','uploads/brands/bmw_logo.svg','uploads/brands/bmw_brand.jpg','German luxury car manufacturer','Germany',1916,'','','','',1,4,'','','2025-12-08 17:11:40','2026-01-09 14:40:04'),(5,'Mercedes-Benz','mercedes-benz','uploads/brands/mercedes-benz-9.svg','uploads/brands/mercedes-benz-9.svg','Luxury automotive brand','Germany',1926,NULL,NULL,NULL,NULL,1,5,NULL,NULL,'2025-12-08 17:11:40','2025-12-24 17:00:07'),(6,'Ford','ford','uploads/brands/ford_logo.svg','uploads/brands/ford_brand.jpg','American automotive giant','USA',1903,NULL,NULL,NULL,NULL,1,6,NULL,NULL,'2025-12-08 17:11:40','2026-01-09 14:40:04'),(7,'Volkswagen','volkswagen','uploads/brands/volkswagen_logo.svg','uploads/brands/volkswagen_brand.jpg','German automotive manufacturer','Germany',1937,NULL,NULL,NULL,NULL,1,7,NULL,NULL,'2025-12-08 17:11:40','2026-01-09 14:40:04'),(8,'Hyundai','hyundai','uploads/brands/hyundai_logo.svg','uploads/brands/hyundai_brand.jpg','Korean automotive brand','South Korea',1967,NULL,NULL,NULL,NULL,1,8,NULL,NULL,'2025-12-08 17:11:40','2026-01-09 14:40:04'),(9,'Mitsubishi','mitsubishi',NULL,NULL,'Reliable Japanese manufacturer','Japan',1870,NULL,NULL,NULL,NULL,1,9,NULL,NULL,'2025-12-08 17:11:39','2025-12-16 11:50:24'),(10,'Mazda','mazda',NULL,NULL,'Innovative Japanese automaker','Japan',1920,NULL,NULL,NULL,NULL,1,10,NULL,NULL,'2025-12-08 17:11:39','2025-12-16 11:50:24'),(11,'Suzuki','suzuki',NULL,NULL,'Affordable Japanese vehicles','Japan',1909,NULL,NULL,NULL,NULL,1,11,NULL,NULL,'2025-12-08 17:11:39','2025-12-16 11:50:24'),(12,'Subaru','subaru',NULL,NULL,'AWD specialist Japanese brand','Japan',1953,NULL,NULL,NULL,NULL,1,12,NULL,NULL,'2025-12-08 17:11:39','2025-12-16 11:50:24'),(13,'Isuzu','isuzu',NULL,NULL,'Commercial vehicle expert','Japan',1916,NULL,NULL,NULL,NULL,1,13,NULL,NULL,'2025-12-08 17:11:39','2025-12-16 11:50:24'),(14,'Lexus','lexus',NULL,NULL,'Luxury division of Toyota','Japan',1989,NULL,NULL,NULL,NULL,1,14,NULL,NULL,'2025-12-08 17:11:39','2025-12-16 11:50:24'),(15,'Kia','kia',NULL,NULL,'Korean automotive manufacturer','South Korea',1944,NULL,NULL,NULL,NULL,1,15,NULL,NULL,'2025-12-08 17:11:40','2025-12-16 11:50:24'),(16,'BYD','byd','uploads/brands/byd-1.svg','uploads/brands/byd-1.svg','Chinese EV and hybrid manufacturer','China',1995,NULL,NULL,NULL,NULL,1,16,NULL,NULL,'2025-12-08 17:11:40','2025-12-24 17:00:07'),(17,'Geely','geely','uploads/brands/geely-logo-2.svg','uploads/brands/geely-logo-2.svg','Chinese automaker including Lynk & Co','China',1986,NULL,NULL,NULL,NULL,1,17,NULL,NULL,'2025-12-08 17:11:40','2025-12-24 17:00:07'),(18,'Chery','chery','uploads/brands/chery-3.svg','uploads/brands/chery-3.svg','Chinese budget car manufacturer','China',1997,NULL,NULL,NULL,NULL,1,18,NULL,NULL,'2025-12-08 17:11:40','2025-12-24 17:00:07'),(19,'Great Wall','great-wall','uploads/brands/great-wall-seeklogo.png','','Chinese SUV manufacturer','China',1984,NULL,NULL,NULL,NULL,1,19,NULL,NULL,'2025-12-08 17:11:40','2025-12-24 17:00:07'),(20,'JAC','jac','uploads/brands/jac-motors-seeklogo.png','','Chinese light commercial and SUV manufacturer','China',1964,NULL,NULL,NULL,NULL,1,20,NULL,NULL,'2025-12-08 17:11:40','2025-12-24 17:00:07'),(21,'Dongfeng','dongfeng','uploads/brands/DONGFENG.png','','Chinese automaker with passenger models','China',1969,NULL,NULL,NULL,NULL,1,21,NULL,NULL,'2025-12-08 17:11:40','2025-12-24 17:00:07'),(22,'Wuling','wuling',NULL,NULL,'Chinese EV and microcar manufacturer','China',2002,NULL,NULL,NULL,NULL,1,22,NULL,NULL,'2025-12-08 17:11:40','2025-12-16 11:50:24'),(23,'BAIC','baic','uploads/brands/BAIC.png','','Chinese passenger car and SUV manufacturer','China',1958,'','','','',1,23,'','','2025-12-08 17:11:40','2026-01-07 06:42:08'),(24,'Changan','changan','uploads/brands/changan-automobile-logo-1.svg','','Chinese EV and gas car manufacturer','China',1862,NULL,NULL,NULL,NULL,1,24,NULL,NULL,'2025-12-08 17:11:40','2025-12-24 17:00:07'),(25,'MG','mg',NULL,NULL,'Chinese-British heritage EV and ICE manufacturer','China',1924,NULL,NULL,NULL,NULL,1,25,NULL,NULL,'2025-12-08 17:11:40','2025-12-16 11:50:24'),(26,'Peugeot','peugeot',NULL,NULL,'French automaker','France',1810,NULL,NULL,NULL,NULL,1,26,NULL,NULL,'2025-12-08 17:11:40','2025-12-16 11:50:24'),(27,'Renault','renault','uploads/brands/renault-2.svg','uploads/brands/renault-2.svg','French automaker','France',1899,NULL,NULL,NULL,NULL,1,27,NULL,NULL,'2025-12-08 17:11:40','2025-12-24 17:00:07'),(28,'Citroën','citroen','uploads/brands/citroen-racing-2009-2016-logo.svg','','French automaker','France',1919,NULL,NULL,NULL,NULL,1,28,NULL,NULL,'2025-12-08 17:11:40','2025-12-24 17:00:07'),(29,'Skoda','skoda',NULL,NULL,'Czech automaker part of Volkswagen Group','Czech Republic',1895,NULL,NULL,NULL,NULL,1,29,NULL,NULL,'2025-12-08 17:11:40','2025-12-16 11:50:24'),(30,'Audi','audi',NULL,NULL,'German luxury car manufacturer','Germany',1909,NULL,NULL,NULL,NULL,1,30,NULL,NULL,'2025-12-08 17:11:40','2025-12-16 11:50:24'),(31,'Volvo','volvo',NULL,NULL,'Swedish automaker','Sweden',1927,NULL,NULL,NULL,NULL,1,31,NULL,NULL,'2025-12-08 17:11:40','2025-12-16 11:50:24'),(32,'Chevrolet','chevrolet',NULL,NULL,'American automotive brand','USA',1911,NULL,NULL,NULL,NULL,1,32,NULL,NULL,'2025-12-08 17:11:40','2025-12-16 11:50:24'),(33,'Land Rover','land-rover',NULL,NULL,'British luxury SUV manufacturer','UK',1948,NULL,NULL,NULL,NULL,1,33,NULL,NULL,'2025-12-08 17:11:39','2025-12-16 11:50:24');
/*!40000 ALTER TABLE `vehicle_brands_enhanced` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle_models`
--

DROP TABLE IF EXISTS `vehicle_models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicle_models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `year_from` year(4) DEFAULT NULL,
  `year_to` year(4) DEFAULT NULL,
  `engine_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`engine_types`)),
  `fuel_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fuel_types`)),
  `transmission_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`transmission_types`)),
  `body_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`body_types`)),
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `compatibility_notes` text DEFAULT NULL,
  `status` enum('active','discontinued','upcoming') DEFAULT 'active',
  `featured` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `total_products` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_brand` (`brand_id`),
  KEY `idx_slug` (`slug`),
  KEY `idx_status` (`status`),
  KEY `idx_year_from` (`year_from`),
  KEY `idx_year_to` (`year_to`),
  CONSTRAINT `vehicle_models_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `vehicle_brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_models`
--

LOCK TABLES `vehicle_models` WRITE;
/*!40000 ALTER TABLE `vehicle_models` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicle_models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle_models_enhanced`
--

DROP TABLE IF EXISTS `vehicle_models_enhanced`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicle_models_enhanced` (
  `id` int(11) NOT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `model_name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `model_image` varchar(255) DEFAULT NULL,
  `year_from` int(11) DEFAULT NULL,
  `year_to` int(11) DEFAULT NULL,
  `engine_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of engine types: ["petrol", "diesel", "electric"]' CHECK (json_valid(`engine_types`)),
  `fuel_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of fuel types: ["petrol", "diesel", "electric", "hybrid"]' CHECK (json_valid(`fuel_types`)),
  `transmission_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of transmission types: ["manual", "automatic", "cvt"]' CHECK (json_valid(`transmission_types`)),
  `body_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of body types: ["sedan", "suv", "hatchback"]' CHECK (json_valid(`body_types`)),
  `compatibility_info` text DEFAULT NULL,
  `technical_specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Technical specifications in JSON format' CHECK (json_valid(`technical_specs`)),
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_models_enhanced`
--

LOCK TABLES `vehicle_models_enhanced` WRITE;
/*!40000 ALTER TABLE `vehicle_models_enhanced` DISABLE KEYS */;
INSERT INTO `vehicle_models_enhanced` VALUES (1,1,'Corolla','corolla',NULL,2015,2024,NULL,'[\"petrol\", \"diesel\"]',NULL,'[\"sedan\"]',NULL,NULL,1,1,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(2,1,'Camry','camry',NULL,2018,2024,NULL,'[\"petrol\", \"hybrid\"]',NULL,'[\"sedan\"]',NULL,NULL,1,2,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(3,2,'Civic','civic',NULL,2016,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\", \"hatchback\"]',NULL,NULL,1,3,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(4,2,'CR-V','cr-v',NULL,2017,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,4,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(5,3,'Altima','altima',NULL,2019,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,5,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(6,4,'X3','x3',NULL,2018,2024,NULL,'[\"petrol\", \"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,6,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(7,4,'X5','x5',NULL,2019,2024,NULL,'[\"petrol\", \"diesel\", \"electric\"]',NULL,'[\"suv\"]',NULL,NULL,1,7,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(8,5,'C-Class','c-class',NULL,2019,2024,NULL,'[\"petrol\", \"diesel\"]',NULL,'[\"sedan\"]',NULL,NULL,1,8,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(9,6,'F-150','f-150',NULL,2015,2024,NULL,'[\"petrol\", \"diesel\"]',NULL,'[\"pickup\"]',NULL,NULL,1,9,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(10,7,'Golf','golf',NULL,2018,2024,NULL,'[\"petrol\", \"diesel\", \"electric\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,10,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(11,8,'Tucson','tucson',NULL,2016,2024,NULL,'[\"petrol\", \"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,11,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(12,1,'Hilux','hilux',NULL,2015,2024,NULL,'[\"diesel\"]',NULL,'[\"pickup\"]',NULL,NULL,1,12,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(13,1,'Land Cruiser Prado','land-cruiser-prado',NULL,2018,2024,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,13,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(14,1,'RAV4','rav4',NULL,2019,2024,NULL,'[\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,14,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(15,1,'Fortuner','fortuner',NULL,2016,2024,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,15,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(16,1,'Hiace','hiace',NULL,2019,2024,NULL,'[\"diesel\"]',NULL,'[\"van\"]',NULL,NULL,1,16,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(17,1,'Prius','prius',NULL,2016,2024,NULL,'[\"hybrid\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,17,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(18,1,'Yaris','yaris',NULL,2017,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,18,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(19,1,'Highlander','highlander',NULL,2014,2024,NULL,'[\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,19,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(20,1,'Premio','premio',NULL,2007,2018,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,20,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(21,1,'Allion','allion',NULL,2007,2018,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,21,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(22,1,'Wish','wish',NULL,2003,2017,NULL,'[\"petrol\"]',NULL,'[\"mpv\"]',NULL,NULL,1,22,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(23,1,'Noah','noah',NULL,2001,2024,NULL,'[\"petrol\",\"hybrid\"]',NULL,'[\"mpv\"]',NULL,NULL,1,23,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(24,1,'Voxy','voxy',NULL,2001,2024,NULL,'[\"petrol\",\"hybrid\"]',NULL,'[\"mpv\"]',NULL,NULL,1,24,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(25,1,'Ist','ist',NULL,2002,2016,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,25,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(26,1,'Prius C','prius-c',NULL,2012,2020,NULL,'[\"hybrid\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,26,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(27,1,'Passo','passo',NULL,2004,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,27,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(28,1,'Aqua','aqua',NULL,2012,2024,NULL,'[\"hybrid\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,28,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(29,2,'Vezel','vezel',NULL,2014,2024,NULL,'[\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,29,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(30,2,'Grace','grace',NULL,2015,2024,NULL,'[\"hybrid\"]',NULL,'[\"sedan\"]',NULL,NULL,1,30,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(31,2,'Stream','stream',NULL,2006,2014,NULL,'[\"petrol\"]',NULL,'[\"mpv\"]',NULL,NULL,1,31,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(32,2,'Stepwgn','stepwgn',NULL,2015,2024,NULL,'[\"hybrid\"]',NULL,'[\"mpv\"]',NULL,NULL,1,32,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(33,2,'Accord','accord',NULL,2013,2024,NULL,'[\"hybrid\"]',NULL,'[\"sedan\"]',NULL,NULL,1,33,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(34,2,'HR-V','hr-v',NULL,2014,2024,NULL,'[\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,34,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(35,2,'Freed','freed',NULL,2011,2024,NULL,'[\"hybrid\"]',NULL,'[\"mpv\"]',NULL,NULL,1,35,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(36,3,'Patrol','patrol',NULL,2010,2024,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,36,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(37,3,'Navara','navara',NULL,2015,2024,NULL,'[\"diesel\"]',NULL,'[\"pickup\"]',NULL,NULL,1,37,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(38,3,'Note','note',NULL,2013,2024,NULL,'[\"hybrid\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,38,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(39,3,'March','march',NULL,2010,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,39,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(40,3,'Tiida','tiida',NULL,2007,2018,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,40,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(41,3,'Serena','serena',NULL,2011,2024,NULL,'[\"hybrid\"]',NULL,'[\"mpv\"]',NULL,NULL,1,41,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(42,3,'Juke','juke',NULL,2011,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,42,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(43,3,'Qashqai','qashqai',NULL,2014,2024,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,43,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(44,3,'Caravan','caravan',NULL,2012,2024,NULL,'[\"diesel\"]',NULL,'[\"van\"]',NULL,NULL,1,44,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(45,3,'Sunny','sunny',NULL,2011,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,45,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(46,3,'Wingroad','wingroad',NULL,2012,2024,NULL,'[\"petrol\"]',NULL,'[\"wagon\"]',NULL,NULL,1,46,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(47,3,'Teana','teana',NULL,2008,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,47,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(48,3,'Murano','murano',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,48,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(49,3,'Leaf','leaf',NULL,2018,2024,NULL,'[\"electric\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,49,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(50,9,'Pajero','pajero',NULL,2007,2021,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,50,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(51,9,'Pajero Sport','pajero-sport',NULL,2016,2024,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,51,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(52,9,'Outlander','outlander',NULL,2013,2024,NULL,'[\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,52,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(53,9,'RVR','rvr',NULL,2011,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,53,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(54,9,'Lancer','lancer',NULL,2008,2017,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,54,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(55,9,'Canter','canter',NULL,2013,2024,NULL,'[\"diesel\"]',NULL,'[\"truck\"]',NULL,NULL,1,55,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(56,9,'L200','l200',NULL,2015,2024,NULL,'[\"diesel\"]',NULL,'[\"pickup\"]',NULL,NULL,1,56,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(57,9,'Mirage','mirage',NULL,2014,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,57,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(58,9,'Airtrek','airtrek',NULL,2005,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,58,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(59,9,'Delica','delica',NULL,2011,2024,NULL,'[\"diesel\"]',NULL,'[\"van\"]',NULL,NULL,1,59,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(60,9,'Colt','colt',NULL,2008,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,60,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(61,9,'Space Wagon','space-wagon',NULL,2012,2024,NULL,'[\"petrol\"]',NULL,'[\"mpv\"]',NULL,NULL,1,61,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(62,10,'Axela','axela',NULL,2013,2024,NULL,'[\"hybrid\"]',NULL,'[\"sedan\"]',NULL,NULL,1,62,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(63,10,'Demio','demio',NULL,2015,2024,NULL,'[\"hybrid\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,63,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(64,10,'CX-5','cx-5',NULL,2012,2024,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,64,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(65,10,'CX-3','cx-3',NULL,2015,2024,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,65,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(66,10,'Premacy','premacy',NULL,2010,2018,NULL,'[\"diesel\"]',NULL,'[\"mpv\"]',NULL,NULL,1,66,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(67,10,'Atenza','atenza',NULL,2013,2024,NULL,'[\"diesel\"]',NULL,'[\"sedan\"]',NULL,NULL,1,67,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(68,10,'Verisa','verisa',NULL,2007,2011,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,68,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(69,10,'Bongo','bongo',NULL,2010,2024,NULL,'[\"diesel\"]',NULL,'[\"van\"]',NULL,NULL,1,69,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(70,10,'CX-30','cx-30',NULL,2020,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,70,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(71,11,'Vitara','vitara',NULL,2015,2024,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,71,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(72,11,'Escudo','escudo',NULL,2005,2015,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,72,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(73,11,'Alto','alto',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,73,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(74,11,'Baleno','baleno',NULL,2016,2024,NULL,'[\"hybrid\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,74,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(75,11,'S-Cross','s-cross',NULL,2015,2024,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,75,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(76,11,'Jimny','jimny',NULL,2018,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,76,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(77,11,'Carry','carry',NULL,2014,2024,NULL,'[\"diesel\"]',NULL,'[\"van\"]',NULL,NULL,1,77,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(78,11,'Ciaz','ciaz',NULL,2014,2024,NULL,'[\"hybrid\"]',NULL,'[\"sedan\"]',NULL,NULL,1,78,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(79,12,'Forester','forester',NULL,2013,2024,NULL,'[\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,79,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(80,12,'Outback','outback',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"wagon\"]',NULL,NULL,1,80,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(81,12,'Impreza','impreza',NULL,2012,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,81,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(82,12,'Legacy','legacy',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,82,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(83,12,'XV','xv',NULL,2012,2024,NULL,'[\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,83,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(84,12,'WRX','wrx',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,84,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(85,12,'Exiga','exiga',NULL,2009,2018,NULL,'[\"petrol\"]',NULL,'[\"mpv\"]',NULL,NULL,1,85,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(86,12,'Sambar','sambar',NULL,2012,2024,NULL,'[\"diesel\"]',NULL,'[\"van\"]',NULL,NULL,1,86,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(87,13,'Trooper','trooper',NULL,2000,2008,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,87,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(88,13,'Wizard','wizard',NULL,1998,2005,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,88,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(89,13,'MU-X','mu-x',NULL,2013,2024,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,89,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(90,13,'ELF','elf',NULL,2013,2024,NULL,'[\"diesel\"]',NULL,'[\"truck\"]',NULL,NULL,1,90,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(91,14,'LX','lx',NULL,2010,2024,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,91,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(92,14,'GX','gx',NULL,2010,2024,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,92,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(93,14,'RX','rx',NULL,2016,2024,NULL,'[\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,93,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(94,14,'NX','nx',NULL,2015,2024,NULL,'[\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,94,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(95,14,'ES','es',NULL,2013,2024,NULL,'[\"hybrid\"]',NULL,'[\"sedan\"]',NULL,NULL,1,95,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(96,14,'IS','is',NULL,2014,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,96,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(97,14,'LS','ls',NULL,2013,2024,NULL,'[\"hybrid\"]',NULL,'[\"sedan\"]',NULL,NULL,1,97,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(98,14,'CT','ct',NULL,2011,2017,NULL,'[\"hybrid\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,98,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(99,14,'GS','gs',NULL,2012,2020,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,99,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(100,14,'UX','ux',NULL,2019,2024,NULL,'[\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,100,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(101,8,'Santa Fe','santa-fe',NULL,2013,2024,NULL,'[\"petrol\",\"diesel\",\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,101,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(102,8,'Kona','kona',NULL,2018,2024,NULL,'[\"petrol\",\"hybrid\",\"electric\"]',NULL,'[\"suv\"]',NULL,NULL,1,102,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(103,8,'Elantra','elantra',NULL,2012,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,103,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(104,8,'Ioniq','ioniq',NULL,2017,2024,NULL,'[\"hybrid\",\"electric\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,104,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(105,8,'Creta','creta',NULL,2015,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,105,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(106,8,'H1','h1',NULL,2008,2024,NULL,'[\"diesel\"]',NULL,'[\"van\"]',NULL,NULL,1,106,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(107,8,'I20','i20',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,107,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(108,8,'Sonata','sonata',NULL,2015,2024,NULL,'[\"petrol\",\"hybrid\"]',NULL,'[\"sedan\"]',NULL,NULL,1,108,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(109,15,'Sorento','sorento',NULL,2015,2024,NULL,'[\"petrol\",\"diesel\",\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,109,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(110,15,'Seltos','seltos',NULL,2019,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,110,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(111,15,'Stonic','stonic',NULL,2018,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,111,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(112,15,'Rio','rio',NULL,2012,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\",\"sedan\"]',NULL,NULL,1,112,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(113,15,'Carnival','carnival',NULL,2015,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"mpv\"]',NULL,NULL,1,113,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(114,15,'Cerato','cerato',NULL,2014,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,114,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(115,15,'Stinger','stinger',NULL,2018,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,115,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(116,16,'Yuan Up','yuan-up',NULL,2020,2024,NULL,'[\"electric\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,116,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(117,16,'Dolphin','dolphin',NULL,2021,2024,NULL,'[\"electric\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,117,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(118,16,'SongPlus','songplus',NULL,2020,2024,NULL,'[\"electric\",\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,118,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(119,16,'Seagull','seagull',NULL,2023,2024,NULL,'[\"electric\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,119,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(120,16,'Seal','seal',NULL,2022,2024,NULL,'[\"electric\"]',NULL,'[\"sedan\"]',NULL,NULL,1,120,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(121,16,'Qin L','qin-l',NULL,2021,2024,NULL,'[\"hybrid\"]',NULL,'[\"sedan\"]',NULL,NULL,1,121,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(122,16,'Leopard 5','leopard-5',NULL,2022,2024,NULL,'[\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,122,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(123,17,'Emgrand 7','emgrand-7',NULL,2015,2024,NULL,'[\"petrol\",\"hybrid\"]',NULL,'[\"sedan\"]',NULL,NULL,1,123,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(124,17,'Coolray','coolray',NULL,2019,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,124,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(125,17,'Okavango','okavango',NULL,2020,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,125,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(126,17,'Xingyue','xingyue',NULL,2019,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,126,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(127,17,'Galaxy E5','galaxy-e5',NULL,2023,2024,NULL,'[\"electric\"]',NULL,'[\"sedan\"]',NULL,NULL,1,127,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(128,17,'Lynk & Co 01','lynk-co-01',NULL,2017,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,128,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(129,17,'Lynk & Co 02','lynk-co-02',NULL,2018,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,129,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(130,17,'Lynk & Co 03','lynk-co-03',NULL,2019,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,130,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(131,18,'Tiggo 4','tiggo-4',NULL,2018,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,131,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(132,18,'Tiggo 7','tiggo-7',NULL,2017,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,132,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(133,18,'Tiggo 8','tiggo-8',NULL,2018,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,133,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(134,18,'Arrizo 5','arrizo-5',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,134,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(135,18,'Arrizo 6','arrizo-6',NULL,2018,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,135,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(136,18,'Omoda C5','omoda-c5',NULL,2022,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,136,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(137,18,'Jetour X70','jetour-x70',NULL,2018,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,137,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(138,19,'H6','h6',NULL,2017,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,138,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(139,19,'Jolion','jolion',NULL,2020,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,139,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(140,19,'H2','h2',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,140,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(141,19,'Tank 300','tank-300',NULL,2020,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,141,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(142,19,'Tank 500','tank-500',NULL,2021,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,142,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(143,20,'JS3','js3',NULL,2018,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,143,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(144,20,'JS4','js4',NULL,2019,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,144,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(145,20,'JS6','js6',NULL,2020,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,145,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(146,20,'J7','j7',NULL,2019,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,146,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(147,21,'NanoBox','nanobox',NULL,2022,2024,NULL,'[\"electric\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,147,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(148,21,'Aeolus','aeolus',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,148,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(149,21,'Fengshen','fengshen',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,149,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(150,21,'Nammi','nammi',NULL,2020,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,150,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(151,22,'Hongguang Mini EV','hongguang-mini-ev',NULL,2020,2024,NULL,'[\"electric\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,151,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(152,22,'Bingo','bingo',NULL,2021,2024,NULL,'[\"electric\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,152,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(153,22,'E300','e300',NULL,2021,2024,NULL,'[\"electric\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,153,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(154,23,'BJ40','bj40',NULL,2014,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,154,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(155,23,'BJ60','bj60',NULL,2020,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,155,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(156,23,'X55','x55',NULL,2018,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,156,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(157,23,'EU5','eu5',NULL,2019,2024,NULL,'[\"electric\"]',NULL,'[\"sedan\"]',NULL,NULL,1,157,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(158,23,'BJEV','bjev',NULL,2020,2024,NULL,'[\"electric\"]',NULL,'[\"suv\"]',NULL,NULL,1,158,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(159,23,'BJ80','bj80',NULL,2018,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,159,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(160,24,'CS35','cs35',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,160,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(161,24,'CS35 Plus','cs35-plus',NULL,2019,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,161,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(162,24,'CS55','cs55',NULL,2017,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,162,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(163,24,'CS75','cs75',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,163,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(164,24,'CS75 Plus','cs75-plus',NULL,2019,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,164,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(165,24,'Eado','eado',NULL,2015,2024,NULL,'[\"electric\"]',NULL,'[\"sedan\"]',NULL,NULL,1,165,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(166,24,'Explorer','explorer',NULL,2021,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,166,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(167,24,'Yidong','yidong',NULL,2020,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,167,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(168,25,'ZS','zs',NULL,2017,2024,NULL,'[\"petrol\",\"electric\"]',NULL,'[\"suv\"]',NULL,NULL,1,168,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(169,25,'HS','hs',NULL,2019,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,169,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(170,25,'5','5',NULL,2017,2024,NULL,'[\"petrol\",\"electric\"]',NULL,'[\"hatchback\",\"sedan\"]',NULL,NULL,1,170,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(171,25,'3','3',NULL,2014,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,171,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(172,7,'Passat','passat',NULL,2015,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"sedan\"]',NULL,NULL,1,172,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(173,7,'Tiguan','tiguan',NULL,2016,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,173,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(174,7,'Beetle','beetle',NULL,2012,2019,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,174,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(175,7,'Touran','touran',NULL,2016,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"mpv\"]',NULL,NULL,1,175,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(176,7,'Jetta','jetta',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,176,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(177,5,'E-Class','e-class',NULL,2017,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"sedan\"]',NULL,NULL,1,177,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(178,5,'A-Class','a-class',NULL,2019,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,178,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(179,5,'CLA','cla',NULL,2019,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,179,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(180,5,'GLA','gla',NULL,2014,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,180,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(181,5,'GLC','glc',NULL,2016,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,181,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(182,5,'S-Class','s-class',NULL,2014,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"sedan\"]',NULL,NULL,1,182,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(183,5,'ML','ml',NULL,2012,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,183,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(184,5,'GLS','gls',NULL,2016,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,184,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(185,4,'5 Series','5-series',NULL,2017,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"sedan\"]',NULL,NULL,1,185,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(186,4,'1 Series','1-series',NULL,2017,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,186,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(187,4,'4 Series','4-series',NULL,2017,2024,NULL,'[\"petrol\"]',NULL,'[\"coupe\"]',NULL,NULL,1,187,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(188,4,'7 Series','7-series',NULL,2016,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"sedan\"]',NULL,NULL,1,188,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(189,26,'206','206',NULL,2007,2012,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,189,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(190,26,'308','308',NULL,2007,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"hatchback\",\"sedan\"]',NULL,NULL,1,190,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(191,26,'3008','3008',NULL,2017,2024,NULL,'[\"petrol\",\"diesel\",\"hybrid\"]',NULL,'[\"suv\"]',NULL,NULL,1,191,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(192,26,'5008','5008',NULL,2017,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,192,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(193,26,'508','508',NULL,2015,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"sedan\"]',NULL,NULL,1,193,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(194,27,'Megane','megane',NULL,2016,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"hatchback\",\"sedan\"]',NULL,NULL,1,194,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(195,27,'Scenic','scenic',NULL,2016,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"mpv\"]',NULL,NULL,1,195,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(196,27,'Captur','captur',NULL,2017,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,196,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(197,27,'Kadjar','kadjar',NULL,2015,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,197,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(198,28,'C3','c3',NULL,2018,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,198,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(199,28,'C4','c4',NULL,2018,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\",\"sedan\"]',NULL,NULL,1,199,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(200,28,'C5','c5',NULL,2018,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"sedan\"]',NULL,NULL,1,200,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(201,29,'Fabia','fabia',NULL,2017,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,201,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(202,29,'Octavia','octavia',NULL,2017,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"hatchback\",\"sedan\"]',NULL,NULL,1,202,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(203,29,'Superb','superb',NULL,2015,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"sedan\"]',NULL,NULL,1,203,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(204,30,'A3','a3','/uploads/models/a3_model.png',2016,2024,'[]','[\"petrol\",\"diesel\"]','[]','[\"sedan\"]','','{}',1,204,'2025-12-08 17:11:40','2025-12-30 17:39:44'),(205,30,'A4','a4','/uploads/models/a4_model.png',2016,2024,'[]','[\"petrol\",\"diesel\"]','[]','[\"sedan\"]','','{}',1,205,'2025-12-08 17:11:40','2025-12-30 17:54:15'),(206,30,'A6','a6','/uploads/models/a6_model.png',2019,2024,'[]','[\"petrol\",\"diesel\"]','[]','[\"sedan\"]','','{}',1,206,'2025-12-08 17:11:40','2026-01-06 15:43:14'),(207,30,'Q3','q3','/uploads/models/q3_model.png',2017,2025,'[]','[\"petrol\",\"diesel\"]','[]','[\"suv\"]','','{}',1,207,'2025-12-08 17:11:40','2026-01-09 14:27:01'),(208,30,'Q5','q5','/uploads/models/q5_model.png',2017,2024,'[]','[\"petrol\",\"diesel\"]','[]','[\"suv\"]','','{}',1,208,'2025-12-08 17:11:40','2026-01-09 14:27:52'),(209,30,'Q7','q7','/uploads/models/q7_model.png',2016,2024,'[]','[\"petrol\",\"diesel\"]','[]','[\"suv\"]','','{}',1,209,'2025-12-08 17:11:40','2026-01-09 14:28:56'),(210,31,'V60','v60',NULL,2019,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"wagon\"]',NULL,NULL,1,210,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(211,31,'XC60','xc60',NULL,2018,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,211,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(212,31,'XC90','xc90',NULL,2015,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,212,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(213,6,'Fiesta','fiesta',NULL,2013,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,213,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(214,6,'Escape','escape',NULL,2013,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,214,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(215,6,'Explorer','explorer',NULL,2016,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,215,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(216,6,'Everest','everest',NULL,2016,2024,NULL,'[\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,216,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(217,6,'Focus','focus',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,217,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(218,6,'Mustang','mustang',NULL,2015,2024,NULL,'[\"petrol\"]',NULL,'[\"coupe\"]',NULL,NULL,1,218,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(219,32,'Cruze','cruze',NULL,2012,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,219,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(220,32,'Aveo','aveo',NULL,2012,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,220,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(221,32,'Trailblazer','trailblazer',NULL,2019,2024,NULL,'[\"petrol\"]',NULL,'[\"suv\"]',NULL,NULL,1,221,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(222,32,'Malibu','malibu',NULL,2016,2024,NULL,'[\"petrol\"]',NULL,'[\"sedan\"]',NULL,NULL,1,222,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(223,32,'Spark','spark',NULL,2016,2024,NULL,'[\"petrol\"]',NULL,'[\"hatchback\"]',NULL,NULL,1,223,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(224,33,'Discovery','discovery',NULL,2017,2024,NULL,'[\"petrol\",\"diesel\"]',NULL,'[\"suv\"]',NULL,NULL,1,224,'2025-12-08 17:11:40','2025-12-08 17:11:40'),(0,8,'Accent','accent',NULL,2010,2024,NULL,'[\"petrol\", \"diesel\"]',NULL,'[\"sedan\", \"hatchback\"]',NULL,NULL,1,0,'2025-12-15 14:57:52','2025-12-15 14:57:52'),(225,1,'Avensis','avensis',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Imported from PDF inventory label: Avensis',NULL,1,999,'2026-05-14 14:27:20','2026-05-14 14:27:20'),(227,1,'Hilux Vigo/Revo','hilux-vigo-revo',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Imported from PDF inventory label: Hilux Vigo/Revo',NULL,1,999,'2026-05-14 14:27:20','2026-05-14 14:27:20'),(228,14,'RX330','rx330',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Imported from PDF inventory label: Lexus RX330',NULL,1,999,'2026-05-14 14:27:20','2026-05-14 14:27:20');
/*!40000 ALTER TABLE `vehicle_models_enhanced` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-20 16:50:39
