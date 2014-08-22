CREATE TABLE IF NOT EXISTS `#__cck_core_preferences` (
  `userid` int(11) NOT NULL,
  `options` text NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `#__cck_core_types` ADD `asset_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `id`;

ALTER TABLE `#__cck_core_folders` ADD `featured` TINYINT( 4 ) NOT NULL DEFAULT '0' AFTER `description`;

ALTER TABLE `#__cck_core_fields` ADD `sorting` INT NOT NULL DEFAULT '0' AFTER `ordering`;

UPDATE `#__cck_core_fields` SET `title` = 'Core Sorting', `name` = 'core_sorting', `storage_field` = 'sorting' WHERE `id` = 15;
UPDATE `#__cck_core_fields` SET `title` = 'Core Position Margin', `name` = 'core_position_margin', `label` = 'Position Margin', `storage_field` = 'position_margin' WHERE `id` = 155;
UPDATE `#__cck_core_fields` SET `type` = 'select_dynamic', `selectlabel` = 'Select', `options2` = '{"query":"","table":"#__cck_core_types","name":"title","where":"published=1","value":"name"}' WHERE `id` = 58;
UPDATE `#__cck_core_fields` SET `type` = 'select_dynamic', `selectlabel` = 'Select', `options2` = '{"query":"","table":"#__cck_core_searchs","name":"title","where":"published=1","value":"name"}' WHERE `id` = 59;
UPDATE `#__cck_core_fields` SET `bool2` = '1' WHERE `id` = 299;

INSERT INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES
(162, 'Core Featured', 'core_featured', 3, 'radio', '', 0, 'Featured', ' ', 3, '', '', '0', 'No=0||Yes <em>(Featured Folder can be selected as a skeleton for new Content Type)</em>=1', '', 0, 50, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'featured', '', '', '', 0, '0000-00-00 00:00:00'),
(163, 'Core Title', 'core_title', 3, 'text', '', 0, 'Override Title', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'options[title]', '', '', '', 0, '0000-00-00 00:00:00'),
(164, 'Core Options Theme (Calendar)', 'core_options_theme_calendar', 3, 'select_simple', '', 0, 'Theme', ' ', 3, '', '', 'steel', 'Gold=gold||Steel=steel||Win2k', '', 0, 50, 32, 0, 10, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'json[options2][theme]', '', '', '', 0, '0000-00-00 00:00:00'),
(165, 'Core Options Time Pos', 'core_options_time_pos', 3, 'select_simple', '', 0, '', ' ', 3, '', '', 'right', 'Left=left||Right=right', '', 0, 50, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'json[options2][time_pos]', '', '', '', 0, '0000-00-00 00:00:00'),
(166, 'Core Options Dates', 'core_options_dates', 3, 'select_simple', '', 0, 'Dates', ' ', 3, '', '', '0', 'All (Default)=0||Past=1||Past & Today=2||Today & Future=3||Future=4', '', 0, 50, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'json[options2][dates]', '', '', '', 0, '0000-00-00 00:00:00'),
(167, 'Core Options Week Numbers', 'core_options_week_numbers', 3, 'select_simple', '', 0, 'Show Week Numbers', ' ', 3, '', '', '0', 'No=0||Yes=1', '', 0, 50, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'json[options2][week_numbers]', '', '', '', 0, '0000-00-00 00:00:00'),
(168, 'Core Options Time', 'core_options_time', 3, 'select_simple', '', 0, 'Time', ' ', 3, '', '', '12', 'No=0||12 (am/pm)=12||24 (hours)=24', '', 0, 50, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'json[options2][time]', '', '', '', 0, '0000-00-00 00:00:00'),
(169, 'Core Position Padding', 'core_position_padding', 3, 'text', '', 0, '', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'position_padding', '', '', '', 0, '0000-00-00 00:00:00');

UPDATE `#__cck_core_folders` SET `title` = 'Apps', `name` = 'apps', `introchar` = 'A' WHERE `id` = 8;
UPDATE `#__cck_core_folders` SET `featured` = '1' WHERE `id` = 10;
UPDATE `#__cck_core_folders` SET `featured` = '1' WHERE `id` = 11;
UPDATE `#__cck_core_folders` SET `featured` = '1' WHERE `id` = 13;
UPDATE `#__cck_core_folders` SET `featured` = '1' WHERE `id` = 14;

UPDATE `#__template_styles` SET `params` = '{"field_label":"1","field_description":"0","variation_default":"seb_css3","position_force_height":"1","position_margin":"8","position_header":"0","position_header_variation":"","position_left":"0","position_left_variation":"","position_top":"1","position_top_variation":"","position_sidebody_a":"0","position_sidebody_b":"0","position_bottom":"1","position_bottom_variation":"","position_right":"400","position_right_variation":"","position_footer":"0","position_footer_variation":"","debug":"0"}' WHERE `title` = "seb_one - Default";