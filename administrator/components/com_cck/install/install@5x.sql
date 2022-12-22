
CREATE TABLE IF NOT EXISTS `#__cck_store_item_content` (
  `id` int(10) unsigned NOT NULL,
  `archived_mode` tinyint(3) NOT NULL DEFAULT 0,
  `aliases` text NOT NULL,
  `meta_desc` text NOT NULL,
  `meta_desc_auto` text NOT NULL,
  `meta_title` text NOT NULL,
  `meta_title_auto` text NOT NULL,
  `page_titles` text NOT NULL,
  `snippets` text NOT NULL,
  `texts` text NOT NULL,
  `titles` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cck_store_item_categories` (
  `id` int(10) unsigned NOT NULL,
  `archived_mode` tinyint(3) NOT NULL DEFAULT 0,
  `aliases` text NOT NULL,
  `meta_desc` text NOT NULL,
  `meta_desc_auto` text NOT NULL,
  `nav_items` text NOT NULL,
  `meta_title` text NOT NULL,
  `meta_title_auto` text NOT NULL,
  `page_titles` text NOT NULL,
  `snippets` text NOT NULL,
  `texts` text NOT NULL,
  `titles` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cck_store_item_language` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` tinyint(3) NOT NULL DEFAULT 0,
  `access_live` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__cck_store_item_language` (`id`, `type`, `access_live`) VALUES
(1, 0, 1);

CREATE TABLE IF NOT EXISTS `#__cck_store_item_menu` (
  `id` int(10) unsigned NOT NULL,
  `item_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `item_request` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `children_type` tinyint(3) NOT NULL DEFAULT 0,
  `children_content_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cck_store_item_menu_types` (
  `id` int(10) unsigned NOT NULL,
  `list_type` tinyint(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cck_store_item_usergroups` (
  `id` int(10) unsigned NOT NULL,
  `visibility_admin` tinyint(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 0,
  `visibility_manager` tinyint(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cck_store_item_viewlevels` (
  `id` int(10) unsigned NOT NULL,
  `access` int(11) NOT NULL DEFAULT 1,
  `content_types` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cck_store_join_o_nav_list_nav_items` (
  `id` int(11) NOT NULL,
  `id2` int(11) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
