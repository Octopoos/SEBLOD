
ALTER TABLE `#__cck_core_downloads` ADD `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' AFTER `hits`;
ALTER TABLE `#__cck_core_downloads` ADD `upload_date` datetime DEFAULT NULL AFTER `token`;