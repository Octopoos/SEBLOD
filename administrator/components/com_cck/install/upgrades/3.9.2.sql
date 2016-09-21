
ALTER TABLE `#__cck_core_types` ADD `admin_form` TINYINT(3) NOT NULL DEFAULT '0' AFTER `options_intro`;

ALTER TABLE `#__cck_core_templates` ADD `options` VARCHAR(2048) NOT NULL AFTER `featured`;