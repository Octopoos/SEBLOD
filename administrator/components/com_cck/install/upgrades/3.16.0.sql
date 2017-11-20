
ALTER TABLE `#__cck_core_types` ADD `parent_inherit` TINYINT(3) NOT NULL AFTER `parent`;

UPDATE `#__cck_core_types` SET `parent_inherit`=1 WHERE `parent` != "";

UPDATE `#__cck_core_fields` SET `options` = 'Allowed=||Allowed Hidden=hidden||Not Allowed=none||location=optgroup||Administrator Only=admin||Site Only=site' WHERE `id` = 276;
UPDATE `#__cck_core_fields` SET `options` = 'No=0||Yes=optgroup||Yes for Everyone=1||Yes for Super Admin=2||Config No Search=optgroup||Yes for Everyone=-1||Yes for Super Admin=-2' WHERE `id` = 174;
