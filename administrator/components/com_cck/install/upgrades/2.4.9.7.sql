
ALTER TABLE `#__cck_core_folders` ADD `icon_path` VARCHAR( 100 ) NOT NULL AFTER `elements`;
ALTER TABLE `#__cck_core_folders` ADD `app` VARCHAR( 50 ) NOT NULL AFTER `description`;
ALTER TABLE `#__cck_core_folders` ADD `home` TINYINT( 3 ) NOT NULL DEFAULT '0' AFTER `featured`;

UPDATE `#__cck_core_fields` SET `size` = 16 WHERE `id` = 21;
UPDATE `#__cck_core_fields` SET `size` = 16, `defaultvalue` = '#ffffff' WHERE `id` = 22;
UPDATE `#__cck_core_fields` SET `options2` = '{"preparecontent":"","prepareform":"$options = Helper_Admin::getFolderOptions( false, true, false, true, $config[''vName''] );\\r\\n$class = $field->css ? '' ''.$field->css : '''';\\r\\n$form = JHtml::_( ''select.genericlist'', $options, $name, ''class=\\"inputbox select''.$class.''\\" size=\\"1\\"'', ''value'', ''text'', $value, $id );","preparestore":""}' WHERE `id` = 41;
UPDATE `#__cck_core_fields` SET `selectlabel` = 'Use Global', `defaultvalue` = '' WHERE `id` = 194;
UPDATE `#__cck_core_fields` SET `selectlabel` = 'Use Global', `defaultvalue` = '', `options` = 'Position bottomLeft=bottomLeft||Position bottomRight=bottomRight||Position inline=inline||Position centerRight=centerRight||Position topLeft=topLeft||Position topRight=topRight' WHERE `id` = 195;

UPDATE `#__cck_core_folders` SET `name` = "joomla_article", `home` = 1, `icon_path` = "media/cck/apps/joomla_article/images/icon.png" WHERE `id` = 10;
UPDATE `#__cck_core_folders` SET `name` = "joomla_category", `home` = 1, `icon_path` = "media/cck/apps/joomla_category/images/icon.png" WHERE `id` = 11;
UPDATE `#__cck_core_folders` SET `name` = "joomla_message", `home` = 1 WHERE `id` = 12;
UPDATE `#__cck_core_folders` SET `name` = "joomla_user", `home` = 1, `icon_path` = "media/cck/apps/joomla_user/images/icon.png" WHERE `id` = 13;
UPDATE `#__cck_core_folders` SET `name` = "joomla_user_group", `home` = 1, `icon_path` = "media/cck/apps/joomla_user_group/images/icon.png" WHERE `id` = 14;