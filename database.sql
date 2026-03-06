-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th3 06, 2026 lúc 02:03 AM
-- Phiên bản máy phục vụ: 8.3.0
-- Phiên bản PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `db_testshopquanao`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`id`, `name`, `description`, `logo_url`, `status`, `created_at`, `updated_at`) VALUES
(7, 'Adidas', NULL, 'brands/ZtaWNXYVJT86CdtgTOmcdm9CBv17sk7VU4ph7eG5.jpg', 0, '2026-01-18 20:29:48', '2026-01-18 20:29:48'),
(5, 'Nike', NULL, 'brands/1qNE4vyq0IwJ3pukOKpbuxByyupp2c7vbW1uT6Kq.png', 1, '2025-11-12 09:51:35', '2025-11-16 10:51:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_qNuC8nMtFAGuafUW', 'a:1:{s:11:\"valid_until\";i:1766450080;}', 1767659680),
('laravel_cache_B3EnvCHxDHzSlpp8', 'a:1:{s:11:\"valid_until\";i:1766752562;}', 1767962042),
('laravel_cache_lvbccl138ol3znJE', 'a:1:{s:11:\"valid_until\";i:1766752743;}', 1767962283),
('laravel_cache_d98MIU5XIoCPCFe7', 'a:1:{s:11:\"valid_until\";i:1766752886;}', 1767962426),
('laravel_cache_BD5v0VrmrWHh8cMf', 'a:1:{s:11:\"valid_until\";i:1766753129;}', 1767962729),
('laravel_cache_5WOMhV0lVT58EPvM', 'a:1:{s:11:\"valid_until\";i:1766753260;}', 1767962920),
('laravel_cache_jzcAU97fr4GkaW3z', 'a:1:{s:11:\"valid_until\";i:1766753305;}', 1767962965),
('laravel_cache_vYwwaIwC17imI0fs', 'a:1:{s:11:\"valid_until\";i:1766753324;}', 1767962984),
('laravel_cache_sHB1mlC7uNwGZ6wQ', 'a:1:{s:11:\"valid_until\";i:1766753353;}', 1767963013),
('laravel_cache_9LbCwmidP9TnqBcR', 'a:1:{s:11:\"valid_until\";i:1766753426;}', 1767963086),
('laravel_cache_dbw3inPVFAKKRi6Z', 'a:1:{s:11:\"valid_until\";i:1766753527;}', 1767963187),
('laravel_cache_kvnPLUoaDqpZQPwI', 'a:1:{s:11:\"valid_until\";i:1766753565;}', 1767963225),
('laravel_cache_SBJYkJtBwsb8y7Mg', 'a:1:{s:11:\"valid_until\";i:1766753635;}', 1767963295),
('laravel_cache_UN1tJ5DlEfVdTI7c', 'a:1:{s:11:\"valid_until\";i:1766753709;}', 1767963369),
('laravel_cache_SbV0sGo9DhBx66sl', 'a:1:{s:11:\"valid_until\";i:1766755244;}', 1767964304),
('laravel_cache_602xRohm0uVA42bq', 'a:1:{s:11:\"valid_until\";i:1766755492;}', 1767964972),
('laravel_cache_NwTuMNQ4pQOwDXvt', 'a:1:{s:11:\"valid_until\";i:1766755987;}', 1767965167),
('laravel_cache_J5k3jq6N1l7Ai24J', 'a:1:{s:11:\"valid_until\";i:1766762119;}', 1767971659),
('laravel_cache_sXMrvviBxvwgmJEH', 'a:1:{s:11:\"valid_until\";i:1766762146;}', 1767971806),
('laravel_cache_mfO18KZaUnpO22Yw', 'a:1:{s:11:\"valid_until\";i:1766762168;}', 1767971828),
('laravel_cache_SAuM4vYXN0ehNC3i', 'a:1:{s:11:\"valid_until\";i:1766763278;}', 1767972338),
('laravel_cache_QInVf0bhqgh6V18w', 'a:1:{s:11:\"valid_until\";i:1767084099;}', 1768293759),
('laravel_cache_65L5iZ3ENLw60XDa', 'a:1:{s:11:\"valid_until\";i:1767084420;}', 1768293720),
('laravel_cache_OcH92r7Y5EMuEqLN', 'a:1:{s:11:\"valid_until\";i:1767085388;}', 1768294328),
('laravel_cache_GDqDkTTqi0npx7qs', 'a:1:{s:11:\"valid_until\";i:1767085558;}', 1768295098),
('laravel_cache_4WvaZMMvbCMiAcmt', 'a:1:{s:11:\"valid_until\";i:1767099295;}', 1768308955),
('laravel_cache_zYgDocINbsUKmowh', 'a:1:{s:11:\"valid_until\";i:1767617303;}', 1768826963),
('laravel_cache_hNBKzkWvLIom58Az', 'a:1:{s:11:\"valid_until\";i:1767617469;}', 1768827009),
('laravel_cache_FD7218jcyJN3yAv0', 'a:1:{s:11:\"valid_until\";i:1767619445;}', 1768827185),
('laravel_cache_T6TNhfAdDw0wMKwJ', 'a:1:{s:11:\"valid_until\";i:1767789682;}', 1768999342),
('laravel_cache_S6me0vXmrKi5EoaB', 'a:1:{s:11:\"valid_until\";i:1768017695;}', 1769227235),
('laravel_cache_7gePBKZDqOKLNuHd', 'a:1:{s:11:\"valid_until\";i:1768018029;}', 1769227449),
('laravel_cache_aQPJeNFgAJpJ0aCb', 'a:1:{s:11:\"valid_until\";i:1768739467;}', 1769948407),
('laravel_cache_EISkT5PtQc0tUrVt', 'a:1:{s:11:\"valid_until\";i:1768739913;}', 1769949153),
('laravel_cache_8iY5uOHm8PraTfwI', 'a:1:{s:11:\"valid_until\";i:1768739988;}', 1769949588),
('laravel_cache_Sd05np59NcQvh0rp', 'a:1:{s:11:\"valid_until\";i:1768836826;}', 1770044746),
('laravel_cache_yNZArY14ep8vMW9F', 'a:1:{s:11:\"valid_until\";i:1768836868;}', 1770046528),
('laravel_cache_NfHpqnIDMXYR9zkU', 'a:1:{s:11:\"valid_until\";i:1768837046;}', 1770046586),
('laravel_cache_uhl2zw2kG6GN0rFw', 'a:1:{s:11:\"valid_until\";i:1768838331;}', 1770046731);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

DROP TABLE IF EXISTS `carts`;
CREATE TABLE IF NOT EXISTS `carts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT (now()),
  `updated_at` datetime DEFAULT (now()),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-09-13 20:53:40', '2025-09-13 20:53:40'),
(2, 3, 1, '2025-09-24 19:48:36', '2025-09-24 19:48:36'),
(6, 8, 1, '2025-11-16 11:03:54', '2025-11-16 11:03:54'),
(7, 9, 1, '2025-11-25 20:50:33', '2025-11-25 20:50:33'),
(8, 11, 1, '2025-12-14 08:56:08', '2025-12-14 08:56:08'),
(9, 13, 1, '2026-01-05 19:26:15', '2026-01-05 19:26:15'),
(10, 14, 1, '2026-01-06 09:03:09', '2026-01-06 09:03:09'),
(11, 15, 1, '2026-01-07 19:38:27', '2026-01-07 19:38:27'),
(12, 16, 1, '2026-01-19 22:33:16', '2026-01-19 22:33:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cart_id` int DEFAULT NULL,
  `product_variant_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT (now()),
  `updated_at` datetime DEFAULT (now()),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=141 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_variant_id`, `quantity`, `price`, `total_price`, `created_at`, `updated_at`) VALUES
