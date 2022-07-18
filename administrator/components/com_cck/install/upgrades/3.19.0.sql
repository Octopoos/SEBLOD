
ALTER TABLE `#__cck_core_fields` CHANGE `storage_params` `storage_mode` TINYINT(3) NOT NULL DEFAULT '0';
ALTER TABLE `#__cck_core_sites` CHANGE `groups` `usergroups` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
