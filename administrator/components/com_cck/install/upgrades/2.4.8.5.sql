
INSERT IGNORE INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES
(446, 'Button Next (4)', 'button_next_4', 3, 'button_submit', '', 1, 'Next', ' ', 3, '', '', '', '', '{"alt_link_text":"","alt_link":"","alt_link_options":""}', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'none', '', '', 'button_next_4', '', '', '', 0, '0000-00-00 00:00:00'),
(447, 'Button Next (5)', 'button_next_5', 3, 'button_submit', '', 1, 'Next', ' ', 3, '', '', '', '', '{"alt_link_text":"","alt_link":"","alt_link_options":""}', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'none', '', '', 'button_next_5', '', '', '', 0, '0000-00-00 00:00:00');

UPDATE `#__cck_core_fields` SET `options` = '_=||Event Change=change||Event Keyup=keyup||None=none' WHERE `id` = 268;
UPDATE `#__cck_core_fields` SET `label` = 'Content Creation', `options` = 'Denied=none||Allowed=||location=optgroup||Administrator Only=admin||Site Only=site' WHERE `id` = 276;

ALTER TABLE `#__cck_core_type_field` CHANGE `computation` `computation` VARCHAR( 512 ) NOT NULL COMMENT 'admin,site';
ALTER TABLE `#__cck_core_type_field` CHANGE `conditional` `conditional` VARCHAR( 2048 ) NOT NULL COMMENT 'admin,site';
ALTER TABLE `#__cck_core_type_field` CHANGE `conditional_options` `conditional_options` text NOT NULL COMMENT 'admin,site';
ALTER TABLE `#__cck_core_search_field` CHANGE `computation` `computation` VARCHAR( 512 ) NOT NULL COMMENT 'search';
ALTER TABLE `#__cck_core_search_field` CHANGE `conditional` `conditional` VARCHAR( 2048 ) NOT NULL COMMENT 'search';
ALTER TABLE `#__cck_core_search_field` CHANGE `conditional_options` `conditional_options` text NOT NULL COMMENT 'search';