(140, 6, 126, 1, 250000.00, 250000.00, '2026-01-20 09:57:48', '2026-01-20 09:57:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `colors`
--

DROP TABLE IF EXISTS `colors`;
CREATE TABLE IF NOT EXISTS `colors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hex_code` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `colors`
--

INSERT INTO `colors` (`id`, `name`, `hex_code`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Trắng', '#FFFFFF', 1, '2025-11-11 14:48:58', '2025-11-17 10:54:59'),
(2, 'Xanh navy', '#000080', 1, '2025-11-11 14:48:58', '2025-11-17 10:55:58'),
(6, 'Hồng', '#D4C3CD', 1, '2025-11-22 19:54:13', '2025-11-22 19:55:04'),
(5, 'Đen', '#000000', 1, '2025-11-20 19:41:38', '2025-11-20 19:41:38'),
(7, 'Nâu', '#43201A', 1, '2025-11-22 19:54:56', '2025-11-22 19:54:56'),
(8, 'Cam Brandied', '#D96F40', 1, '2025-11-22 19:55:39', '2025-11-22 19:55:39'),
(9, 'Xám Castle Rock', '#403D46', 1, '2025-11-22 19:56:03', '2025-11-22 19:56:03'),
(10, 'Vàng Sundress', '#F7E5AB', 1, '2025-11-22 20:14:31', '2025-11-22 20:14:31'),
(11, 'Xanh Everglade', '#01615F', 1, '2025-11-22 20:16:00', '2025-11-22 20:16:00'),
(12, 'Xám Granite', '#7A7395', 1, '2025-11-22 20:16:24', '2025-11-22 20:16:24'),
(13, 'Đỏ tươi', '#AD1E24', 1, '2025-11-27 20:28:07', '2025-11-27 20:28:07'),
(14, 'Xanh Estate', '#173663', 1, '2025-11-27 21:11:22', '2025-11-27 21:11:22'),
(15, 'Hồng Sea Pink', '#FD9DC2', 1, '2025-11-27 21:11:39', '2025-11-27 21:11:39'),
(16, 'Màu test', '#F58741', 0, '2025-12-10 22:27:14', '2026-01-18 20:45:12');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000003_create_cache_table', 2),
(3, '0001_01_01_000001_create_users_status_table', 3),
(4, '0001_01_01_000002_create_departments_table', 3),
(5, '0001_01_01_000004_create_jobs_table', 3),
(6, '2025_04_18_075503_alter_users_table', 3),
(7, '2025_06_06_141837_create_products_table', 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province_id` int NOT NULL,
  `district_id` int NOT NULL,
  `ward_id` int NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_note` text COLLATE utf8mb4_unicode_ci,
  `text_custom_couple` text COLLATE utf8mb4_unicode_ci,
  `order_status` enum('pending','confirmed','processing','delivering','delivered','cancelled','returning','returned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `items_total` decimal(10,2) DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT NULL,
  `shipping_discount` decimal(10,2) DEFAULT NULL,
  `promotion_id` int DEFAULT NULL,
  `promotion_discount` decimal(10,2) DEFAULT NULL,
  `total_money` decimal(10,2) DEFAULT NULL,
  `refunded_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `actual_revenue` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('cod','vnpay') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('unpaid','paid','refunded','failed') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vnpay_transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `payment_expires_at` datetime DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `ghn_order_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ghn_sort_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ghn_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ghn_status_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ghn_total_fee` decimal(15,2) DEFAULT NULL,
  `ghn_expected_delivery_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ghn_cod_amount` decimal(15,2) DEFAULT NULL,
  `ghn_last_sync_at` datetime DEFAULT NULL,
  `ghn_log` json DEFAULT NULL,
  `ghn_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `fullname`, `email`, `phone_number`, `province_id`, `district_id`, `ward_id`, `address`, `text_note`, `text_custom_couple`, `order_status`, `items_total`, `shipping_fee`, `shipping_discount`, `promotion_id`, `promotion_discount`, `total_money`, `refunded_amount`, `actual_revenue`, `payment_method`, `payment_status`, `vnpay_transaction_id`, `paid_at`, `payment_expires_at`, `shipped_at`, `delivered_at`, `cancelled_at`, `ghn_order_code`, `ghn_sort_code`, `ghn_status`, `ghn_status_text`, `ghn_total_fee`, `ghn_expected_delivery_time`, `ghn_cod_amount`, `ghn_last_sync_at`, `ghn_log`, `ghn_note`, `created_at`, `updated_at`) VALUES
(39, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 217, 1750, 511101, '27 NTT, Thị trấn Núi Sập, Huyện Thoại Sơn, An Giang', NULL, NULL, 'delivering', 230000.00, 20500.00, NULL, NULL, 0.00, 250500.00, 0.00, 0.00, 'cod', 'unpaid', NULL, NULL, '2025-12-04 19:20:20', NULL, NULL, NULL, 'LKPDQY', '000-H-00-00', 'delivering', NULL, 36501.00, NULL, 250500.00, '2026-01-19 22:06:10', '[{\"status\": \"picking\", \"trip_code\": \"\", \"updated_date\": \"2025-12-02T12:40:28.125Z\", \"payment_type_id\": 2}, {\"status\": \"picked\", \"trip_code\": \"\", \"updated_date\": \"2025-12-02T12:41:22.021Z\", \"payment_type_id\": 2}, {\"status\": \"delivering\", \"trip_code\": \"\", \"updated_date\": \"2025-12-02T13:04:34.694Z\", \"payment_type_id\": 2}]', '', '2025-12-02 19:20:20', '2026-01-19 22:06:10'),
(75, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 202, 1450, 20814, '27 NTT, Phường 14, Quận 8, Hồ Chí Minh', NULL, NULL, 'pending', 230000.00, 22000.00, 0.00, NULL, 0.00, 252000.00, 0.00, 0.00, 'vnpay', 'paid', '15402921', '2026-01-20 09:47:48', '2026-01-21 09:47:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 09:47:10', '2026-01-20 09:47:48'),
(46, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 217, 1755, 510411, '27 NTT, Xã Châu Phong, Thị Xã Tân Châu, An Giang', NULL, NULL, 'returned', 230000.00, 20500.00, NULL, NULL, 0.00, 250500.00, 0.00, 0.00, 'vnpay', 'paid', '15317336', '2025-12-03 15:31:54', '2025-12-05 15:30:18', NULL, NULL, '2025-12-04 20:12:32', 'LKPEAD', '0-000-0-00', 'returned', NULL, 20500.00, NULL, 0.00, '2025-12-05 11:52:59', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2025-12-05T04:38:45.662Z\", \"payment_type_id\": 1}, {\"status\": \"return\", \"trip_code\": \"\", \"updated_date\": \"2025-12-05T04:38:46.416Z\", \"payment_type_id\": 1}, {\"status\": \"returned\", \"trip_code\": \"\", \"updated_date\": \"2025-12-05T04:52:54.716Z\", \"payment_type_id\": 1}]', '', '2025-12-03 15:30:18', '2025-12-05 11:52:59'),
(47, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 268, 2018, 220614, '27 NTT, Xã Thụy Lôi, Huyện Tiên Lữ, Hưng Yên', NULL, NULL, 'delivering', 200000.00, 0.00, NULL, 1, 0.00, 200000.00, 0.00, 0.00, 'cod', 'unpaid', NULL, NULL, '2025-12-05 16:04:08', NULL, NULL, NULL, 'LKBQW8', '207-C-06-00', 'delivering', NULL, 24900.00, NULL, 200000.00, '2026-01-19 22:06:08', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2025-12-03T13:08:22.326Z\", \"payment_type_id\": 2}, {\"status\": \"delivering\", \"trip_code\": \"\", \"updated_date\": \"2025-12-03T13:08:22.741Z\", \"payment_type_id\": 2}]', '', '2025-12-03 16:04:08', '2026-01-19 22:06:08'),
(48, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 269, 2073, 80412, '27 NTT, Xã Thái Niên, Huyện Bảo Thắng, Lào Cai', NULL, NULL, 'delivered', 250000.00, 0.00, 24900.00, 1, 0.00, 250000.00, 0.00, 0.00, 'cod', 'paid', NULL, '2025-12-03 20:27:26', '2025-12-05 19:19:28', NULL, '2025-12-03 20:27:26', NULL, 'LKBCUP', '000-C-00-00', 'delivered', NULL, 24900.00, NULL, 225100.00, '2025-12-03 20:27:26', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2025-12-03T12:52:59.315Z\", \"payment_type_id\": 2}, {\"status\": \"delivering\", \"trip_code\": \"\", \"updated_date\": \"2025-12-03T12:52:59.789Z\", \"payment_type_id\": 2}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2025-12-03T13:27:09.241Z\", \"payment_type_id\": 2}]', '', '2025-12-03 19:19:28', '2025-12-03 20:27:26'),
(49, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 268, 2018, 220613, '27 NTT, Xã Thủ Sỹ, Huyện Tiên Lữ, Hưng Yên', NULL, NULL, 'delivered', 235000.00, 0.00, 24900.00, 1, 0.00, 235000.00, 0.00, 0.00, 'vnpay', 'paid', '15317858', '2025-12-03 19:22:47', '2025-12-05 19:22:18', NULL, '2025-12-03 20:24:23', NULL, 'LKBCCX', '207-C-06-00', 'delivered', NULL, 24900.00, NULL, 0.00, '2025-12-03 20:24:23', '\"[{\\\"status\\\":\\\"picking\\\",\\\"payment_type_id\\\":1,\\\"trip_code\\\":\\\"\\\",\\\"updated_date\\\":\\\"2025-12-03T12:51:37.878Z\\\"},{\\\"status\\\":\\\"picked\\\",\\\"payment_type_id\\\":1,\\\"trip_code\\\":\\\"\\\",\\\"updated_date\\\":\\\"2025-12-03T12:52:02.707Z\\\"},{\\\"status\\\":\\\"delivering\\\",\\\"payment_type_id\\\":1,\\\"trip_code\\\":\\\"\\\",\\\"updated_date\\\":\\\"2025-12-03T12:52:20.123Z\\\"},{\\\"status\\\":\\\"delivered\\\",\\\"payment_type_id\\\":1,\\\"trip_code\\\":\\\"\\\",\\\"updated_date\\\":\\\"2025-12-03T12:52:34.302Z\\\"}]\"', '', '2025-12-03 19:22:18', '2025-12-03 20:24:23'),
(50, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 268, 2018, 220612, '27 NTT, Xã Thiện Phiến, Huyện Tiên Lữ, Hưng Yên', NULL, NULL, 'delivered', 190000.00, 24900.00, 0.00, 2, 19000.00, 195900.00, 0.00, 0.00, 'cod', 'paid', NULL, '2025-12-03 20:24:23', '2025-12-05 19:32:37', NULL, '2025-12-03 20:24:23', NULL, 'LKBCN4', '207-C-06-00', 'delivered', NULL, 24900.00, NULL, 171000.00, '2025-12-03 20:24:23', '\"[{\\\"status\\\":\\\"storing\\\",\\\"payment_type_id\\\":2,\\\"trip_code\\\":\\\"\\\",\\\"updated_date\\\":\\\"2025-12-03T12:45:12.607Z\\\"},{\\\"status\\\":\\\"delivered\\\",\\\"payment_type_id\\\":2,\\\"trip_code\\\":\\\"\\\",\\\"updated_date\\\":\\\"2025-12-03T12:45:13.013Z\\\"}]\"', '', '2025-12-03 19:32:37', '2025-12-03 20:24:23'),
(52, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 266, 2267, 140812, '27 NTT, Xã Sập Vạt, Huyện Yên Châu, Sơn La', NULL, NULL, 'cancelled', 250000.00, 24900.00, 0.00, NULL, 0.00, 274900.00, 0.00, 0.00, 'cod', 'unpaid', NULL, NULL, '2025-12-07 12:00:04', NULL, NULL, NULL, 'LK3FFB', '000-C-00-00', 'ready_to_pick', NULL, 24900.00, NULL, 250000.00, '2025-12-05 12:00:24', NULL, '', '2025-12-05 12:00:04', '2025-12-05 12:03:18'),
(53, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 268, 2046, 220908, '27 NTT, Xã Minh Hải, Huyện Văn Lâm, Hưng Yên', NULL, NULL, 'confirmed', 105000.00, 24900.00, 0.00, NULL, 0.00, 129900.00, 0.00, 0.00, 'vnpay', 'paid', '15324741', '2025-12-07 09:04:14', '2025-12-09 09:03:06', NULL, NULL, NULL, 'LK3EMA', '000-C-00-00', 'ready_to_pick', NULL, 24900.00, NULL, 0.00, '2026-01-17 10:55:18', NULL, '', '2025-12-07 09:03:06', '2026-01-17 10:55:18'),
(54, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 258, 1780, 470316, '27 NTT, Xã Phan Tiến, Huyện Bắc Bình, Bình Thuận', NULL, NULL, 'delivered', 650000.00, 20500.00, 0.00, NULL, 0.00, 670500.00, 0.00, 0.00, 'vnpay', 'paid', '15324782', '2025-12-07 09:55:47', '2025-12-09 09:55:06', NULL, '2025-12-07 09:58:13', NULL, 'LK4QWB', 'E-000-U-00-00', 'delivered', NULL, 20500.00, NULL, 0.00, '2025-12-07 09:58:13', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2025-12-07T02:57:21.681Z\", \"payment_type_id\": 1}, {\"status\": \"delivering\", \"trip_code\": \"\", \"updated_date\": \"2025-12-07T02:57:22.041Z\", \"payment_type_id\": 1}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2025-12-07T02:58:07.55Z\", \"payment_type_id\": 1}]', '', '2025-12-07 09:55:06', '2025-12-07 09:58:13'),
(55, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 269, 2043, 80620, '27 NTT, Xã Tân Thượng, Huyện Văn Bàn, Lào Cai', NULL, 'Nam hehe Nữ huhu', 'delivered', 400000.00, 0.00, 24900.00, 1, 0.00, 400000.00, 0.00, 0.00, 'cod', 'paid', NULL, '2025-12-08 10:40:39', '2025-12-10 09:54:35', NULL, '2025-12-08 10:40:39', NULL, 'LK4486', '000-C-00-00', 'delivered', NULL, 24900.00, NULL, 375100.00, '2025-12-08 10:40:39', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2025-12-08T03:40:17.123Z\", \"payment_type_id\": 2}, {\"status\": \"money_collect_delivering\", \"trip_code\": \"\", \"updated_date\": \"2025-12-08T03:40:17.451Z\", \"payment_type_id\": 2}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2025-12-08T03:40:34.518Z\", \"payment_type_id\": 2}]', '', '2025-12-08 09:54:35', '2025-12-08 10:40:39'),
(56, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 202, 1450, 20807, '27 NTT, Phường 7, Quận 8, Hồ Chí Minh', NULL, NULL, 'delivered', 400000.00, 22000.00, 0.00, NULL, 0.00, 422000.00, 0.00, 0.00, 'cod', 'paid', NULL, '2025-12-09 09:52:29', '2025-12-11 09:51:19', NULL, '2025-12-09 09:52:29', NULL, 'LKKHFM', '750-M-14-00', 'delivered', NULL, 22000.00, NULL, 400000.00, '2025-12-09 09:52:29', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2025-12-09T02:52:11.548Z\", \"payment_type_id\": 2}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2025-12-09T02:52:12.042Z\", \"payment_type_id\": 2}]', '', '2025-12-09 09:51:19', '2025-12-09 09:52:29'),
(57, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 217, 1566, 510110, '27 NTT, Phường Mỹ Thạnh, Thành phố Long Xuyên, An Giang', NULL, NULL, 'cancelled', 400000.00, 20500.00, 0.00, NULL, 0.00, 420500.00, 0.00, 0.00, 'cod', 'unpaid', NULL, NULL, '2025-12-12 20:15:46', NULL, NULL, NULL, 'LKT3DC', '000-H-00-00', 'cancel', NULL, 20500.00, NULL, 400000.00, '2025-12-11 10:27:41', '[{\"status\": \"cancel\", \"trip_code\": \"\", \"updated_date\": \"2025-12-10T13:17:09.899Z\", \"payment_type_id\": 2}]', '', '2025-12-10 20:15:46', '2025-12-11 10:27:41'),
(58, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 217, 1566, 510110, '27 NTT, Phường Mỹ Thạnh, Thành phố Long Xuyên, An Giang', NULL, NULL, 'delivered', 400000.00, 20500.00, 0.00, NULL, 0.00, 420500.00, 0.00, 0.00, 'cod', 'paid', NULL, '2025-12-11 10:27:37', '2025-12-13 10:25:02', NULL, '2025-12-11 10:27:37', NULL, 'LKTW6R', '000-H-00-00', 'delivered', NULL, 20500.00, NULL, 400000.00, '2025-12-11 10:27:37', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2025-12-11T03:27:29.452Z\", \"payment_type_id\": 2}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2025-12-11T03:27:29.97Z\", \"payment_type_id\": 2}]', '', '2025-12-11 10:25:02', '2025-12-11 10:27:37'),
(59, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 202, 1450, 20807, '27 NTT, Phường 7, Quận 8, Hồ Chí Minh', NULL, NULL, 'delivered', 230000.00, 22000.00, 0.00, NULL, 0.00, 252000.00, 252000.00, 0.00, 'cod', 'refunded', NULL, '2025-12-14 20:09:07', '2025-12-16 20:07:13', NULL, '2025-12-14 20:09:07', NULL, 'LKXTAL', '750-M-14-00', 'delivered', NULL, 22000.00, NULL, 230000.00, '2025-12-14 20:09:07', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2025-12-14T13:07:57.952Z\", \"payment_type_id\": 2}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2025-12-14T13:07:58.38Z\", \"payment_type_id\": 2}]', '', '2025-12-14 20:07:13', '2025-12-16 16:21:12'),
(60, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 262, 3279, 370811, '27 NTT, Xã Tây Phú, Huyện Tây Sơn, Bình Định', NULL, NULL, 'delivered', 465000.00, 20500.00, 0.00, NULL, 0.00, 485500.00, 235000.00, 250500.00, 'vnpay', 'paid', '15340782', '2025-12-14 20:21:10', '2025-12-16 20:20:33', NULL, '2025-12-14 20:21:53', NULL, 'LKXTMP', '100-REX-52-00', 'delivered', NULL, 20500.00, NULL, 0.00, '2025-12-15 11:18:02', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2025-12-14T13:21:48.558Z\", \"payment_type_id\": 1}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2025-12-14T13:21:49.112Z\", \"payment_type_id\": 1}]', '', '2025-12-14 20:20:33', '2025-12-15 11:18:02'),
(61, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 266, 2267, 140811, '27 NTT, Xã Phiêng Khoài, Huyện Yên Châu, Sơn La', NULL, NULL, 'delivered', 450000.00, 24900.00, 0.00, NULL, 0.00, 474900.00, 250000.00, 224900.00, 'cod', 'paid', NULL, '2025-12-15 14:40:52', '2025-12-17 14:39:07', NULL, '2025-12-15 14:40:52', NULL, 'LKACRA', '000-C-00-00', 'delivered', NULL, 24900.00, NULL, 450000.00, '2025-12-15 14:40:52', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2025-12-15T07:39:28.728Z\", \"payment_type_id\": 2}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2025-12-15T07:39:29.018Z\", \"payment_type_id\": 2}]', '', '2025-12-15 14:39:07', '2025-12-16 16:21:13'),
(62, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 202, 3695, 90765, '27 NTT, Phường An Phú, Thành Phố Thủ Đức, Hồ Chí Minh', NULL, NULL, 'delivered', 230000.00, 22000.00, 0.00, 2, 23000.00, 229000.00, 229000.00, 0.00, 'vnpay', 'refunded', '15376355', '2025-12-31 09:32:11', '2026-01-02 09:31:38', NULL, '2025-12-31 09:35:45', NULL, 'LW7UYH', '350-M-05-00', 'delivered', NULL, 22000.00, NULL, 0.00, '2025-12-31 10:07:18', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2025-12-31T02:35:17.167Z\", \"payment_type_id\": 1}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2025-12-31T02:35:17.653Z\", \"payment_type_id\": 1}]', '', '2025-12-31 09:31:38', '2025-12-31 10:07:18'),
(63, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 269, 2043, 80619, '27 NTT, Xã Tân An, Huyện Văn Bàn, Lào Cai', NULL, 'hehe', 'delivered', 415000.00, 24900.00, 0.00, NULL, 0.00, 439900.00, 0.00, 0.00, 'cod', 'paid', NULL, '2025-12-31 10:09:17', '2026-01-02 10:08:10', NULL, '2025-12-31 10:09:17', NULL, 'LW7C4A', '000-C-00-00', 'delivered', NULL, 24900.00, NULL, 415000.00, '2025-12-31 10:09:17', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2025-12-31T03:09:11.046Z\", \"payment_type_id\": 2}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2025-12-31T03:09:11.552Z\", \"payment_type_id\": 2}]', '', '2025-12-31 10:08:10', '2025-12-31 10:09:17'),
(64, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 202, 1533, 22014, '27 NTT, Xã Tân Quý Tây, Huyện Bình Chánh, Hồ Chí Minh', NULL, NULL, 'delivered', 435000.00, 22000.00, 0.00, NULL, 0.00, 457000.00, 205000.00, 252000.00, 'vnpay', 'paid', '15376662', '2025-12-31 13:09:25', '2026-01-02 13:08:32', NULL, '2025-12-31 13:11:37', NULL, 'LW7DK8', 'FT-M-102-00', 'delivered', NULL, 22000.00, NULL, 0.00, '2025-12-31 13:11:37', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2025-12-31T06:11:30.602Z\", \"payment_type_id\": 1}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2025-12-31T06:11:31.098Z\", \"payment_type_id\": 1}]', '', '2025-12-31 13:08:32', '2025-12-31 13:14:19'),
(65, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 266, 2079, 140513, '27 NTT, Xã Song Pe, Huyện Bắc Yên, Sơn La', NULL, NULL, 'delivered', 255000.00, 24900.00, 0.00, NULL, 0.00, 279900.00, 279900.00, 0.00, 'cod', 'refunded', NULL, '2026-01-03 15:37:16', '2026-01-05 15:36:38', NULL, '2026-01-03 15:37:16', NULL, 'LWUVGY', '000-C-00-00', 'delivered', NULL, 24900.00, NULL, 255000.00, '2026-01-03 15:37:16', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2026-01-03T08:37:09.491Z\", \"payment_type_id\": 2}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2026-01-03T08:37:09.985Z\", \"payment_type_id\": 2}]', '', '2026-01-03 15:36:38', '2026-01-03 15:38:21'),
(66, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 269, 1892, 80817, '27 NTT, Xã Nậm Mòn, Huyện Bắc Hà, Lào Cai', NULL, NULL, 'delivered', 255000.00, 24900.00, 0.00, NULL, 0.00, 279900.00, 279900.00, 0.00, 'cod', 'refunded', NULL, '2026-01-03 15:56:01', '2026-01-05 15:55:34', NULL, '2026-01-03 15:56:01', NULL, 'LWUVP7', '000-C-00-00', 'delivered', NULL, 24900.00, NULL, 255000.00, '2026-01-03 15:56:01', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2026-01-03T08:56:00.762Z\", \"payment_type_id\": 2}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2026-01-03T08:56:01.229Z\", \"payment_type_id\": 2}]', '', '2026-01-03 15:55:34', '2026-01-03 15:56:46'),
(67, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 267, 2156, 230526, '27 NTT, Xã Vũ Lâm, Huyện Lạc Sơn, Hòa Bình', NULL, NULL, 'delivered', 255000.00, 24900.00, 0.00, NULL, 0.00, 279900.00, 279900.00, 0.00, 'cod', 'refunded', NULL, '2026-01-03 19:41:54', '2026-01-05 19:40:10', NULL, '2026-01-03 19:41:54', NULL, 'LWUPLU', '000-C-00-00', 'delivered', NULL, 24900.00, NULL, 255000.00, '2026-01-03 19:41:54', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2026-01-03T12:41:03.675Z\", \"payment_type_id\": 2}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2026-01-03T12:41:04.192Z\", \"payment_type_id\": 2}]', '', '2026-01-03 19:40:10', '2026-01-03 19:42:44'),
(68, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 224, 1822, 31229, '27 NTT, Xã Vinh Quang, Huyện Vĩnh Bảo, Hải Phòng', NULL, NULL, 'delivered', 1440000.00, 32100.00, 0.00, NULL, 0.00, 1472100.00, 720000.00, 752100.00, 'cod', 'paid', NULL, '2026-01-03 20:09:52', '2026-01-05 20:09:24', NULL, '2026-01-03 20:09:52', NULL, 'LWUP3B', '209-C-07-00', 'delivered', NULL, 32100.00, NULL, 1440000.00, '2026-01-04 19:58:14', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2026-01-03T13:09:51.52Z\", \"payment_type_id\": 2}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2026-01-03T13:09:52Z\", \"payment_type_id\": 2}]', '', '2026-01-03 20:09:24', '2026-01-04 19:58:14'),
(69, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 252, 2038, 610411, '27 NTT, Xã Phong Điền, Huyện Trần Văn Thời, Cà Mau', NULL, NULL, 'cancelled', 250000.00, 20500.00, 0.00, NULL, 0.00, 270500.00, 0.00, 0.00, 'cod', 'unpaid', NULL, NULL, '2026-01-10 20:45:05', NULL, NULL, '2026-01-08 21:05:43', 'LWV9MW', 'F-000-W-00-00', NULL, NULL, 20500.00, '2026-01-10T16:59:59Z', NULL, NULL, NULL, NULL, '2026-01-08 20:45:05', '2026-01-08 21:05:43'),
(70, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 217, 1750, 511101, '27 NTT, Thị trấn Núi Sập, Huyện Thoại Sơn, An Giang', NULL, NULL, 'delivered', 190000.00, 20500.00, 0.00, NULL, 0.00, 210500.00, 0.00, 0.00, 'vnpay', 'paid', '15392592', '2026-01-12 20:42:28', '2026-01-13 20:38:38', NULL, '2026-01-13 10:13:10', NULL, 'LWDCLH', '000-H-00-00', 'delivered', NULL, 20500.00, NULL, 0.00, '2026-01-13 10:13:10', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2026-01-13T03:12:47.492Z\", \"payment_type_id\": 1}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2026-01-13T03:12:47.97Z\", \"payment_type_id\": 1}]', '', '2026-01-12 20:38:38', '2026-01-13 10:13:10'),
(71, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 217, 1566, 510110, '27 NTT, Phường Mỹ Thạnh, Thành phố Long Xuyên, An Giang', NULL, NULL, 'delivered', 250000.00, 20500.00, 0.00, NULL, 0.00, 270500.00, 270500.00, 0.00, 'cod', 'refunded', NULL, '2026-01-17 10:56:18', '2026-01-17 20:52:00', NULL, '2026-01-17 10:56:18', NULL, 'LWBHWK', '000-H-00-00', 'delivered', NULL, 20500.00, NULL, 250000.00, '2026-01-17 10:56:18', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2026-01-17T03:56:13.255Z\", \"payment_type_id\": 2}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2026-01-17T03:56:13.727Z\", \"payment_type_id\": 2}]', '', '2026-01-16 20:52:00', '2026-01-20 09:51:54'),
(72, 8, 'Trinh Phat Dat', 'trinhphatdat@gmail.com', '0987070245', 217, 1750, 511101, '27 NTT, Thị trấn Núi Sập, Huyện Thoại Sơn, An Giang', NULL, 'S- haha, M-hihi', 'delivered', 405000.00, 20500.00, 0.00, NULL, 0.00, 425500.00, 425500.00, 0.00, 'cod', 'refunded', NULL, '2026-01-17 10:56:00', '2026-01-17 20:57:37', NULL, '2026-01-17 10:56:00', NULL, 'LWBHAF', '000-H-00-00', 'delivered', NULL, 20500.00, NULL, 405000.00, '2026-01-17 10:56:00', '[{\"status\": \"storing\", \"trip_code\": \"\", \"updated_date\": \"2026-01-17T03:55:55.451Z\", \"payment_type_id\": 2}, {\"status\": \"delivered\", \"trip_code\": \"\", \"updated_date\": \"2026-01-17T03:55:55.83Z\", \"payment_type_id\": 2}]', '', '2026-01-16 20:57:37', '2026-01-17 11:04:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

DROP TABLE IF EXISTS `order_details`;
CREATE TABLE IF NOT EXISTS `order_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `product_variant_id` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_variant_id`, `price`, `quantity`, `total_price`, `created_at`, `updated_at`) VALUES
(11, 7, 68, 99000.00, 1, 99000.00, '2025-11-17 21:15:39', '2025-11-17 21:15:39'),
(10, 7, 66, 100000.00, 1, 100000.00, '2025-11-17 21:15:39', '2025-11-17 21:15:39'),
(9, 7, 69, 105000.00, 1, 105000.00, '2025-11-17 21:15:39', '2025-11-17 21:15:39'),
(8, 7, 70, 110000.00, 1, 110000.00, '2025-11-17 21:15:39', '2025-11-17 21:15:39'),
(60, 52, 126, 250000.00, 1, 250000.00, '2025-12-05 12:00:04', '2025-12-05 12:00:04'),
(91, 75, 120, 230000.00, 1, 230000.00, '2026-01-20 09:47:10', '2026-01-20 09:47:10'),
(54, 46, 123, 230000.00, 1, 230000.00, '2025-12-03 15:30:18', '2025-12-03 15:30:18'),
(56, 48, 126, 250000.00, 1, 250000.00, '2025-12-03 19:19:29', '2025-12-03 19:19:29'),
(55, 47, 99, 200000.00, 1, 200000.00, '2025-12-03 16:04:08', '2025-12-03 16:04:08'),
(57, 49, 121, 235000.00, 1, 235000.00, '2025-12-03 19:22:18', '2025-12-03 19:22:18'),
(47, 39, 120, 230000.00, 1, 230000.00, '2025-12-02 19:20:20', '2025-12-02 19:20:20'),
(58, 50, 84, 190000.00, 1, 190000.00, '2025-12-03 19:32:37', '2025-12-03 19:32:37'),
(61, 53, 69, 105000.00, 1, 105000.00, '2025-12-07 09:03:06', '2025-12-07 09:03:06'),
(62, 54, 126, 250000.00, 1, 250000.00, '2025-12-07 09:55:06', '2025-12-07 09:55:06'),
(63, 54, 90, 200000.00, 2, 400000.00, '2025-12-07 09:55:06', '2025-12-07 09:55:06'),
(64, 55, 117, 200000.00, 2, 400000.00, '2025-12-08 09:54:35', '2025-12-08 09:54:35'),
(65, 56, 78, 200000.00, 2, 400000.00, '2025-12-09 09:51:19', '2025-12-09 09:51:19'),
(66, 57, 78, 200000.00, 2, 400000.00, '2025-12-10 20:15:46', '2025-12-10 20:15:46'),
(67, 58, 78, 200000.00, 2, 400000.00, '2025-12-11 10:25:02', '2025-12-11 10:25:02'),
(68, 59, 120, 230000.00, 1, 230000.00, '2025-12-14 20:07:13', '2025-12-14 20:07:13'),
(69, 60, 124, 235000.00, 1, 235000.00, '2025-12-14 20:20:33', '2025-12-14 20:20:33'),
(70, 60, 120, 230000.00, 1, 230000.00, '2025-12-14 20:20:33', '2025-12-14 20:20:33'),
(71, 61, 129, 250000.00, 1, 250000.00, '2025-12-15 14:39:07', '2025-12-15 14:39:07'),
(72, 61, 90, 200000.00, 1, 200000.00, '2025-12-15 14:39:07', '2025-12-15 14:39:07'),
(73, 62, 120, 230000.00, 1, 230000.00, '2025-12-31 09:31:38', '2025-12-31 09:31:38'),
(74, 63, 115, 210000.00, 1, 210000.00, '2025-12-31 10:08:10', '2025-12-31 10:08:10'),
(75, 63, 114, 205000.00, 1, 205000.00, '2025-12-31 10:08:10', '2025-12-31 10:08:10'),
(76, 64, 100, 205000.00, 1, 205000.00, '2025-12-31 13:08:32', '2025-12-31 13:08:32'),
(77, 64, 120, 230000.00, 1, 230000.00, '2025-12-31 13:08:32', '2025-12-31 13:08:32'),
(78, 65, 127, 255000.00, 1, 255000.00, '2026-01-03 15:36:38', '2026-01-03 15:36:38'),
(79, 66, 127, 255000.00, 1, 255000.00, '2026-01-03 15:55:34', '2026-01-03 15:55:34'),
(80, 67, 127, 255000.00, 1, 255000.00, '2026-01-03 19:40:10', '2026-01-03 19:40:10'),
(81, 68, 120, 230000.00, 4, 920000.00, '2026-01-03 20:09:24', '2026-01-03 20:09:24'),
(82, 68, 128, 260000.00, 2, 520000.00, '2026-01-03 20:09:24', '2026-01-03 20:09:24'),
(83, 69, 126, 250000.00, 1, 250000.00, '2026-01-08 20:45:05', '2026-01-08 20:45:05'),
(84, 70, 84, 190000.00, 1, 190000.00, '2026-01-12 20:38:38', '2026-01-12 20:38:38'),
(85, 71, 126, 250000.00, 1, 250000.00, '2026-01-16 20:52:00', '2026-01-16 20:52:00'),
(86, 72, 118, 205000.00, 1, 205000.00, '2026-01-16 20:57:37', '2026-01-16 20:57:37'),
(87, 72, 117, 200000.00, 1, 200000.00, '2026-01-16 20:57:37', '2026-01-16 20:57:37');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `brand_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `product_type` enum('male','female','couple') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `material` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_price` decimal(10,2) NOT NULL,
  `max_price` decimal(10,2) NOT NULL,
  `has_discount` tinyint(1) NOT NULL,
  `max_discount` int NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT (now()),
  `updated_at` datetime DEFAULT (now()),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `brand_id`, `title`, `thumbnail`, `description`, `product_type`, `material`, `min_price`, `max_price`, `has_discount`, `max_discount`, `status`, `created_at`, `updated_at`) VALUES
(9, 3, 'Áo thun nữ relax CHẠM', 'products/thumbnails/yPxila1B87onJLhFuBX5FZiivjt9HCpbdYDxj6xc.jpg', NULL, 'female', 'Cotton 100%', 200000.00, 210000.00, 0, 0, 1, '2025-11-22 19:59:38', '2025-11-22 19:59:38'),
(8, 5, 'Áo thun nữ đơn giản', 'products/thumbnails/kh4J3ywv9UDVQiBBtPJhyj8ARJIshgCoWDIGoX7t.jpg', NULL, 'female', 'Cotton 97%', 100000.00, 110000.00, 1, 5, 1, '2025-11-20 19:49:21', '2025-11-22 19:27:07'),
(7, 3, 'Áo thun nam đơn giản', 'products/thumbnails/HU3cse9OtbkYRg1mGyjiLtA2rQxqvmNbflS5nmLB.png', NULL, 'male', 'Cotton 100%', 99000.00, 115000.00, 1, 10, 1, '2025-11-17 10:57:24', '2025-11-21 19:55:22'),
(10, 3, 'Áo Thun Nam Must Be Loved', 'products/thumbnails/DhGPwnPPFz4qW1SIllAjSkKBUa4l0K0GzdHGTeRU.jpg', NULL, 'male', '86.6% cotton và 13.4% polyester', 180000.00, 200000.00, 0, 0, 1, '2025-11-22 20:46:32', '2025-11-22 20:46:32'),
(11, 5, 'Áo Thun Nam Ép Nhung Jazz', 'products/thumbnails/cEj9ZoBoBZxTiCFVZxwimA5H9hNKwa5bs0xuPPy5.jpg', NULL, 'male', 'Cotton 100%', 200000.00, 210000.00, 0, 0, 1, '2025-11-22 21:09:17', '2025-11-22 21:09:17'),
(12, 3, 'Áo Thun Nữ Green X Typo Go Outside', 'products/thumbnails/nZeKUIY0k5W015SX8qc7k5m1RrOlUTH6F18BiVBW.jpg', NULL, 'female', '79,36% Cotton và 20,64% Iscra-S', 200000.00, 210000.00, 0, 0, 1, '2025-11-22 21:12:55', '2025-11-22 21:12:55'),
(20, 5, 'Áo thun cặp ISH', 'products/thumbnails/er5HUzCAq4VAzf0kMhjxTBfIejWMNS12JnbdjU5V.jpg', NULL, 'couple', 'Cotton 100%', 200000.00, 210000.00, 0, 0, 1, '2025-11-27 20:31:17', '2025-11-27 20:31:17'),
(19, 5, 'Áo thun cặp MLOBF', 'products/thumbnails/mcHZn61Ge3WAyh2HhuKjU0LSFcawZ5rxowi21RrM.jpg', NULL, 'couple', 'Cotton 100%', 205000.00, 215000.00, 0, 0, 1, '2025-11-27 20:29:49', '2025-11-27 22:44:08'),
(18, 5, 'Áo thun cặp ONE-LOVE', 'products/thumbnails/FwFNi6dozx3JlASsbzrPjzk1hcDtHTjpHRAa6m2m.jpg', NULL, 'couple', 'Cotton 100%', 200000.00, 210000.00, 0, 0, 1, '2025-11-27 20:04:29', '2025-11-27 20:04:29'),
(17, 5, 'Áo thun cặp YMMBF', 'products/thumbnails/kuj1Tg0ZK65dAWsdAML22nS1CDn8wfVpUKlhuWDX.jpg', NULL, 'couple', 'Cotton 100%', 200000.00, 210000.00, 0, 0, 1, '2025-11-27 20:00:12', '2025-11-27 20:00:12'),
(21, 5, 'Áo Thun Nam Regular Typo Gain', 'products/thumbnails/ghQATLytf3Xo5QbAfokiQto480zIwHOXnAo3OUje.jpg', NULL, 'male', 'Cotton 100%', 230000.00, 240000.00, 0, 0, 1, '2025-11-27 21:16:56', '2026-01-16 22:49:52'),
(22, 3, 'Áo Thun Nữ Relax Healing Nature', 'products/thumbnails/OsCjGp8PPS1y4DVRsGC5WHZrDwzX7FznzzOZQmfd.jpg', NULL, 'female', 'Cotton 100%', 250000.00, 260000.00, 0, 0, 1, '2025-11-27 21:21:40', '2026-01-16 22:42:03'),
(28, 5, 'sản phẩm test 2', 'products/thumbnails/ee6o9dslchCXGWwiFBzYi9qFt0BgerklAaSY2Kif.png', NULL, 'male', 'vải test', 20000.00, 20000.00, 0, 0, 0, '2026-01-05 20:52:25', '2026-01-05 20:52:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
CREATE TABLE IF NOT EXISTS `product_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `product_variant_id` int DEFAULT NULL,
  `rating` tinyint DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `user_id`, `product_id`, `product_variant_id`, `rating`, `comment`, `status`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, 8, 7, NULL, 4, 'Sản phẩm tốt', 1, NULL, '2025-11-21 15:34:19', '2025-11-21 15:34:19'),
(2, 8, 7, 69, 5, 'áo đẹp', 1, NULL, '2025-11-21 15:35:40', '2025-11-21 15:35:40'),
(3, 8, 7, NULL, 1, 'áo không giống như mô tả', 1, NULL, '2025-12-10 20:25:19', '2025-12-10 20:25:19'),
(4, 8, 21, 120, 4, 'tốt', 1, NULL, '2025-12-31 13:16:13', '2025-12-31 13:16:13'),
(5, 8, 21, NULL, 5, 'sản phẩm này đẹp quá', 1, NULL, '2025-12-31 13:48:10', '2025-12-31 13:48:10'),
(6, 8, 21, NULL, 3, 'sản phẩm này đẹp quá', 1, NULL, '2025-12-31 13:48:33', '2025-12-31 13:48:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE IF NOT EXISTS `product_variants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `size_id` int DEFAULT NULL,
  `color_id` int NOT NULL,
  `stock` int DEFAULT NULL,
  `defective_stock` int NOT NULL DEFAULT '0',
  `original_price` decimal(10,2) NOT NULL,
  `discount` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT (now()),
  `updated_at` datetime DEFAULT (now()),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `size_id` (`size_id`)
) ENGINE=MyISAM AUTO_INCREMENT=138 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `size_id`, `color_id`, `stock`, `defective_stock`, `original_price`, `discount`, `price`, `image_url`, `status`, `created_at`, `updated_at`) VALUES
(70, 7, 2, 2, 5, 0, 110000.00, 0, 110000.00, 'products/variants/aKs1d63icw2H5YIJWHstHAdogx77DwVJAnzif6Po.png', 1, '2025-11-17 11:05:18', '2025-11-24 19:30:25'),
(68, 7, 3, 1, 5, 0, 110000.00, 10, 99000.00, 'products/variants/4JsfwMa3rkOlsu9XaTQSUSd3eMoGL2TmXOMBfs8p.png', 1, '2025-11-17 10:57:24', '2025-11-21 19:55:22'),
(67, 7, 2, 1, 7, 0, 105000.00, 5, 99750.00, 'products/variants/PXKpCJl4YRnztZg0ut7AB1wyyEKzSR6I9OK3SN28.png', 1, '2025-11-17 10:57:24', '2025-11-26 20:33:00'),
(69, 7, 1, 2, 4, 0, 105000.00, 0, 105000.00, 'products/variants/sANTmB3xrutFEQsZahnW3MGk1BSZuDUqiRX2pqOI.png', 1, '2025-11-17 11:05:18', '2025-12-07 09:03:06'),
(66, 7, 1, 1, 5, 0, 100000.00, 0, 100000.00, 'products/variants/stk756Wj17DYVJ413Y2ShrlZzZfW2oyYDOF03TJC.png', 1, '2025-11-17 10:57:24', '2025-11-21 19:55:22'),
(71, 7, 3, 2, 5, 0, 115000.00, 0, 115000.00, 'products/variants/h0YX48SqzgchmOd7Ub8nkkRnBB8Q05gLhjAICmYQ.png', 1, '2025-11-17 11:05:18', '2025-11-21 19:55:22'),
(72, 8, 1, 5, 5, 0, 100000.00, 0, 100000.00, 'products/variants/wuGln7dLSNULI2yoZUfEjqgMgc3QkHnkq2zDke4L.png', 1, '2025-11-20 19:49:21', '2025-11-26 20:48:08'),
(73, 8, 2, 5, 5, 0, 105000.00, 0, 105000.00, 'products/variants/fRINlAw1kSSH79QpIBs6oASchKcmNoVNSANrbATl.png', 1, '2025-11-20 19:49:21', '2025-11-22 19:27:07'),
(74, 8, 3, 5, 5, 0, 110000.00, 0, 110000.00, 'products/variants/k8LIVj8JCN3AzzrtI7PbuFpxUfBjPHb72aJQggRv.png', 1, '2025-11-20 19:49:21', '2025-11-22 19:27:07'),
(75, 8, 1, 1, 5, 0, 100000.00, 0, 100000.00, 'products/variants/80d3LfRQX7fvO4xndyS9P6Ck7GtlQeIREdmj83GS.png', 1, '2025-11-20 19:50:30', '2025-11-22 19:27:07'),
(76, 8, 2, 1, 5, 0, 105000.00, 0, 105000.00, 'products/variants/NLZsL5RosUjObFcv3mRxS5IEAEgAgr6OUGTVwbRA.png', 1, '2025-11-20 19:50:30', '2025-11-22 19:27:07'),
(77, 8, 3, 1, 5, 0, 110000.00, 5, 104500.00, 'products/variants/tTRdLCSI8XbXDwIrR9AwpghJMzKaUi78NZHw2JdO.jpg', 1, '2025-11-20 19:58:05', '2025-11-22 19:27:07'),
(78, 9, 1, 6, 6, 0, 200000.00, 0, 200000.00, 'products/variants/TUR58vGaZrEpDBzNIXAcBCfmlVsDGdFJoktmRClQ.jpg', 1, '2025-11-22 19:59:38', '2025-12-11 10:25:02'),
(79, 9, 2, 6, 10, 0, 205000.00, 0, 205000.00, 'products/variants/wAUW2qnn9kdMt4Eq1Ws153nrMx5NhE3a5kzA7VRV.jpg', 1, '2025-11-22 19:59:38', '2025-11-22 19:59:38'),
(80, 9, 3, 6, 10, 0, 210000.00, 0, 210000.00, 'products/variants/Tq4cQ0TTz2AjdKZyMyAx5bbRvup2ExPISZKPthag.jpg', 1, '2025-11-22 19:59:38', '2025-11-22 19:59:38'),
(81, 9, 1, 7, 10, 0, 200000.00, 0, 200000.00, 'products/variants/0BbYCBYfg7qzsnircUsjPBwBgIGw1RRErNnfv315.jpg', 1, '2025-11-22 19:59:38', '2025-11-22 19:59:38'),
(82, 9, 2, 7, 10, 0, 205000.00, 0, 205000.00, 'products/variants/b19nEGsQh2HYlNnlySF2ZO1OMo1kaFOmsicVuUWN.jpg', 1, '2025-11-22 19:59:38', '2025-11-22 19:59:38'),
(83, 9, 3, 7, 10, 0, 210000.00, 0, 210000.00, 'products/variants/7qa3wUYuZOjHLBPafGpbAiuv0W7W1ahYBevkB3CJ.jpg', 1, '2025-11-22 19:59:38', '2025-11-22 19:59:38'),
(84, 10, 1, 10, 8, 0, 190000.00, 0, 190000.00, 'products/variants/0AIAb1Haensdk2YKWIByZPRnvMWYJmPgNyzAAv8U.jpg', 1, '2025-11-22 20:46:32', '2026-01-12 20:38:38'),
(85, 10, 2, 10, 10, 0, 195000.00, 0, 195000.00, 'products/variants/CDu7nfmjyoPv6eybvWrZ9d6Fteq9Jwi6Nk6EpDpW.jpg', 1, '2025-11-22 20:46:32', '2025-11-22 20:46:32'),
(86, 10, 3, 10, 10, 0, 200000.00, 0, 200000.00, 'products/variants/c4xitMd3h6NvNBXtB7K8zChb9a3ydlmvMEef4QH9.jpg', 1, '2025-11-22 20:46:32', '2025-11-22 20:46:32'),
(87, 10, 1, 1, 10, 0, 180000.00, 0, 180000.00, 'products/variants/G522ydMP42otz8qW6q8En50hzBIziX6pFItzRONb.jpg', 1, '2025-11-22 20:46:32', '2025-11-22 20:46:32'),
(88, 10, 2, 1, 10, 0, 185000.00, 0, 185000.00, 'products/variants/jB5FMepx8UJuKgmcYQVm8GcNeq5NVlsGqrgtDSj8.jpg', 1, '2025-11-22 20:46:32', '2025-11-22 20:46:32'),
(89, 10, 3, 1, 10, 0, 190000.00, 0, 190000.00, 'products/variants/tpskrEzvJBxKfe4EAXkj4lx9kuzQGR06fddFVLyP.jpg', 1, '2025-11-22 20:46:32', '2025-11-22 20:46:32'),
(90, 11, 1, 11, 7, 0, 200000.00, 0, 200000.00, 'products/variants/v6gYSt3q1yt9cBDLPQHu9IBiMR93WwZd4zTOqHDN.jpg', 1, '2025-11-22 21:09:17', '2025-12-15 14:39:07'),
(91, 11, 2, 11, 10, 0, 205000.00, 0, 205000.00, 'products/variants/ct4pt2wGhE08lamHIPx1g9iOfjtOievT82oZQkff.jpg', 1, '2025-11-22 21:09:17', '2025-11-26 22:40:20'),
(92, 11, 3, 11, 10, 0, 210000.00, 0, 210000.00, 'products/variants/7Cq0f9QcJK2RBjBkZKa6XsGDDuAb9BdLYfr3OmND.jpg', 1, '2025-11-22 21:09:17', '2025-11-22 21:09:17'),
(93, 11, 1, 12, 10, 0, 200000.00, 0, 200000.00, 'products/variants/sPC1CDvvpIicr0dI7zKS5awAYHY95R0ydDRpmIIc.jpg', 1, '2025-11-22 21:09:17', '2025-12-03 10:57:34'),
(94, 11, 2, 12, 10, 0, 205000.00, 0, 205000.00, 'products/variants/oV4hGvZYUDYbGgMnHaU1KzIYPlv9k6KBOn4WjMR1.jpg', 1, '2025-11-22 21:09:17', '2025-11-22 21:09:17'),
(95, 11, 3, 12, 10, 0, 210000.00, 0, 210000.00, 'products/variants/NgsIdrOsbK1guILfPvP9DNnlj7OLWxjYHuMgM7DX.jpg', 1, '2025-11-22 21:09:17', '2025-11-22 21:09:17'),
(96, 12, 1, 8, 11, 0, 200000.00, 0, 200000.00, 'products/variants/hbUAJz4Zl2XN8JU7qz58U1e9urAwD2k8IJAOtTNU.jpg', 1, '2025-11-22 21:12:55', '2026-01-19 22:05:42'),
(97, 12, 2, 8, 10, 0, 205000.00, 0, 205000.00, 'products/variants/B3P9XAik3UAx03c5rYznQ79T0X0NaqiHiQ7Z2i1D.jpg', 1, '2025-11-22 21:12:55', '2025-11-26 22:40:28'),
(98, 12, 3, 8, 10, 0, 210000.00, 0, 210000.00, 'products/variants/9fXpstP0LjeJVprGnbZzkMnQXFqfm7Od4pGAT9OM.jpg', 1, '2025-11-22 21:12:55', '2025-11-22 21:12:55'),
(99, 12, 1, 9, 9, 0, 200000.00, 0, 200000.00, 'products/variants/UBrmDEGgFyKoY3TRDPc5sdw9H4ZwtuKRREv6nj0o.jpg', 1, '2025-11-22 21:12:55', '2025-12-03 16:04:08'),
(100, 12, 2, 9, 9, 0, 205000.00, 0, 205000.00, 'products/variants/kQ0eqSlEnNzWHAtNPbr3zrj7QdPyCQHs6sX1uX8T.jpg', 1, '2025-11-22 21:12:55', '2025-12-31 13:08:32'),
(101, 12, 3, 9, 10, 0, 210000.00, 0, 210000.00, 'products/variants/60Md20RPQ5082k2eCgC9UKBQROmR91dkmkTatwZn.jpg', 1, '2025-11-22 21:12:55', '2025-11-22 21:12:55'),
(102, 13, 1, 5, 6, 0, 250000.00, 0, 250000.00, 'products/variants/6mkFH92WqUbyaMnU474Nbsu4fd85REImqFpQ3N4x.png', 1, '2025-11-25 09:32:49', '2025-11-26 20:21:36'),
(103, 13, 2, 5, 6, 0, 250000.00, 0, 250000.00, 'products/variants/fQdNIhLKmMmINWGmQt3G6CWmof1j8aGEH0R2ZhCY.png', 1, '2025-11-25 09:32:49', '2025-11-25 09:32:49'),
(104, 13, 3, 5, 6, 0, 255000.00, 0, 255000.00, 'products/variants/1p06CtSfcLRCsWCRbzHjnPbtbym1F7e8niEhY08P.png', 1, '2025-11-25 09:32:49', '2025-11-26 20:21:36'),
(105, 16, 1, 1, 10, 0, 200000.00, 0, 200000.00, 'products/variants/iJelEvfc9KNXg4uYWEf2JrFHUYlU0t9b78OqDT3f.png', 1, '2025-11-27 19:36:51', '2025-11-27 19:36:51'),
(106, 16, 2, 1, 10, 0, 205000.00, 0, 205000.00, 'products/variants/NffG7raYYjPuaPh1ts1ZkRgow1Z1F3NKndupTBlB.png', 1, '2025-11-27 19:36:51', '2025-11-27 19:36:51'),
(107, 16, 3, 1, 10, 0, 210000.00, 0, 210000.00, 'products/variants/zVtMm9tASTDMZEMDZOJDszAeC93Tg3l9RrfUQO3z.png', 1, '2025-11-27 19:36:51', '2025-11-27 19:36:51'),
(108, 17, 1, 1, 10, 0, 200000.00, 0, 200000.00, 'products/variants/cvHqNJP7BYIiiItASO9iK3ycvrZxJnZk4wZrHfj4.jpg', 1, '2025-11-27 20:00:12', '2025-11-27 20:00:12'),
(109, 17, 2, 1, 9, 0, 205000.00, 0, 205000.00, 'products/variants/NcnVXNm5UP06kYWKeMuUHXXKnpRf6mAmwDPL8Q5N.jpg', 1, '2025-11-27 20:00:12', '2026-01-19 22:43:24'),
(110, 17, 3, 1, 9, 0, 210000.00, 0, 210000.00, 'products/variants/ngLyZ5Bjxd9Gmc9GayobpOYih8KwCVJG81VyvHCu.jpg', 1, '2025-11-27 20:00:12', '2026-01-19 22:43:24'),
(111, 18, 1, 5, 10, 0, 200000.00, 0, 200000.00, 'products/variants/sxrcClcds0uOQj9Ed2otScGe8k6J2Q46YmnxHOJY.jpg', 1, '2025-11-27 20:04:29', '2025-11-27 20:04:29'),
(112, 18, 2, 5, 10, 0, 205000.00, 0, 205000.00, 'products/variants/2Gllol4XEjJIZHeGmjfE9YoWVlExVFTHWp8s4kVz.jpg', 1, '2025-11-27 20:04:29', '2025-11-27 20:04:29'),
(113, 18, 3, 5, 10, 0, 210000.00, 0, 210000.00, 'products/variants/jmBmAG9D9NBYZpu8MglSfWTVAd2o3OBmjnijPugo.jpg', 1, '2025-11-27 20:04:29', '2025-11-27 20:04:29'),
(114, 19, 1, 13, 9, 0, 205000.00, 0, 205000.00, 'products/variants/2xxDldV9gSll4QUezlagaPYMgBfRaHQFAAIce6mH.jpg', 1, '2025-11-27 20:29:49', '2025-12-31 10:08:10'),
(115, 19, 2, 13, 9, 0, 210000.00, 0, 210000.00, 'products/variants/OTeJRbXBxhuS6IDmTvawkOyWrITpmwxjHPZ9vaRD.jpg', 1, '2025-11-27 20:29:49', '2025-12-31 10:08:10'),
(116, 19, 3, 13, 10, 0, 215000.00, 0, 215000.00, 'products/variants/ET9ydvB1UvzMmh9Wi9N2nPpjtyI4sT64hs9W13ni.jpg', 1, '2025-11-27 20:29:49', '2025-11-27 22:44:08'),
(117, 20, 1, 5, 7, 1, 200000.00, 0, 200000.00, 'products/variants/GBZRHyLlM64BvNd8sSxR8xIeuFz5e9Przc8oiGcv.jpg', 1, '2025-11-27 20:31:17', '2026-01-17 11:04:20'),
(118, 20, 2, 5, 9, 1, 205000.00, 0, 205000.00, 'products/variants/GUZNcOMw5iiAJxu3bt5CFAMSXu17Km0Zn6lGPFOg.jpg', 1, '2025-11-27 20:31:17', '2026-01-17 11:04:20'),
(119, 20, 3, 5, 10, 0, 210000.00, 0, 210000.00, 'products/variants/iQ9uLEXfPw67nVpKuBajM3G6EVHJCrMFrG0YdSLu.jpg', 1, '2025-11-27 20:31:17', '2025-12-02 20:02:54'),
(120, 21, 1, 15, 2, 0, 230000.00, 0, 230000.00, 'products/variants/Ro1ptkg2jHiZPWZeFi3Owy8mAQpnjd1z7KHtLkT1.jpg', 1, '2025-11-27 21:16:56', '2026-01-20 09:47:10'),
(121, 21, 2, 15, 9, 0, 235000.00, 0, 235000.00, 'products/variants/hakGguOlbf71th4Qj4yehNwXwSbN99fV1DVaYvbU.jpg', 1, '2025-11-27 21:16:56', '2026-01-16 22:49:52'),
(122, 21, 3, 15, 10, 0, 240000.00, 0, 240000.00, 'products/variants/whOYMAAY1rxvNNqucfxWvpadpvyjpRDlKuHzACto.jpg', 1, '2025-11-27 21:16:56', '2026-01-16 22:49:52'),
(123, 21, 1, 14, 13, 0, 230000.00, 0, 230000.00, 'products/variants/DKEFasyi1DxHXBt6THXVyiuDNMU6qi6vuipHtrz1.jpg', 1, '2025-11-27 21:16:56', '2026-01-16 22:49:52'),
(124, 21, 2, 14, 9, 0, 235000.00, 0, 235000.00, 'products/variants/a8uMhDUAQKgujxquMG2EDKf24OgCoQRS2MQiKeib.jpg', 1, '2025-11-27 21:16:56', '2026-01-16 22:49:52'),
(125, 21, 3, 14, 10, 0, 240000.00, 0, 240000.00, 'products/variants/ykF4Ycc8t1eNlG9dZ2yzUy2WKwyvdRJUkDWVSN4D.jpg', 1, '2025-11-27 21:16:56', '2026-01-16 22:49:52'),
(126, 22, 1, 11, 15, 1, 250000.00, 0, 250000.00, 'products/variants/W3sEAZbd2DlWUy8nJJZGZRZHUbdAnVXD5msCrchO.jpg', 1, '2025-11-27 21:21:40', '2026-01-20 09:51:44'),
(127, 22, 2, 11, 8, 0, 255000.00, 0, 255000.00, 'products/variants/kcjlskNath1eJXHj4o9GeZDsGgyql0HHnehot4Pj.jpg', 1, '2025-11-27 21:21:40', '2026-01-16 22:42:03'),
(128, 22, 3, 11, 9, 0, 260000.00, 0, 260000.00, 'products/variants/K11G5NbSDootbpuN9JSJYixq8olpvpakdm4r76HD.jpg', 1, '2025-11-27 21:21:40', '2026-01-16 22:42:03'),
(129, 22, 1, 12, 9, 0, 250000.00, 0, 250000.00, 'products/variants/hwreqpVLHr5s8IPH09dksNeBdBfkKYtRmwqwNehW.jpg', 1, '2025-11-27 21:21:40', '2026-01-16 22:42:03'),
(130, 22, 2, 12, 10, 0, 255000.00, 0, 255000.00, 'products/variants/VWwt6Z7pBqMcCtGyji1fXlb1UeYhNjFSbgRuUaem.jpg', 1, '2025-11-27 21:21:40', '2026-01-16 22:42:03'),
(131, 22, 3, 12, 10, 0, 260000.00, 0, 260000.00, 'products/variants/NfHBz3IAUGWdBConFD7wQIQR7OBnKn2JgdDIrslz.jpg', 1, '2025-11-27 21:21:40', '2026-01-16 22:42:03'),
(136, 27, 2, 1, 2, 0, 20000.00, 0, 20000.00, 'products/variants/B1MY2p0zZw2nkAIAmbzYLPujoDw7VD7vZ2XJP0ss.png', 0, '2026-01-05 20:50:16', '2026-01-05 20:51:00'),
(137, 28, 2, 1, 2, 0, 20000.00, 0, 20000.00, 'products/variants/odNQosO4utCyRJIuCzz2t4DYtqddka8ekoEOJfDU.png', 0, '2026-01-05 20:52:25', '2026-01-05 20:52:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotions`
--

DROP TABLE IF EXISTS `promotions`;
CREATE TABLE IF NOT EXISTS `promotions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `discount_type` enum('percentage','fixed_amount','free_shipping') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `min_order_value` decimal(10,2) DEFAULT NULL,
  `usage_limit` int DEFAULT NULL,
  `used_count` int DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `promotions`
--

INSERT INTO `promotions` (`id`, `code`, `name`, `url_image`, `description`, `discount_type`, `discount_value`, `min_order_value`, `usage_limit`, `used_count`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'freeship123', 'Miễn phí vận chuyển', 'promotions/OcXSDUy8LFXhEQi9fmmOnqfYmpyNign5y7aeCxZU.jpg', NULL, 'free_shipping', 0.00, 150000.00, 10, 7, '2026-01-01 15:55:34', '2026-01-31 15:55:34', 1, '2025-11-12 15:55:54', '2026-01-19 22:43:24'),
(2, 'discount10', 'Giảm 10% cho đơn hàng trên 200k', 'promotions/YWOsuObBBIXKFMzLrikJFEW7U1dE2pkSQDgMFl17.jpg', NULL, 'percentage', 10.00, 0.00, 10, 2, '2026-01-01 20:12:44', '2026-01-31 20:12:44', 1, '2025-11-12 20:14:27', '2026-01-04 20:36:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `return_requests`
--

DROP TABLE IF EXISTS `return_requests`;
CREATE TABLE IF NOT EXISTS `return_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `return_type` enum('full','partial') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` enum('defective','wrong_item','not_as_described','size_issue','quality_issue','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected','shipping','received','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `refund_status` enum('pending','completed','failed') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `admin_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `return_requests`
--

INSERT INTO `return_requests` (`id`, `order_id`, `user_id`, `return_type`, `reason`, `status`, `refund_amount`, `refund_status`, `bank_name`, `bank_account_number`, `bank_account_name`, `custom_note`, `admin_note`, `admin_id`, `created_at`, `updated_at`, `approved_at`, `rejected_at`, `received_at`, `refunded_at`) VALUES
(22, 71, 8, 'full', 'defective', 'refunded', 270500.00, 'completed', 'MB BANK', '0987070241', 'TRINH PHAT DAT', NULL, NULL, 3, '2026-01-20 09:49:56', '2026-01-20 09:51:54', '2026-01-20 09:51:09', NULL, '2026-01-20 09:51:44', '2026-01-20 09:51:54'),
(5, 54, 8, 'full', 'quality_issue', 'refunded', 670500.00, 'completed', 'MB Bank', '0987070241', 'TRINH PHAT DAT', NULL, 'hàng ổn', 8, '2025-12-08 14:39:52', '2025-12-08 15:08:45', '2025-12-08 15:03:48', NULL, '2025-12-08 15:08:36', '2025-12-08 15:08:45'),
(6, 55, 8, 'full', 'defective', 'refunded', 400000.00, 'completed', 'MB BAnk', '0987070241', 'TRINH PHAT DAT', 'Áo rách', NULL, 8, '2025-12-08 20:09:18', '2025-12-08 20:18:51', '2025-12-08 20:18:46', NULL, '2025-12-08 20:18:48', '2025-12-08 20:18:51'),
(7, 56, 8, 'partial', 'quality_issue', 'refunded', 200000.00, 'completed', 'MB Bank', '0987070241', 'TRINH PHAT DAT', 'có 1 áo bị bung chỉ', 'ok', 8, '2025-12-09 09:55:07', '2025-12-09 10:18:55', '2025-12-09 10:18:43', NULL, '2025-12-09 10:18:52', '2025-12-09 10:18:55'),
(10, 60, 8, 'partial', 'not_as_described', 'refunded', 235000.00, 'completed', 'MB BAnk', '0987070241', 'TRINH PHAT DAT', 'giao sai mẫu', 'ok', 8, '2025-12-14 20:22:53', '2025-12-14 20:24:47', '2025-12-14 20:24:08', NULL, '2025-12-14 20:24:36', '2025-12-14 20:24:47'),
(9, 58, 8, 'full', 'defective', 'refunded', 420500.00, 'completed', 'MB Bank', '0987070241', 'TRINH PHAT DAT', 'Áo rách', NULL, 8, '2025-12-11 10:49:11', '2025-12-11 11:04:19', '2025-12-11 11:02:34', NULL, '2025-12-11 11:03:17', '2025-12-11 11:04:19'),
(11, 61, 8, 'full', 'quality_issue', 'rejected', 474900.00, 'failed', 'MBBANK', '0987070241', 'TRINH PHAT DAT', NULL, 'thích', 8, '2025-12-15 14:41:34', '2025-12-15 14:42:18', NULL, '2025-12-15 14:42:18', NULL, NULL),
(12, 61, 8, 'partial', 'size_issue', 'refunded', 250000.00, 'completed', 'MB Bank', '0987070241', 'TRINH PHAT DAT', NULL, NULL, 8, '2025-12-15 14:59:52', '2025-12-16 16:21:13', '2025-12-16 16:20:18', NULL, '2025-12-16 16:20:28', '2025-12-16 16:21:13'),
(13, 59, 8, 'full', 'wrong_item', 'refunded', 252000.00, 'completed', 'MB BANK', '0987070241', 'TRINH PHAT DAT', NULL, NULL, 8, '2025-12-15 15:03:23', '2025-12-16 16:21:12', '2025-12-16 16:20:16', NULL, '2025-12-16 16:20:26', '2025-12-16 16:21:12'),
(14, 62, 8, 'full', 'quality_issue', 'refunded', 229000.00, 'completed', 'MB Bank', '0987070241', 'TRINH PHAT DAT', NULL, NULL, 3, '2025-12-31 09:36:23', '2025-12-31 09:49:47', '2025-12-31 09:49:17', NULL, '2025-12-31 09:49:42', '2025-12-31 09:49:47'),
(15, 63, 8, 'full', 'wrong_item', 'rejected', 439900.00, 'failed', 'MB BANK', '0987070241', 'TRINH PHAT DAT', NULL, 'shop giao đúng', 3, '2025-12-31 10:18:25', '2025-12-31 10:19:17', NULL, '2025-12-31 10:19:17', NULL, NULL),
(16, 64, 8, 'partial', 'defective', 'refunded', 205000.00, 'completed', 'MB BANK', '0987070241', 'TRINH PHAT DAT', NULL, NULL, 3, '2025-12-31 13:12:56', '2025-12-31 13:14:19', '2025-12-31 13:13:32', NULL, '2025-12-31 13:14:13', '2025-12-31 13:14:19'),
(17, 65, 8, 'full', 'wrong_item', 'refunded', 279900.00, 'completed', 'MB BANK', '0987070241', 'TRINH PHAT DAT', NULL, NULL, 8, '2026-01-03 15:38:08', '2026-01-03 15:38:21', '2026-01-03 15:38:17', NULL, '2026-01-03 15:38:19', '2026-01-03 15:38:21'),
(18, 66, 8, 'full', 'wrong_item', 'refunded', 279900.00, 'completed', 'MB BANK', '0987070241', 'TRINH PHAT DAT', NULL, NULL, 8, '2026-01-03 15:56:30', '2026-01-03 15:56:46', '2026-01-03 15:56:42', NULL, '2026-01-03 15:56:44', '2026-01-03 15:56:46'),
(19, 67, 8, 'full', 'defective', 'refunded', 279900.00, 'completed', 'MB BANK', '0987070241', 'TRINH PHAT DAT', NULL, NULL, 8, '2026-01-03 19:42:30', '2026-01-03 19:42:44', '2026-01-03 19:42:40', NULL, '2026-01-03 19:42:42', '2026-01-03 19:42:44'),
(20, 68, 8, 'partial', 'quality_issue', 'refunded', 720000.00, 'completed', 'MB BANK', '0987070241', 'TRINH PHAT DAT', NULL, NULL, 8, '2026-01-03 20:11:17', '2026-01-03 20:11:29', '2026-01-03 20:11:26', NULL, '2026-01-03 20:11:28', '2026-01-03 20:11:29'),
(21, 72, 8, 'full', 'defective', 'refunded', 425500.00, 'completed', 'MB BANK', '0987070241', 'TRINH PHAT DAT', NULL, NULL, 8, '2026-01-17 11:03:51', '2026-01-17 11:04:22', '2026-01-17 11:04:18', NULL, '2026-01-17 11:04:20', '2026-01-17 11:04:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `return_request_images`
--

DROP TABLE IF EXISTS `return_request_images`;
CREATE TABLE IF NOT EXISTS `return_request_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `return_request_id` int DEFAULT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `return_request_images`
--

INSERT INTO `return_request_images` (`id`, `return_request_id`, `image_url`, `description`, `created_at`, `updated_at`) VALUES
(3, 6, 'return_requests/s9G8ZC9fJ5UG6EGH3HlYzSnFsAd0AzYDS97MJzNU.jpg', NULL, '2025-12-08 20:09:18', '2025-12-08 20:09:18'),
(2, 4, 'return_requests/ZbbsDsv3641m71lFxImmNPI5jRMsWkrphDEU5j5i.jpg', NULL, '2025-12-04 16:13:53', '2025-12-04 16:13:53'),
(4, 7, 'return_requests/ix93Av5v85oXSiF5zxkL1Wad29IYTZ0a5Dxy3VLk.jpg', NULL, '2025-12-09 09:55:07', '2025-12-09 09:55:07'),
(5, 8, 'return_requests/qwuDnhcbzyr6LtPiUXu0e19P8Lgk3U5l4hDW49bt.jpg', NULL, '2025-12-11 10:38:06', '2025-12-11 10:38:06'),
(6, 9, 'return_requests/3bP4IewIMlZWs4Pxzc159JxEtiuBXkY5cVJpsuZe.jpg', NULL, '2025-12-11 10:49:11', '2025-12-11 10:49:11'),
(7, 10, 'return_requests/9udgXcFK7YnfJJoMeX0IxfY9XCDW3ZcQZFW37Q42.jpg', NULL, '2025-12-14 20:22:53', '2025-12-14 20:22:53'),
(8, 11, 'return_requests/l78Ahtp4mLtwHuAZqUE4heGTEBQHbK9F8gmBQN4z.jpg', NULL, '2025-12-15 14:41:34', '2025-12-15 14:41:34'),
(9, 12, 'return_requests/LWitr4PbqXZ0LKtA8V1WJm8KYtPIhedlyJuL6gnr.jpg', NULL, '2025-12-15 14:59:52', '2025-12-15 14:59:52'),
(10, 13, 'return_requests/pjFfU1QeKmyzvOkyBomvOEb04XPhOzhMfEObztDi.jpg', NULL, '2025-12-15 15:03:23', '2025-12-15 15:03:23'),
(11, 14, 'return_requests/p1bfxwuBkBgyDGCvuAtZeVWxwKxaxoKg0CtVjyf0.jpg', NULL, '2025-12-31 09:36:24', '2025-12-31 09:36:24'),
(12, 15, 'return_requests/mUJMgD1rDCmTF3iiUr4Wdf0PFtL4fdXT2s2cD1Y5.jpg', NULL, '2025-12-31 10:18:25', '2025-12-31 10:18:25'),
(13, 16, 'return_requests/GohtrRQ4FOZTse3JJudfnJrrhOWPzAlEgfgHAg1L.jpg', NULL, '2025-12-31 13:12:57', '2025-12-31 13:12:57'),
(14, 17, 'return_requests/BGiQv5gBlRKqPfSyPWXSi56oW4lgshzTniJj88nN.jpg', NULL, '2026-01-03 15:38:09', '2026-01-03 15:38:09'),
(15, 18, 'return_requests/zfozsw4B9lOUBcfcL6N5ErjYJMSNIOA2ibTfjSXg.jpg', NULL, '2026-01-03 15:56:30', '2026-01-03 15:56:30'),
(16, 19, 'return_requests/VkFhslIkC7W1Xv0ZPnjttNOdwNDr7flEZJK2qqox.jpg', NULL, '2026-01-03 19:42:30', '2026-01-03 19:42:30'),
(17, 20, 'return_requests/WyZpbMTQCoDGfe4oqlAKiSTQ2mcXPNizVdo7n38m.jpg', NULL, '2026-01-03 20:11:17', '2026-01-03 20:11:17'),
(18, 21, 'return_requests/IwQ7C5XtCnFPQWUbesuMlh0vk9noHhNsmS0Invvn.jpg', NULL, '2026-01-17 11:03:52', '2026-01-17 11:03:52'),
(19, 22, 'return_requests/lcf5zcMa1Gc6uzSwHoOQSb8b8JtXZ8ykTvOMefk9.jpg', NULL, '2026-01-20 09:49:58', '2026-01-20 09:49:58');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `return_request_items`
--

DROP TABLE IF EXISTS `return_request_items`;
CREATE TABLE IF NOT EXISTS `return_request_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `return_request_id` int DEFAULT NULL,
  `order_detail_id` int DEFAULT NULL,
  `product_variant_id` int DEFAULT NULL,
  `ordered_quantity` int DEFAULT NULL,
  `return_quantity` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `return_request_items`
--

INSERT INTO `return_request_items` (`id`, `return_request_id`, `order_detail_id`, `product_variant_id`, `ordered_quantity`, `return_quantity`, `price`, `refund_amount`, `created_at`, `updated_at`) VALUES
(1, 6, 64, 117, 2, 2, 200000.00, 400000.00, '2025-12-08 20:09:18', '2025-12-08 20:09:18'),
(2, 7, 65, 78, 2, 1, 200000.00, 200000.00, '2025-12-09 09:55:07', '2025-12-09 09:55:07'),
(3, 8, 67, 78, 2, 2, 200000.00, 400000.00, '2025-12-11 10:38:06', '2025-12-11 10:38:06'),
(4, 9, 67, 78, 2, 2, 200000.00, 400000.00, '2025-12-11 10:49:11', '2025-12-11 10:49:11'),
(5, 10, 69, 124, 1, 1, 235000.00, 235000.00, '2025-12-14 20:22:53', '2025-12-14 20:22:53'),
(6, 11, 71, 129, 1, 1, 250000.00, 250000.00, '2025-12-15 14:41:34', '2025-12-15 14:41:34'),
(7, 11, 72, 90, 1, 1, 200000.00, 200000.00, '2025-12-15 14:41:34', '2025-12-15 14:41:34'),
(8, 12, 71, 129, 1, 1, 250000.00, 250000.00, '2025-12-15 14:59:52', '2025-12-15 14:59:52'),
(9, 13, 68, 120, 1, 1, 230000.00, 230000.00, '2025-12-15 15:03:23', '2025-12-15 15:03:23'),
(10, 14, 73, 120, 1, 1, 230000.00, 230000.00, '2025-12-31 09:36:23', '2025-12-31 09:36:23'),
(11, 15, 74, 115, 1, 1, 210000.00, 210000.00, '2025-12-31 10:18:25', '2025-12-31 10:18:25'),
(12, 15, 75, 114, 1, 1, 205000.00, 205000.00, '2025-12-31 10:18:25', '2025-12-31 10:18:25'),
(13, 16, 76, 100, 1, 1, 205000.00, 205000.00, '2025-12-31 13:12:56', '2025-12-31 13:12:56'),
(14, 17, 78, 127, 1, 1, 255000.00, 255000.00, '2026-01-03 15:38:08', '2026-01-03 15:38:08'),
(15, 18, 79, 127, 1, 1, 255000.00, 255000.00, '2026-01-03 15:56:30', '2026-01-03 15:56:30'),
(16, 19, 80, 127, 1, 1, 255000.00, 255000.00, '2026-01-03 19:42:30', '2026-01-03 19:42:30'),
(17, 20, 81, 120, 4, 2, 230000.00, 460000.00, '2026-01-03 20:11:17', '2026-01-03 20:11:17'),
(18, 20, 82, 128, 2, 1, 260000.00, 260000.00, '2026-01-03 20:11:17', '2026-01-03 20:11:17'),
(19, 21, 86, 118, 1, 1, 205000.00, 205000.00, '2026-01-17 11:03:51', '2026-01-17 11:03:51'),
(20, 21, 87, 117, 1, 1, 200000.00, 200000.00, '2026-01-17 11:03:51', '2026-01-17 11:03:51'),
(21, 22, 85, 126, 1, 1, 250000.00, 250000.00, '2026-01-20 09:49:56', '2026-01-20 09:49:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT (now()),
  `updated_at` datetime DEFAULT (now()),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 1, '0000-00-00 00:00:00', '2025-11-10 23:26:12'),
(2, 'Nhân viên', 1, '0000-00-00 00:00:00', '2025-11-10 23:26:28'),
(3, 'Khách hàng', 1, '0000-00-00 00:00:00', '2025-11-10 23:26:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('9g7wAyqEhLaOG4fjEKSFQW58UPegeJQDJtfabkji', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSkVKcTk5dGw1RE92WHNPNjZtQTAzeFpFWUJjMWsydGVwNWFaZ2hmRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1762094284),
('vERbwpHjYEoeeInFOK0WpDQmKnSAfpQCC4MNhBoS', 8, '127.0.0.1', 'PostmanRuntime/7.49.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaUF4SzRSeTlCUEtad2tDSHJybFhGNjNEYkdyR3BhOVJsZFg5aWQyQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1764165195);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipping`
--

DROP TABLE IF EXISTS `shipping`;
CREATE TABLE IF NOT EXISTS `shipping` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT (now()),
  `updated_at` datetime DEFAULT (now()),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sizes`
--

DROP TABLE IF EXISTS `sizes`;
CREATE TABLE IF NOT EXISTS `sizes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `length` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `width` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sleeve` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sizes`
--

INSERT INTO `sizes` (`id`, `name`, `length`, `width`, `sleeve`, `order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'S', '66', '52', '17', 1, 1, '2025-11-11 10:00:08', '2025-11-24 19:16:09'),
(2, 'M', '68', '54', '18', 2, 1, '2025-11-11 10:01:24', '2025-11-24 19:16:25'),
(3, 'L', '70', '56', '19', 3, 1, '2025-11-11 10:31:54', '2026-01-18 20:43:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_id` int DEFAULT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT (now()),
  `updated_at` datetime DEFAULT (now()),
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `role_id`, `fullname`, `phone_number`, `email`, `password`, `remember_token`, `address`, `status`, `created_at`, `updated_at`) VALUES
(3, 1, 'Trịnh Phát Đạt', '0987070241', 'tpd@gmail.com', '$2y$12$vhrWVD8A1iRimh/i5peNzeuXDIToCs75Ov2Z83grMrvk8W9Efr5tO', NULL, '27 Nguyễn Thiện Thuật', 1, '2025-09-24 19:48:36', '2025-09-24 19:48:36'),
(8, 3, 'Trinh Phat Dat', '0987070245', 'trinhphatdat@gmail.com', '$2y$12$Qqep9iy1ixu6CAOns1gaBOeJ1laU6NMQarLAxMgdC5Q2LgkIJASz.', NULL, '27 NTT', 1, '2025-11-16 11:03:54', '2025-11-21 20:35:32'),
(9, 3, 'Nguyễn Tấn Đạt', '0987456243', 'ntd@gmail.com', '$2y$12$6GxU.xQmpxf/YX/nbLn4KuurVY2RLcC2sxQwJ4pkhTfrh1AghOud.', NULL, '67 Nguyễn Huệ', 0, '2025-11-25 20:50:33', '2025-12-30 19:44:36'),
(12, 2, 'test test test 123', '0845975263', 'test@gmail.com', '$2y$12$ztjzLPCENIdbj0HjuNfQ4Ocz69RNO5lrGfmoi/DC9rTEMg9D2kWDG', NULL, 'SG', 1, '2025-12-30 19:47:56', '2026-01-05 21:38:06'),
(13, 3, 'Nguyen Mau An', '0586748593', 'nguyenmauan@gmail.com', '$2y$12$GAAsSXojEO/2ai2md3IJGeSpzwHMAT.b5ZLXwxnXalgnHuAV90D46', NULL, 'SG', 1, '2026-01-05 19:26:15', '2026-01-05 19:26:15'),
(14, 3, 'Nguyen Mau An', '0586748592', 'nguyenmauann@gmail.com', '$2y$12$UxgwESlzIKM9wtu2m6LbMOqvD4rM2eHbYB79sJydQJB8vXA6YLatu', NULL, 'SG', 1, '2026-01-06 09:03:09', '2026-01-06 09:03:09'),
(15, 3, 'Nguyen Tan Dat', '0875984574', 'tandat8503@gmail.com', '$2y$12$1PrmtBtUcTdVleGHyDsFiuodoguCU.GfijcydaeZ.z60hGfaeXGFu', 'ybQ2f8GTv3qOGmsWtGdP1cT8LgvRNl9NTZfkxnFJgFr3MeoR0LPIWwRg6lCQ', 'Tây Ninh', 1, '2026-01-07 19:38:27', '2026-01-07 19:40:30'),
(16, 3, 'Trịnh Phát Đạt 3', '0854756253', 'dattrinh1922@gmail.com', '$2y$12$0ps3qJj2aRNfz0Il9Hi1NOGh3COQOeBxjcfkqdR1xRXE4zg1v6Mzi', NULL, '1 Nguyễn Huệ', 1, '2026-01-19 22:33:16', '2026-01-19 22:33:16');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
