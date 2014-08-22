
CREATE TABLE IF NOT EXISTS `#__cck_core_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `e_id` int(11) NOT NULL DEFAULT '0',
  `e_title` varchar(50) NOT NULL,
  `e_name` varchar(50) NOT NULL,
  `e_type` varchar(50) NOT NULL,
  `e_version` int(11) NOT NULL DEFAULT '1',
  `e_core` longblob,
  `e_more` varchar(255) NOT NULL,
  `e_more1` longblob,
  `e_more2` longblob,
  `e_more3` longblob,
  `e_more4` longblob,
  `e_more5` longblob,
  `date_time` datetime NOT NULL,
  `note` varchar(255) NOT NULL,
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_id_version` (`e_id`,`e_type`,`e_version`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;

ALTER TABLE `#__cck_core_types` ADD `version` INT( 11 ) NOT NULL DEFAULT '1' AFTER `storage_location`;
ALTER TABLE `#__cck_core_searchs` ADD `version` INT( 11 ) NOT NULL DEFAULT '1' AFTER `options`;

INSERT IGNORE INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES
(258, 'Core Version Type Filter', 'core_version_e_type_filter', 3, 'select_simple', '', 0, 'Type', ' ', 3, '', '', 'type', 'Content Types=type||Search Types=search', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', ' onchange="if($j(''#filter_location'').val() == ''e_id''){ $j(''#filter_location'').val(''title''); $j(''#filter_search'').val(''''); } this.form.submit();"', 'dev', '', '', 'filter_e_type', '', '', '', 0, '0000-00-00 00:00:00'),
(259, 'Core Version Location Filter', 'core_version_location_filter', 3, 'select_simple', '', 0, 'Location', ' ', 3, '', '', '', 'ID=e_id||Details=optgroup||Title=e_title||Name=e_name', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'filter_location', '', '', '', 0, '0000-00-00 00:00:00'),
(260, 'Core Note', 'core_note', 3, 'text', '', 0, 'Note', ' ', 3, '', '', '', '', '', 0, 255, 96, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'note', '', '', '', 0, '0000-00-00 00:00:00'),
(261, 'Core Options Display', 'core_options_display', 3, 'select_simple', '', 0, 'Show Options', ' ', 3, '', '', '0', 'Hide=-1||Show=optgroup||Following Options=0||Alphabetical AZ=1||Alphabetical ZA=2', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'sorting', '', '', '', 0, '0000-00-00 00:00:00'),
(262, 'Core Show Hide', 'core_show_hide2', 3, 'select_simple', '', 0, 'Show', ' ', 3, '', '', '', 'Hide=0||Show=optgroup||Above=1||Below=2', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'show', '', '', '', 0, '0000-00-00 00:00:00');

UPDATE `#__cck_core_fields` SET `maxlength` = '512' WHERE `id` = 44;
UPDATE `#__cck_core_fields` SET `options` = 'Hide=-2||Show=optgroup||Above=-1||Below=0||Both=1' WHERE `id` = 244;

-- --------------------------------------------------------

UPDATE `#__cck_core_types` SET `storage_location` = 'joomla_article' WHERE `name` IN ("article","article_grp_basic","article_grp_publishing","article_grp_metadata","article_grp_images_links");
UPDATE `#__cck_core_types` SET `storage_location` = 'joomla_category' WHERE `name` IN ("category","category_grp_basic","category_grp_publishing","category_grp_metadata");
UPDATE `#__cck_core_types` SET `storage_location` = 'joomla_message' WHERE `name` IN ("message");
UPDATE `#__cck_core_types` SET `storage_location` = 'joomla_user' WHERE `name` IN ("user","user_grp_basic");
UPDATE `#__cck_core_types` SET `storage_location` = 'joomla_user_group' WHERE `name` IN ("user_group");