
INSERT IGNORE INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES
(294, 'Button Search', 'button_search', 3, 'button_submit', '', 1, 'Search', ' ', 3, '', '', '', '', '{"alt_link_text":"","alt_link":"","alt_link_options":""}', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 'inputbutton', '', 'none', '', '', 'button_search', '', '', '', 0, '0000-00-00 00:00:00'),
(293, 'Button Save', 'button_save', 3, 'button_submit', '', 1, 'Save', ' ', 3, '', '', '', '', '{"alt_link_text":"","alt_link":"","alt_link_options":""}', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 1, 0, 0, 0, 0, 0, 0, '', '', 'none', '', '', 'button_save', '', '', '', 0, '0000-00-00 00:00:00'),
(8, 'CCK (3)', 'cck_3', 3, 'select_dynamic', '', 1, 'Type', ' ', 3, '', '', '', '', '{"query":"","table":"#__cck_core_types","name":"title","where":"published=1","value":"name","orderby":"title","orderby_direction":"ASC","limit":"","language_detection":"joomla","language_codes":"EN,GB,US,FR","language_default":"EN"}', 0, 255, 32, 0, 0, 0, 0, ',', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'standard', 'free', '#__cck_core', 'cck', '', '', '', 0, '0000-00-00 00:00:00'),
(9, 'CCK Id', 'cck_id', 3, 'text', '', 1, 'ID', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'standard', 'free', '#__cck_core', 'id', '', '', '', 0, '0000-00-00 00:00:00'),
(272, 'Core Rules (Type)', 'core_rules_type', 3, 'jform_rules', '', 0, 'Permissions', ' ', 3, '', '', '', '', '{"extension":"com_cck","section":"form"}', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'jform[rules]', '', '', '', 0, '0000-00-00 00:00:00'),
(273, 'Core Rules (Folder)', 'core_rules_folder', 3, 'jform_rules', '', 0, 'Permissions', ' ', 3, '', '', '', '', '{"extension":"com_cck","section":"folder"}', 0, 255, 32, 0, 0, 0, 0, '', 1, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'jform[rules]', '', '', '', 0, '0000-00-00 00:00:00'),
(274, 'Core Tables', 'core_tables', 3, '42', '', 0, 'Table', ' ', 3, '', '', '', '', '{"preparecontent":"","prepareform":"$opts    = array();\\r\\n$prefix  = JFactory::getApplication()->getCfg( ''dbprefix'' );\\r\\n$tables  = JCckDatabase::loadColumn( ''SHOW TABLES'' );\\r\\nif ( count( $tables ) ) {\\r\\n  foreach ( $tables as $table ) {\\r\\n    $t = str_replace( $prefix, ''#__'', $table );\\r\\n    $opts[] = JHtml::_( ''select.option'', $t, $t, ''value'', ''text'' );\\r\\n  }\\r\\n}\\r\\n$attr = ''class=\\"inputbox select\\" size=\\"1\\" ''.$field->attributes;\\r\\n$form = JHtml::_( ''select.genericlist'', $opts, $name, $attr, ''value'', ''text'', $value, $id );","preparestore":""}', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 'style="max-width:200px;"', 'dev', '', '', 'table', '', '', '', 0, '0000-00-00 00:00:00');

CREATE TABLE IF NOT EXISTS `#__cck_more_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `extension` varchar(50) NOT NULL,
  `folder` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `options` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_extension` (`extension`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=500 ;

UPDATE `#__cck_core_fields` SET `bool` = 1 WHERE `type` = "group_x";
UPDATE `#__cck_core_fields` SET `attributes` = 'style="max-width:200px;"' WHERE `id` IN (29,216,217);
UPDATE `#__cck_core_fields` SET `script` = '$j("fieldset#layer").on("click", "label", function() {\r\n $j("#layer label").removeClass(''selected''); $j(this).addClass(''selected'');\r\n var current = $j("#"+$j(this).attr(''for''));\r\n if (current.prop("checked") != true) {\r\n  $j("#layer input").removeAttr("checked"); current.attr("checked", "checked"); $j(".layers").slideUp();  $j("#layer_"+current.val()).slideDown();\r\n }\r\n});' WHERE `id` = 57;
UPDATE `#__cck_core_fields` SET `options` = 'Hide=-1||Show=optgroup||Filename Title=0||Icon=1||Image=2||Thumb1=3||Thumb2=4||Thumb3=5||Thumb4=6||Thumb5=7' WHERE `id` = 113;
UPDATE `#__cck_core_fields` SET `options` = 'Hide=-1||Show=optgroup||Filename Title=0||Icon=1||Show No Link=optgroup||Filename Title=8' WHERE `id` = 132;
UPDATE `#__cck_core_fields` SET `bool` = 0, `options2` = '{"extension":"com_content","section":"category"}' WHERE `id` = 392;
UPDATE `#__cck_core_fields` SET `bool` = 0, `options2` = '{"extension":"com_content","section":"article"}' WHERE `id` = 393;

UPDATE `#__extensions` SET `params` = '{"group":"PLG_CCK_FIELD_LINK_GROUP_CONTENT","export":""}' WHERE `name` = "plg_cck_field_link_content";
UPDATE `#__extensions` SET `params` = '{"group":"PLG_CCK_FIELD_LINK_GROUP_CONTENT","export":""}' WHERE `name` = "plg_cck_field_link_content_delete";
UPDATE `#__extensions` SET `enabled` = '1' WHERE `folder` = 'cck_field_validation' AND ( `element` = 'ajax_availability' );