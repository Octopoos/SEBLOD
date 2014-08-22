
ALTER TABLE `#__cck_core_types` ADD `indexed` VARCHAR( 50 ) NOT NULL AFTER `description`;
ALTER TABLE `#__cck_core_types` ADD `storage_location` VARCHAR( 50 ) NOT NULL AFTER `options_intro`;

INSERT IGNORE INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES
(256, 'Core Auto Redirection', 'core_auto_redirection', 3, 'select_simple', '', 0, 'Redirection', ' ', 3, '', '', '', 'No=0||Redirection=optgroup||Content=1||Form=2', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'auto_redirect', '', '', '', 0, '0000-00-00 00:00:00'),
(257, 'Core Indexing', 'core_indexing', 3, 'select_simple', '', 0, 'Indexing', ' ', 3, '', '', 'intro', 'Content=content||Intro=intro', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'indexed', '', '', '', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------
