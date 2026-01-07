CREATE TABLE IF NOT EXISTS `organizational_positions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `level` int(11) NOT NULL COMMENT 'Hierarchy level: 0=High Command, 1=Department, etc',
  `level_key` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'level_0, level_1, etc for styling',
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Self-referential FK',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Position title: CEO, Department Head, etc',
  `position_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Full position name or department name',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'FK to users table',
  `display_order` int(11) NOT NULL DEFAULT '0' COMMENT 'Display order within same level',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `metadata` json DEFAULT NULL COMMENT 'Extra data: icon, color, description, etc',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_level_order` (`level`,`display_order`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_active` (`is_active`),
  KEY `organizational_positions_user_id_foreign` (`user_id`),
  CONSTRAINT `organizational_positions_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `organizational_positions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `organizational_positions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert migration record
INSERT INTO migrations (migration, batch) VALUES ('2026_01_07_103206_create_organizational_positions_table', (SELECT IFNULL(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) AS m));
