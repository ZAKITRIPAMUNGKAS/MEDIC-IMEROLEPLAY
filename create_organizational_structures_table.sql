-- Manual SQL to create organizational_structures table
-- Run this in your MySQL database if migrations fail

CREATE TABLE IF NOT EXISTS `organizational_structures` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `structure_data` json NOT NULL COMMENT 'Stores the entire hierarchy structure',
  `required_names` json DEFAULT NULL COMMENT 'Stores the required names list',
  `hospital_type` enum('ems','roxwood') NOT NULL DEFAULT 'ems',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL COMMENT 'Optional name/label for the structure',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
