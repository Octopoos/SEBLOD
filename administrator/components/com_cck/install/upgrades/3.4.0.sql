
ALTER TABLE `#__cck_core_templates` ADD `featured` TINYINT(4) NOT NULL DEFAULT '0' AFTER `description`;

UPDATE `#__cck_core_templates` SET `featured` = '1' WHERE `name` = "seb_one";

UPDATE `#__cck_core_fields` SET `maxlength` = '512' WHERE `id` = 199;