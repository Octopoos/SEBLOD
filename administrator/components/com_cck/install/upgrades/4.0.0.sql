app
UPDATE `#__modules` SET `published` = 0 WHERE `module` IN ("mod_cck_menu","mod_cck_quickadd");

--

ALTER TABLE `#__cck_core_types` CHANGE `alias` `relationships` VARCHAR(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__cck_core_types` CHANGE `parent_inherit` `parent_inherit` TINYINT(3) NOT NULL DEFAULT '0';
			
ALTER TABLE `#__cck_core_fields` CHANGE `cols` `cols` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__cck_core_fields` CHANGE `rows` `rows` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__cck_core_fields` CHANGE `bool` `bool` TINYINT(4) NOT NULL DEFAULT '0';

ALTER TABLE `#__cck_core_searchs` CHANGE `template_search` `template_search` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__cck_core_searchs` CHANGE `template_filter` `template_filter` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__cck_core_searchs` CHANGE `template_list` `template_list` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__cck_core_searchs` CHANGE `template_item` `template_item` INT(11) NOT NULL DEFAULT '0';

--

ALTER TABLE `#__cck_core` CHANGE `pk` `pk` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__cck_core` CHANGE `pkb` `pkb` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__cck_core` CHANGE `author_id` `author_id` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__cck_core` CHANGE `parent_id` `parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__cck_core` CHANGE `store_id` `store_id` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__cck_core` CHANGE `download_hits` `download_hits` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__cck_core` CHANGE `storage_location` `storage_location` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__cck_core` CHANGE `storage_table` `storage_table` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__cck_core` CHANGE `app` `app` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__cck_core` CHANGE `date_time` `date_time` DATETIME NULL DEFAULT NULL;
ALTER TABLE `#__cck_core` CHANGE `author_session` `author_session` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

--

ALTER TABLE `#__cck_core_fields` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__cck_core_folders` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__cck_core_searchs` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__cck_core_sites` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__cck_core_templates` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__cck_core_types` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__cck_core_versions` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;

UPDATE `#__cck_core_fields` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';
UPDATE `#__cck_core_folders` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';
UPDATE `#__cck_core_searchs` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';
UPDATE `#__cck_core_sites` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';
UPDATE `#__cck_core_templates` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';
UPDATE `#__cck_core_types` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';
UPDATE `#__cck_core_versions` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';

ALTER TABLE `#__cck_core_fields` MODIFY `checked_out` INT UNSIGNED;
ALTER TABLE `#__cck_core_folders` MODIFY `checked_out` INT UNSIGNED;
ALTER TABLE `#__cck_core_searchs` MODIFY `checked_out` INT UNSIGNED;
ALTER TABLE `#__cck_core_sites` MODIFY `checked_out` INT UNSIGNED;
ALTER TABLE `#__cck_core_templates` MODIFY `checked_out` INT UNSIGNED;
ALTER TABLE `#__cck_core_types` MODIFY `checked_out` INT UNSIGNED;
ALTER TABLE `#__cck_core_versions` MODIFY `checked_out` INT UNSIGNED;

UPDATE `#__cck_core_fields` SET `checked_out` = NULL WHERE `checked_out` = 0;
UPDATE `#__cck_core_folders` SET `checked_out` = NULL WHERE `checked_out` = 0;
UPDATE `#__cck_core_searchs` SET `checked_out` = NULL WHERE `checked_out` = 0;
UPDATE `#__cck_core_sites` SET `checked_out` = NULL WHERE `checked_out` = 0;
UPDATE `#__cck_core_templates` SET `checked_out` = NULL WHERE `checked_out` = 0;
UPDATE `#__cck_core_types` SET `checked_out` = NULL WHERE `checked_out` = 0;
UPDATE `#__cck_core_versions` SET `checked_out` = NULL WHERE `checked_out` = 0;

--

ALTER TABLE `#__cck_more_processings` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__cck_more_jobs` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;

UPDATE `#__cck_more_processings` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';
UPDATE `#__cck_more_jobs` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';

ALTER TABLE `#__cck_more_processings` MODIFY `checked_out` INT UNSIGNED;
ALTER TABLE `#__cck_more_jobs` MODIFY `checked_out` INT UNSIGNED;

UPDATE `#__cck_more_processings` SET `checked_out` = NULL WHERE `checked_out` = 0;
UPDATE `#__cck_more_jobs` SET `checked_out` = NULL WHERE `checked_out` = 0;

--

UPDATE `#__cck_core_fields` SET `type` = 'select_dynamic', `options2` = '{"query":"","table":"#__viewlevels","name":"title","where":"","value":"id","orderby":"ordering","orderby_direction":"ASC","limit":"","language_detection":"joomla","language_codes":"EN,GB,US,FR","language_default":"EN","attr1":"","attr2":"","attr3":"","attr4":"","attr5":"","attr6":""}' WHERE `name` = 'core_access';
UPDATE `#__cck_core_fields` SET `options` = 'Allowed=||Allowed Hidden=hidden||As Collection=collection||Not Allowed=none||Location=optgroup||Administrator Only=admin||Site Only=site' WHERE `name` = 'core_location';
UPDATE `#__cck_core_fields` SET `options2` = '{"query":"","table":"#__cck_core_types","name":"title","where":"location NOT IN (\\"none\\",\\"collection\\") AND published != -44","value":"name","orderby":"title","orderby_direction":"ASC","limit":"","language_detection":"joomla","language_codes":"EN,GB,US,FR","language_default":"EN","attr1":"","attr2":"","attr3":"","attr4":"","attr5":"","attr6":""}' WHERE `name` = 'core_parent_type';
UPDATE `#__cck_core_fields` SET `options2` = '{"query":"","table":"#__cck_core_types","name":"title","where":"published = 1 AND location NOT IN(\\"admin\\",\\"none\\",\\"collection\\")","value":"name","orderby":"title","orderby_direction":"ASC","limit":"","language_detection":"joomla","language_codes":"EN,GB,US,FR","language_default":"EN"}' WHERE `name` = 'core_content_type';
UPDATE `#__cck_core_fields` SET `label` = 'clear' WHERE `name` IN ("icon_delete","icon_download","icon_edit","icon_preview","icon_trash","icon_view","icon_add","icon_file_plus","icon_file_check","icon_file_remove","icon_file_minus");

--

ALTER TABLE `#__cck_store_item_content` CHANGE `cck` `cck` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__cck_store_item_categories` CHANGE `cck` `cck` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';

--