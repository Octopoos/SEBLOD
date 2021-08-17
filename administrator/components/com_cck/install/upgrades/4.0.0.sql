
UPDATE `#__modules` SET `published` = 0 WHERE `module` IN ("mod_cck_menu","mod_cck_quickadd");

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