/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `account_deactivate_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_deactivate_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `date_time` datetime DEFAULT NULL,
  `otp_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` int NOT NULL,
  `assigned_modules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `message` text,
  `display_order` int NOT NULL,
  `status` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `banner_ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banner_ads` (
  `ban_id` int NOT NULL AUTO_INCREMENT,
  `ban_name` varchar(255) DEFAULT NULL,
  `ban_link` varchar(255) DEFAULT NULL,
  `ban_status` int DEFAULT '0',
  `EnBanimage` varchar(255) DEFAULT NULL,
  `ban_caption` text,
  `display_order` int DEFAULT NULL,
  `ChBanimage` varchar(255) DEFAULT NULL,
  `Video` text,
  `PageId` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ban_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `banner_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banner_master` (
  `ban_id` int NOT NULL AUTO_INCREMENT,
  `ban_name` varchar(255) DEFAULT NULL,
  `ban_link` varchar(255) NOT NULL,
  `ban_status` int DEFAULT '0',
  `EnBanimage` varchar(255) DEFAULT NULL,
  `ban_caption` text,
  `display_order` int DEFAULT NULL,
  `ChBanimage` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `EnBanMobileimage` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ban_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `brand_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brand_prices` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `GroupId` int NOT NULL,
  `Brand` int NOT NULL,
  `type` int DEFAULT '2' COMMENT '1=Price up, 2=Discount',
  `Price` decimal(20,2) NOT NULL DEFAULT '0.00',
  `dis_type` int NOT NULL DEFAULT '1',
  `Status` int NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`),
  KEY `Group` (`GroupId`,`Brand`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `BrandId` int NOT NULL AUTO_INCREMENT,
  `EnName` varchar(255) DEFAULT NULL,
  `PopularBrand` int DEFAULT '0',
  `exclude_global_dis` int NOT NULL DEFAULT '0',
  `Image` varchar(255) DEFAULT NULL,
  `MobileImage` varchar(255) DEFAULT NULL,
  `UniqueKey` varchar(255) DEFAULT NULL,
  `ParentLevel` int NOT NULL DEFAULT '0',
  `Details` text,
  `dis_type` int DEFAULT '0',
  `discount` int DEFAULT NULL,
  `meta_title` text,
  `meta_keywords` text,
  `meta_description` text,
  `seo_star_script` text,
  `BrandStatus` int DEFAULT '1' COMMENT '0=deactive,1=active',
  `youtube_available` int DEFAULT '0',
  `CreatedDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `DisplayOrder` int DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`BrandId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cart_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_details` (
  `detail_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `user_token` varchar(255) DEFAULT NULL,
  `row_key` int NOT NULL DEFAULT '0',
  `prod_id` int DEFAULT NULL,
  `prod_name` varchar(255) DEFAULT NULL,
  `prod_option` varchar(255) DEFAULT NULL,
  `prod_option_id` int DEFAULT NULL,
  `prod_quantity` int DEFAULT NULL,
  `prod_unit_price` decimal(20,2) DEFAULT '0.00',
  `Weight` decimal(20,3) DEFAULT '0.000',
  `prod_code` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`detail_id`),
  KEY `detail_id` (`detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL,
  `category_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_discount` double(8,2) NOT NULL,
  `meta_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_keyword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `category_shipping_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_shipping_charges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category_ids` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `amount` decimal(20,2) NOT NULL DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country` (
  `countryid` int NOT NULL AUTO_INCREMENT,
  `countryname` varchar(100) DEFAULT NULL,
  `countrycode` varchar(10) DEFAULT NULL,
  `taxtitle` varchar(255) DEFAULT NULL,
  `taxpercentage` decimal(20,2) DEFAULT '0.00',
  `country_status` int DEFAULT '1',
  `phonecode` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`countryid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `country_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country_codes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `iso` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phonecode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coupon_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_code` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `coupon_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int NOT NULL DEFAULT '0',
  `category_id` int NOT NULL DEFAULT '0',
  `brand_id` int NOT NULL DEFAULT '0',
  `validity` date DEFAULT NULL,
  `nooftimes` int NOT NULL DEFAULT '0',
  `discount_type` tinyint NOT NULL DEFAULT '1',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `customer_type` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coupon_code_usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_code_usage` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` int NOT NULL,
  `customer_id` int NOT NULL DEFAULT '0',
  `order_id` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_master` (
  `cust_id` int NOT NULL AUTO_INCREMENT,
  `cust_type` int NOT NULL DEFAULT '0' COMMENT '0=normal member, 1=Alumni',
  `cust_username` varchar(100) DEFAULT NULL,
  `cust_firstname` varchar(50) DEFAULT NULL,
  `cust_lastname` varchar(50) DEFAULT NULL,
  `cust_company` varchar(255) DEFAULT NULL,
  `cust_address1` varchar(100) DEFAULT NULL,
  `cust_address2` varchar(100) DEFAULT NULL,
  `cust_city` varchar(50) DEFAULT NULL,
  `cust_state` varchar(50) DEFAULT NULL,
  `cust_zip` varchar(20) DEFAULT NULL,
  `cust_country` varchar(50) DEFAULT NULL,
  `cust_phone` varchar(20) DEFAULT NULL,
  `cust_fax` varchar(255) DEFAULT NULL,
  `cust_landline` varchar(20) DEFAULT NULL,
  `cust_office` varchar(20) DEFAULT NULL,
  `cust_ic` varchar(20) DEFAULT NULL,
  `cust_dob` date DEFAULT NULL,
  `cust_email` varchar(100) DEFAULT NULL,
  `cust_password` varchar(255) DEFAULT NULL,
  `cust_status` int DEFAULT NULL,
  `cust_added_by` varchar(15) DEFAULT NULL,
  `cust_newsletter` int DEFAULT '0',
  `group_admin` int NOT NULL DEFAULT '0',
  `howyouknow` varchar(255) DEFAULT NULL,
  `password_reset_count` int DEFAULT '0',
  `cust_terms_agreed` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cust_create_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cust_modify_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cust_countrycode` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`cust_id`),
  KEY `cust_id` (`cust_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `emailtemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emailtemplate` (
  `id` int NOT NULL AUTO_INCREMENT,
  `templatename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `template_type` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fav_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fav_products` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `cust_id` int NOT NULL,
  `prod_id` int NOT NULL,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `frontendmenu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `frontendmenu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `menuname` varchar(100) NOT NULL,
  `parent_id` int NOT NULL,
  `page_id` int NOT NULL,
  `menu_url` varchar(255) NOT NULL,
  `status` tinyint NOT NULL,
  `display_order` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `groups` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `GroupTitle` varchar(255) DEFAULT NULL,
  `type` int DEFAULT '2' COMMENT '1=Price up, 2=Discount',
  `DiscountValue` int NOT NULL,
  `DiscountType` int NOT NULL COMMENT '1=$, 2=%',
  `Details` text,
  `Status` int DEFAULT '1' COMMENT '0=deactive,1=active',
  `DisplayOrder` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `international_shipping_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `international_shipping_methods` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Zone` varchar(255) NOT NULL,
  `CountriesList` text NOT NULL,
  `ShipCost` text NOT NULL,
  `PriceRange70` decimal(20,2) NOT NULL DEFAULT '0.00',
  `PriceRange300` decimal(20,2) NOT NULL DEFAULT '0.00',
  `PriceRange99999` decimal(20,2) NOT NULL DEFAULT '0.00',
  `Box_XS` decimal(20,2) NOT NULL DEFAULT '0.00',
  `Box_S` decimal(20,2) NOT NULL DEFAULT '0.00',
  `Box_M` decimal(20,2) NOT NULL DEFAULT '0.00',
  `Box_L` decimal(20,2) NOT NULL DEFAULT '0.00',
  `Box_XL` decimal(20,2) NOT NULL DEFAULT '0.00',
  `Box_Pallet` decimal(20,2) NOT NULL DEFAULT '0.00',
  `Status` int NOT NULL DEFAULT '1',
  `FreeShippingCost` decimal(20,2) NOT NULL DEFAULT '0.00',
  `CreatedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `local_shipping_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `local_shipping_methods` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Price` decimal(20,2) DEFAULT '0.00',
  `FreeShipAvailable` int NOT NULL DEFAULT '0',
  `FreeShipCost` decimal(20,2) DEFAULT '0.00',
  `CountriesList` varchar(255) DEFAULT NULL,
  `WeightFrom` decimal(20,2) NOT NULL,
  `WeightTo` decimal(20,2) DEFAULT NULL,
  `EnName` varchar(255) DEFAULT NULL,
  `PriceRange1` decimal(20,2) DEFAULT '0.00',
  `PriceRange2` decimal(20,2) DEFAULT '0.00',
  `PriceRange5` decimal(20,2) DEFAULT '0.00',
  `PriceRange10` decimal(20,2) DEFAULT '0.00',
  `PriceRange15` decimal(20,2) DEFAULT '0.00',
  `PriceRange20` decimal(20,2) DEFAULT '0.00',
  `PriceRange30` decimal(20,2) DEFAULT '0.00',
  `PriceRangeAbove30` decimal(20,2) DEFAULT '0.00',
  `Hint` varchar(255) DEFAULT NULL,
  `UniqueKey` varchar(255) DEFAULT NULL,
  `Status` int DEFAULT '1' COMMENT '0=deactive,1=active',
  `CreatedDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `DisplayOrder` int DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mobileversions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mobileversions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `version_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int NOT NULL DEFAULT '0',
  `action_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_delivery_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_delivery_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `delivery_info_id` int NOT NULL,
  `order_id` int NOT NULL,
  `detail_id` int NOT NULL,
  `prod_id` int NOT NULL,
  `quantity` int NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '1=delivered, 2=refunded',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_delivery_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_delivery_info` (
  `delivery_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `ship_tracking_number` varchar(255) DEFAULT NULL,
  `next_delivery_date` date DEFAULT NULL,
  `status` int NOT NULL,
  `delivered_date` date DEFAULT NULL,
  `order_status` int DEFAULT NULL,
  `shipping_by` varchar(255) DEFAULT NULL,
  `ship_mobile` varchar(50) DEFAULT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `pickup_instruction` text,
  `delivery_instructions` text,
  `ship_ads1` varchar(255) DEFAULT NULL,
  `ship_ads2` varchar(255) DEFAULT NULL,
  `ship_zip` varchar(50) DEFAULT NULL,
  `ship_country` varchar(255) DEFAULT NULL,
  `shipment_appdt` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`delivery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_details` (
  `detail_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `prod_id` int DEFAULT NULL,
  `prod_name` varchar(255) DEFAULT NULL,
  `prod_option` varchar(255) DEFAULT NULL,
  `prod_quantity` int DEFAULT NULL,
  `prod_unit_price` decimal(20,2) DEFAULT '0.00',
  `Weight` decimal(20,3) DEFAULT '0.000',
  `prod_code` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`detail_id`),
  KEY `detail_id` (`detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_master` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `is_fulfilled` tinyint NOT NULL DEFAULT '1',
  `user_id` int DEFAULT NULL,
  `order_type` int NOT NULL DEFAULT '1' COMMENT '1=Online order, 2=request for quote',
  `shipping_cost` decimal(20,2) DEFAULT '0.00',
  `fuelcharge_percentage` decimal(20,2) DEFAULT '0.00',
  `fuelcharges` decimal(20,2) DEFAULT '0.00',
  `handlingfee` decimal(20,2) DEFAULT '0.00',
  `packaging_fee` decimal(20,2) DEFAULT '0.00',
  `ship_method` int DEFAULT NULL,
  `ship_tracking_number` varchar(100) DEFAULT NULL,
  `tax_label` varchar(255) DEFAULT NULL,
  `tax_percentage` decimal(20,2) DEFAULT NULL,
  `tax_collected` decimal(20,2) DEFAULT '0.00',
  `payable_amount` decimal(20,2) DEFAULT '0.00',
  `discount_amount` decimal(20,2) DEFAULT '0.00',
  `oauth_token` longtext,
  `giftcertificate_amt` decimal(20,2) DEFAULT '0.00',
  `giftcert_id` int DEFAULT NULL,
  `discount_id` int DEFAULT NULL,
  `handling_msg` blob,
  `callback_number` varchar(100) DEFAULT NULL,
  `pay_method` varchar(20) DEFAULT NULL,
  `order_status` int DEFAULT '0',
  `approved_dt` datetime DEFAULT NULL,
  `shipment_appdt` datetime DEFAULT NULL,
  `cancel_dt` datetime DEFAULT NULL,
  `ship_fname` varchar(100) DEFAULT NULL,
  `ship_lname` varchar(100) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `ship_ads1` varchar(255) DEFAULT NULL,
  `ship_ads2` varchar(255) DEFAULT NULL,
  `ship_city` varchar(150) DEFAULT NULL,
  `ship_state` varchar(100) DEFAULT NULL,
  `ship_country` varchar(100) DEFAULT NULL,
  `ship_zip` varchar(50) DEFAULT NULL,
  `ship_email` varchar(255) DEFAULT NULL,
  `bill_fname` varchar(100) DEFAULT NULL,
  `bill_lname` varchar(100) DEFAULT NULL,
  `bill_ads1` varchar(255) DEFAULT NULL,
  `bill_ads2` varchar(255) DEFAULT NULL,
  `bill_city` varchar(150) DEFAULT NULL,
  `bill_state` varchar(100) DEFAULT NULL,
  `bill_country` varchar(100) DEFAULT NULL,
  `bill_zip` varchar(50) DEFAULT NULL,
  `date_entered` datetime DEFAULT CURRENT_TIMESTAMP,
  `trans_id` varchar(255) DEFAULT NULL,
  `error_reason` blob,
  `cc_type` varchar(100) DEFAULT NULL,
  `cc_number` varchar(50) DEFAULT NULL,
  `cc_cvv2` varchar(50) DEFAULT NULL,
  `cc_expiry` varchar(10) DEFAULT NULL,
  `pay_approved_by` varchar(20) DEFAULT NULL,
  `bill_ipaddress` varchar(100) DEFAULT NULL,
  `bill_compname` varchar(100) DEFAULT NULL,
  `bill_name_oncard` varchar(200) DEFAULT NULL,
  `dwn_link_status` int DEFAULT NULL,
  `dwn_count` int DEFAULT '0',
  `ship_mobile` varchar(50) DEFAULT NULL,
  `ship_fax` varchar(255) DEFAULT NULL,
  `ship_landline` varchar(50) DEFAULT NULL,
  `bill_mobile` varchar(50) DEFAULT NULL,
  `bill_email` varchar(255) DEFAULT NULL,
  `bill_fax` varchar(255) DEFAULT NULL,
  `bill_landline` varchar(50) DEFAULT NULL,
  `group_admin_approval` int NOT NULL DEFAULT '0',
  `weight` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `pickup_instruction` varchar(255) DEFAULT NULL,
  `delivery_instructions` text,
  `shipping_by` varchar(100) DEFAULT NULL,
  `delivery_times` int NOT NULL DEFAULT '1' COMMENT '1=single times, 2= multiple timesu',
  `if_items_unavailabel` int DEFAULT '3' COMMENT '1=call, 2= do not replace, 3=Replace',
  `quotation_status` int NOT NULL DEFAULT '0',
  `order_from` int NOT NULL DEFAULT '0',
  `invoice_content` longtext,
  `email_send` int DEFAULT '0',
  `order_updated_times` int NOT NULL DEFAULT '0',
  `order_alt_text` tinytext,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mobile_platform` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_payments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `order_id` bigint NOT NULL,
  `order_from` tinyint NOT NULL DEFAULT (0),
  `shipping_cost` decimal(20,2) DEFAULT '0.00',
  `fuelcharge_percentage` decimal(20,2) DEFAULT '0.00',
  `fuelcharges` decimal(20,2) DEFAULT '0.00',
  `handlingfee` decimal(20,2) DEFAULT '0.00',
  `packaging_fee` decimal(20,2) DEFAULT '0.00',
  `ship_method` int DEFAULT NULL,
  `tax_label` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `tax_percentage` decimal(20,2) DEFAULT '0.00',
  `tax_collected` decimal(20,2) DEFAULT '0.00',
  `payable_amount` decimal(20,2) DEFAULT '0.00',
  `discount_amount` decimal(20,2) DEFAULT '0.00',
  `discount_id` int DEFAULT NULL,
  `pay_method` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `payment_status` tinyint NOT NULL DEFAULT '0' COMMENT '0 is initiated, 1 is completed, 2 is failed, 3 pending',
  `ship_fname` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `ship_lname` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `ship_ads1` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `ship_ads2` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `ship_mobile` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `ship_email` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `ship_city` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `ship_state` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `ship_country` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `ship_zip` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `bill_compname` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `bill_fname` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `bill_lname` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `bill_ads1` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `bill_ads2` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `bill_email` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `bill_mobile` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `bill_city` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `bill_state` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `bill_country` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `bill_zip` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `trans_id` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `error_reason` longtext,
  `delivery_instructions` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `if_items_unavailabel` int DEFAULT '3' COMMENT '1=call, 2= do not replace, 3=Replace',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `order_id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_update_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_update_history` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `order_id` bigint NOT NULL,
  `order_no` mediumtext NOT NULL,
  `order_data` longtext,
  `order_items` longtext,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `paymentlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paymentlog` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `pay_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `received_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `paymentmethods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paymentmethods` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `type` int DEFAULT '1',
  `payment_mode` varchar(10) DEFAULT NULL,
  `payment_name` varchar(50) NOT NULL,
  `testing_url` varchar(255) DEFAULT NULL,
  `test_api_key` varchar(255) DEFAULT NULL,
  `test_api_signature` varchar(255) DEFAULT NULL,
  `live_url` varchar(255) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `test_partner_id` mediumtext,
  `test_partner_secret` mediumtext,
  `test_merchant_id` mediumtext,
  `api_signature` varchar(255) DEFAULT NULL,
  `partner_id` mediumtext,
  `partner_secret` mediumtext,
  `merchant_id` mediumtext,
  `email` varchar(50) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `payment_logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `paymentsettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paymentsettings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paypal_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `paypal_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `paypal_max_amount` int NOT NULL,
  `auth_max_amount` int NOT NULL,
  `currency_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `quotation_expiry_day` int DEFAULT '0',
  `free_shipping_amount` float NOT NULL,
  `local_free_shipping_msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `international_free_shipping_msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `self_collection_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `fuelcharge_percentage` decimal(20,2) DEFAULT '0.00',
  `shipping_cost` float NOT NULL,
  `discount_percentage` int NOT NULL,
  `promo_discount_percentage` int NOT NULL,
  `P_package_fee` float NOT NULL,
  `XXL_package_fee` float NOT NULL,
  `XL_package_fee` float NOT NULL,
  `L_package_fee` float NOT NULL,
  `M_package_fee` float NOT NULL,
  `S_package_fee` float NOT NULL,
  `XS_package_fee` float NOT NULL,
  `XXS_package_fee` float NOT NULL,
  `min_package_fee` float NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `prod_gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prod_gallery` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) DEFAULT NULL,
  `ProdId` int DEFAULT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `LargeImage` varchar(255) DEFAULT NULL,
  `Status` int DEFAULT NULL,
  `DisplayOrder` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_options` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `is_inclusive_tax` tinyint NOT NULL DEFAULT '0',
  `ShippingBox` varchar(50) DEFAULT '0',
  `Weight` decimal(20,3) DEFAULT '0.000',
  `Title` varchar(255) NOT NULL,
  `Prod` int NOT NULL,
  `barcode` text,
  `oldPrice` decimal(20,2) NOT NULL DEFAULT '0.00',
  `Price` decimal(20,2) NOT NULL DEFAULT '0.00',
  `Quantity` int NOT NULL DEFAULT '100',
  `cust_qty_per_day` int NOT NULL DEFAULT '0',
  `Status` int NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`),
  KEY `Group` (`Title`,`Prod`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_prices` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `GroupId` int NOT NULL,
  `Prod` int NOT NULL,
  `Price` decimal(20,2) NOT NULL DEFAULT '0.00',
  `Status` int NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`),
  KEY `Group` (`GroupId`,`Prod`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_reviews` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `ProdId` int NOT NULL DEFAULT '0',
  `CustomerId` int NOT NULL DEFAULT '0',
  `rating` decimal(5,2) NOT NULL DEFAULT '0.00',
  `comments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `is_inclusive_tax` tinyint NOT NULL DEFAULT '0',
  `ProdType` int NOT NULL DEFAULT '1' COMMENT '1=Products,2=Publications',
  `UniqueKey` varchar(255) DEFAULT NULL,
  `EnName` text,
  `SKU` varchar(100) DEFAULT NULL,
  `Code` text,
  `Size` text,
  `EnShortDesc` text,
  `Color` text,
  `Specs` text,
  `StandardPrice` decimal(20,2) DEFAULT '0.00',
  `oldPrice` decimal(20,2) DEFAULT '0.00',
  `Price` decimal(20,2) DEFAULT '0.00',
  `Vendor` text,
  `Supplier` text,
  `Quantity` int DEFAULT NULL,
  `enquiryProduct` int DEFAULT '0',
  `enquiryNotes` text,
  `warningMessage` text,
  `cust_qty_per_day` int NOT NULL DEFAULT '0',
  `ShippingBox` varchar(50) DEFAULT '0',
  `Weight` decimal(20,3) DEFAULT '0.000' COMMENT 'in grams',
  `Dimension` text,
  `MOQ` varchar(100) DEFAULT NULL,
  `unspsc` varchar(100) DEFAULT NULL,
  `barcode` text,
  `gebiz_item` int NOT NULL DEFAULT '0',
  `ProdStatus` int DEFAULT '1',
  `Types` varchar(255) DEFAULT NULL,
  `Brand` int DEFAULT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `LargeImage` varchar(255) DEFAULT NULL,
  `MobileImage` varchar(255) DEFAULT NULL,
  `MobileLargeImage` varchar(255) DEFAULT NULL,
  `Tds` varchar(255) DEFAULT NULL,
  `Sds` varchar(255) DEFAULT NULL,
  `IsFeatured` int DEFAULT '0',
  `IsPromotion` int DEFAULT '0',
  `IsNewarrival` int DEFAULT '0',
  `selfCollection` int DEFAULT '1',
  `localShipping` int DEFAULT '1',
  `IsOverseasShippingTrue` int DEFAULT '1',
  `Video` text,
  `Created` datetime DEFAULT CURRENT_TIMESTAMP,
  `Modified` datetime DEFAULT CURRENT_TIMESTAMP,
  `DisplayOrder` int DEFAULT '0',
  `EnLongDesc` longtext,
  `EnInfo` longtext,
  `MetaTitle` varchar(255) DEFAULT NULL,
  `MetaKey` longtext,
  `MetaDesc` longtext,
  `ProdCode` varchar(255) DEFAULT NULL,
  `ProdTags` varchar(255) DEFAULT NULL,
  `onlineExclusive` int DEFAULT NULL,
  `onlineExclusiveNotes` mediumtext,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `producttag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `producttag` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tagname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotions` (
  `ban_id` int NOT NULL AUTO_INCREMENT,
  `ban_name` varchar(255) DEFAULT NULL,
  `ban_link` varchar(255) NOT NULL,
  `ban_status` int DEFAULT '0',
  `EnBanimage` varchar(255) DEFAULT NULL,
  `EnBanMobileimage` varchar(255) DEFAULT NULL,
  `ban_caption` text,
  `display_order` int DEFAULT NULL,
  `ChBanimage` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ban_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `punchout_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `punchout_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `buyer_cookie` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deployment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser_form_post_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `supplier_setup_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cxml` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_rights` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rights` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `self_collection_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `self_collection_info` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `colection_start_date` date DEFAULT NULL,
  `colection_start_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `colection_to_date` date DEFAULT NULL,
  `colection_to_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_status` tinyint NOT NULL DEFAULT '0',
  `remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `session_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_cart` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `cust_id` bigint NOT NULL,
  `billinginfo` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deliverymethod` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `if_unavailable` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_instructions` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gst` decimal(8,2) DEFAULT '0.00',
  `taxPercentage` decimal(8,2) DEFAULT '0.00',
  `taxtitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taxLabelOnly` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fuelcharge_percentage` decimal(8,2) DEFAULT '0.00',
  `fuelcharges` decimal(8,2) DEFAULT '0.00',
  `handlingfee` decimal(8,2) DEFAULT '0.00',
  `deliverytype` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `packingfee` decimal(8,2) DEFAULT NULL,
  `deliverycost` decimal(8,2) DEFAULT NULL,
  `discount` decimal(8,2) DEFAULT '0.00',
  `discounttext` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `site_path` varchar(255) NOT NULL,
  `download_path` varchar(255) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `company_address` text NOT NULL,
  `company_phone` varchar(20) NOT NULL,
  `company_fax` varchar(20) NOT NULL,
  `GST_res_no` varchar(30) NOT NULL,
  `admin_email` varchar(50) NOT NULL,
  `enquiries_email` varchar(50) NOT NULL,
  `cc_email` varchar(50) NOT NULL,
  `bcc_email` varchar(50) NOT NULL,
  `date_format` varchar(10) NOT NULL,
  `site_title` varchar(100) NOT NULL,
  `database_server` varchar(10) DEFAULT NULL,
  `database_username` varchar(20) DEFAULT NULL,
  `database_password` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `database_name` varchar(50) DEFAULT NULL,
  `notify_min_prod_qty` int NOT NULL,
  `img_files_allowed_ext` varchar(50) NOT NULL,
  `files_allowed_ext` varchar(50) NOT NULL,
  `files_allowed_size` varchar(20) NOT NULL,
  `files_allowed_h` int NOT NULL,
  `files_allowed_w` int NOT NULL,
  `price1` int NOT NULL,
  `price2` int NOT NULL,
  `price3` int NOT NULL,
  `price4` int NOT NULL,
  `noofnews` int NOT NULL,
  `prod_weight` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shipping_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_methods` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Price` decimal(20,2) DEFAULT '0.00',
  `FreeShipAvailable` int NOT NULL DEFAULT '0',
  `FreeShipCost` decimal(20,2) DEFAULT '0.00',
  `EnName` varchar(255) DEFAULT NULL,
  `UniqueKey` varchar(255) DEFAULT NULL,
  `Status` int DEFAULT '1' COMMENT '0=deactive,1=active',
  `DisplayOrder` int DEFAULT '1',
  `shipping_type` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `templatename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `template_type` int DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `socialmedia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `socialmedia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sociallink` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `static_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `static_pages` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `EnTitle` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `Project` int DEFAULT NULL,
  `ChTitle` blob,
  `EnContent` longtext CHARACTER SET latin1 COLLATE latin1_general_ci,
  `ChContent` blob,
  `UniqueKey` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `menu_type` int DEFAULT '1' COMMENT '0=not in menu,1=main manu,2=footer menu',
  `meta_title` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `meta_keywords` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `meta_description` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `parent_id` int DEFAULT '0',
  `style` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `page_link` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `display_order` int NOT NULL DEFAULT '1',
  `banner_type` int NOT NULL DEFAULT '1' COMMENT '1=flash; 2=image',
  `banner_image` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `page_image` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `display_status` int DEFAULT '0',
  `rights_to_visible` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'rights to edit ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `page_title` (`EnTitle`,`parent_id`,`display_status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscriber`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriber` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `ContactNo` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `types` (
  `TypeId` int NOT NULL AUTO_INCREMENT,
  `EnName` varchar(255) DEFAULT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `MobileImage` varchar(255) DEFAULT NULL,
  `UniqueKey` varchar(255) DEFAULT NULL,
  `ParentLevel` int NOT NULL DEFAULT '0',
  `Details` text,
  `dis_type` int DEFAULT '0',
  `discount` int DEFAULT NULL,
  `offerCategory` int NOT NULL DEFAULT '0',
  `meta_title` text,
  `meta_keywords` text,
  `meta_description` text,
  `seo_star_script` text,
  `TypeStatus` int DEFAULT '1' COMMENT '0=deactive,1=active',
  `CreatedDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `DisplayOrder` int DEFAULT '1',
  `ChName` blob,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_logged_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_logged_devices` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `session_id` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_log_off` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2020_10_10_182803_create_admins_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2014_10_12_100000_create_password_resets_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2020_10_13_135607_create_categories_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_10_31_000000_create_tables_missing_from_local_schema',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_10_31_000001_add_columns_missing_from_local_schema',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_10_31_163947_create_personal_access_tokens_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_10_31_000002_create_queue_tables',5);
