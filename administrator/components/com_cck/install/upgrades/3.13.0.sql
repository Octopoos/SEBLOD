
UPDATE `#__cck_core_fields` SET `bool5` = '5' WHERE `type` = 'code_beforerender';

ALTER TABLE `#__cck_core_types` ADD `locked` TINYINT(3) NOT NULL DEFAULT '1' AFTER `location`;

ALTER TABLE `#__cck_core` ADD `download_hits` INT(10) UNSIGNED NOT NULL AFTER `store_id`;

INSERT INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_cck`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES
(622, 'Core Content Type (List Output)', 'cck_list_output', 3, 'select_dynamic', '', 1, 'Type', ' ', 3, '', '', '', '', '{"query":"","table":"#__cck_core_types","name":"title","where":"published=1","value":"name","orderby":"title","orderby_direction":"ASC","limit":"","language_detection":"joomla","language_codes":"EN,GB,US,FR","language_default":"EN"}', 0, 50, 32, 0, 0, 0, 0, ',', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'standard', '', 'free', '', 'cck', '', '', '', 0, '0000-00-00 00:00:00'),
(623, 'Core Download Hits', 'cck_download_hits', 3, 'text', '', 1, '', '', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'standard', '', 'free', '#__cck_core', 'download_hits', '', '', '', 0, '0000-00-00 00:00:00');

UPDATE `#__cck_core_fields` SET `options` = 'Allowed=||Not Allowed=none||location=optgroup||Administrator Only=admin||Site Only=site' WHERE `id` = 276;
UPDATE `#__cck_core_fields` SET `options2` = '{"query":"","table":"#__cck_core_types","name":"title","where":"published = 1 AND location != \\"admin\\" AND location != \\"none\\"","value":"name","orderby":"title","orderby_direction":"ASC","limit":"","language_detection":"joomla","language_codes":"EN,GB,US,FR","language_default":"EN","attr1":"","attr2":"","attr3":"","attr4":"","attr5":"","attr6":""}' WHERE `id` = 58;
UPDATE `#__cck_core_fields` SET `options2` = '{"preparecontent":"","prepareform":"$app = JFactory::getApplication();\\r\\n$view = $app->input->get( \'view\', \'\' );\\r\\n$options = array();\\r\\nif ( trim( $field->selectlabel ) ) {\\r\\n $options = array( JHtml::_( \'select.option\', \'\', \'- \'.$field->selectlabel.\' -\' ) );\\r\\n} else {\\r\\n $value = ( $value ) ? $value : \'\';\\r\\n $options = array();\\r\\n}\\r\\nif ( $view == \'type\' || $view == \'types\' ) {\\r\\n $options[] = JHtml::_( \'select.option\', \'none\', JText::_( \'COM_CCK_NONE\' ) );\\r\\n}\\r\\n$class = $field->css ? \' \'.$field->css : \'\';\\r\\n$options = array_merge( $options, Helper_Admin::getPluginOptions( \'storage_location\', \'cck_\', false, false, true ) );\\r\\n$form = JHtml::_( \'select.genericlist\', $options, $name, \'class=\\"inputbox select\'.$class.\'\\" \'.$field->attributes, \'value\', \'text\', $value, $id );","preparestore":""}' WHERE `id` = 275;

ALTER TABLE `#__cck_core` CHANGE `author_id` `author_id` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `#__cck_core` CHANGE `store_id` `store_id` INT(10) UNSIGNED NOT NULL;

ALTER TABLE `#__cck_core_sites` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__cck_core_sites` CHANGE `guest` `guest` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `#__cck_core_sites` CHANGE `guest_only_group` `guest_only_group` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `#__cck_core_sites` CHANGE `guest_only_viewlevel` `guest_only_viewlevel` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `#__cck_core_sites` CHANGE `public_viewlevel` `public_viewlevel` INT(10) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `#__cck_store_item_users` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL;

ALTER TABLE `#__cck_more_jobs` CHANGE `run_as` `run_as` INT(10) UNSIGNED NOT NULL DEFAULT '0';

UPDATE `#__cck_core_types` SET `location`='none', `storage_location`='joomla_article' WHERE `location`='' AND `storage_location`='none' AND name IN ("article_grp_basic","article_grp_images_links","article_grp_metadata","article_grp_publishing");
UPDATE `#__cck_core_types` SET `location`='none', `storage_location`='joomla_category' WHERE `location`='' AND `storage_location`='none' AND name IN ("category_grp_basic","category_grp_metadata","category_grp_publishing");
UPDATE `#__cck_core_types` SET `location`='none', `storage_location`='joomla_user' WHERE `location`='' AND `storage_location`='none' AND name IN ("user_grp_basic","user_grp_password");