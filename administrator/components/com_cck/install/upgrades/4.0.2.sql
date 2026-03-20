
UPDATE `#__cck_core_fields` SET `type` = 'select_dynamic', `options2` = '{"query":"","table":"#__viewlevels","name":"title","where":"","value":"id","orderby":"ordering","orderby_direction":"ASC","limit":"","language_detection":"joomla","language_codes":"EN,GB,US,FR","language_default":"EN","attr1":"","attr2":"","attr3":"","attr4":"","attr5":"","attr6":""}' WHERE `name` = 'core_access';

ALTER TABLE `#__cck_core_types` ADD `language` CHAR(7) NOT NULL DEFAULT '*' AFTER `indexed`;
ALTER TABLE `#__cck_core_fields` ADD `language` CHAR(7) NOT NULL DEFAULT '*' AFTER `label`;
ALTER TABLE `#__cck_core_fields` ADD `storage_filter` VARCHAR(50) NOT NULL DEFAULT '' AFTER `storage_field2`;
ALTER TABLE `#__cck_core_fields` ADD `versionning` TINYINT(3) NOT NULL DEFAULT '0' AFTER `checked_out_time`;

ALTER TABLE `#__cck_core_folders` DROP `asset_id`;
ALTER TABLE `#__cck_core_folders` ADD `params` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `home`;

ALTER TABLE `#__cck_core_sites` CHANGE `guest_only_viewlevel` `guest_only_viewlevel` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__cck_core_sites` CHANGE `parent_id` `parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__cck_core_sites` ADD `users` VARCHAR(255) NOT NULL AFTER `usergroups`;