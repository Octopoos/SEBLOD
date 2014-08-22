
RENAME TABLE `#__cck_core_integration` TO `#__cck_core_objects`;
ALTER TABLE `#__cck_core_objects` CHANGE `option` `component` VARCHAR( 50 ) NOT NULL;
ALTER TABLE `#__cck_core_objects` ADD `options` TEXT NOT NULL AFTER `component`;
ALTER TABLE `#__cck_core_objects` DROP INDEX `idx_option`, ADD INDEX `idx_component` ( `component` );

INSERT IGNORE INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES
(462, 'Core Dev Radio', 'core_dev_radio', 3, 'radio', '', 0, 'clear', ' ', 3, '', '', '', '', '{"options":[]}', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, 'btn-group', '', 'dev', '', '', 'radio', '', '', '', 0, '0000-00-00 00:00:00');

UPDATE `#__cck_core_objects` SET `options` = '{"default_type":"article","add":"1","add_layout":"icon","add_alt":"2","add_redirect":"1","edit":"0","edit_alt":"1"}' WHERE `name` = "joomla_article";
UPDATE `#__cck_core_objects` SET `options` = '{"default_type":"category","add":"1","add_layout":"icon","add_alt":"2","add_redirect":"1","edit":"0","edit_alt":"1","exclude":""}' WHERE `name` = "joomla_category";
UPDATE `#__cck_core_objects` SET `options` = '{"default_type":"user","add":"1","add_layout":"icon","add_alt":"2","add_redirect":"1","edit":"0","edit_alt":"1","registration":"1"}' WHERE `name` = "joomla_user";
UPDATE `#__cck_core_objects` SET `options` = '{"default_type":"user_group","add":"1","add_layout":"icon","add_alt":"2","add_redirect":"1","edit":"0","edit_alt":"1"}' WHERE `name` = "joomla_user_group";