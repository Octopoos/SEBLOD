
INSERT IGNORE INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES
(436, 'Article Key Reference', 'art_keyreference', 16, 'text', '', 1, 'Key Reference', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'standard', 'joomla_article', '#__content', 'xreference', '', '', '', 0, '0000-00-00 00:00:00'),
(437, 'User Last Reset Date', 'user_lastreset_date', 24, 'calendar', '', 1, 'Last Reset Date', ' ', 3, '', '', '', '', '{"format":"Y-m-d H:i:s","dates":"0","storage_format":"0","time":"12","time_pos":"right","default_hour":"00","default_min":"00","default_sec":"00","theme":"steel","week_numbers":"0"}', 0, 255, 27, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'standard', 'joomla_user', '#__users', 'lastResetTime', '', '', '', 0, '0000-00-00 00:00:00'),
(438, 'User Reset Count', 'user_reset_count', 24, 'text', '', 1, 'Password Reset Count', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'standard', 'joomla_user', '#__users', 'resetCount', '', '', '', 0, '0000-00-00 00:00:00');

UPDATE `#__cck_core_fields` SET `selectlabel` = 'Select' WHERE `id` = 76;
UPDATE `#__cck_core_fields` SET `title` = 'Article External Reference' WHERE `id` = 327;