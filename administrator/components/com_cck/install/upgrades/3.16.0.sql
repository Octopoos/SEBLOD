
ALTER TABLE `#__cck_core_types` ADD `parent_inherit` TINYINT(3) NOT NULL AFTER `parent`;

UPDATE `#__cck_core_types` SET `parent_inherit`=1 WHERE `parent` != "";