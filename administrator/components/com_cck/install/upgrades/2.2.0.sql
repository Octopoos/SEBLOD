
ALTER TABLE `#__cck_core_search_field` ADD `markup_class` VARCHAR( 255 ) NOT NULL COMMENT 'item' AFTER `live_value`;
ALTER TABLE `#__cck_core_type_field` ADD `markup_class` VARCHAR( 255 ) NOT NULL COMMENT 'content,intro' AFTER `live_value`;

ALTER TABLE `#__cck_core_search_position` CHANGE `variation_options` `variation_options` TEXT NOT NULL;
ALTER TABLE `#__cck_core_type_position` CHANGE `variation_options` `variation_options` TEXT NOT NULL;

INSERT IGNORE INTO `#__cck_core_folders` (`id`, `asset_id`, `parent_id`, `title`, `name`, `color`, `introchar`, `colorchar`, `elements`, `depth`, `lft`, `rgt`, `description`, `featured`, `published`, `checked_out`, `checked_out_time`) VALUES
(28, 0, 10, 'Images & Links', 'images_links', '#0090d1', 'A.', '#ffffff', 'field', 3, 10, 11, '', 0, 1, 0, '0000-00-00 00:00:00');

INSERT IGNORE INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES
(403, 'Article Grp Images & Links', 'art_grp_images_links', 22, 'group', '', 1, 'Images and Links', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 1, 0, 0, '', 0, '', 'article_grp_images_links', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'none', '', '', 'article_grp_images_links', '', '', '', 0, '0000-00-00 00:00:00'),
(404, 'Article Image Intro', 'art_image_intro', 28, 'jform_media', '', 1, 'Intro Image', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'images', 'image_intro', '', '', 0, '0000-00-00 00:00:00'),
(405, 'Article Image Intro Alt', 'art_image_intro_alt', 28, 'text', '', 1, 'Alt Text', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'images', 'image_intro_alt', '', '', 0, '0000-00-00 00:00:00'),
(406, 'Article Image Intro Caption', 'art_image_intro_caption', 28, 'text', '', 1, 'Caption', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'images', 'image_intro_caption', '', '', 0, '0000-00-00 00:00:00'),
(407, 'Article Image Intro Float', 'art_image_intro_float', 28, 'select_simple', '', 1, 'Image Float', 'Use Global', 3, '', '', '', 'Left=left||Right=right||None=none', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'images', 'float_intro', '', '', 0, '0000-00-00 00:00:00'),
(408, 'Article Image Fulltext', 'art_image_fulltext', 28, 'jform_media', '', 1, 'Full Article Image', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'images', 'image_fulltext', '', '', 0, '0000-00-00 00:00:00'),
(410, 'Article Image Fulltext Caption', 'art_image_fulltext_caption', 28, 'text', '', 1, 'Caption', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'images', 'image_fulltext_caption', '', '', 0, '0000-00-00 00:00:00'),
(409, 'Article Image Fulltext Alt', 'art_image_fulltext_alt', 28, 'text', '', 1, 'Alt Text', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'images', 'image_fulltext_alt', '', '', 0, '0000-00-00 00:00:00'),
(411, 'Article Image Fulltext Float', 'art_image_fulltext_float', 28, 'select_simple', '', 1, 'Image Float', 'Use Global', 3, '', '', '', 'Left=left||Right=right||None=none', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'images', 'float_fulltext', '', '', 0, '0000-00-00 00:00:00'),
(412, 'Article UrlA', 'art_urla', 28, 'text', '', 1, 'Link A', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'urls', 'urla', '', '', 0, '0000-00-00 00:00:00'),
(413, 'Article UrlA Text', 'art_urla_text', 28, 'text', '', 1, 'Link A Text', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'urls', 'urlatext', '', '', 0, '0000-00-00 00:00:00'),
(414, 'Article UrlA Target', 'art_urla_target', 28, 'select_simple', '', 1, 'URL Target Window', 'Use Global', 3, '', '', '', 'Open in Parent Window=0||Open in New Window=1||Open in Popup=2||Open in Modal=3', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'urls', 'targeta', '', '', 0, '0000-00-00 00:00:00'),
(415, 'Article UrlB', 'art_urlb', 28, 'text', '', 1, 'Link B', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'urls', 'urlb', '', '', 0, '0000-00-00 00:00:00'),
(416, 'Article UrlB Text', 'art_urlb_text', 28, 'text', '', 1, 'Link B Text', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'urls', 'urlbtext', '', '', 0, '0000-00-00 00:00:00'),
(417, 'Article UrlB Target', 'art_urlb_target', 28, 'select_simple', '', 1, 'URL Target Window', 'Use Global', 3, '', '', '', 'Open in Parent Window=0||Open in New Window=1||Open in Popup=2||Open in Modal=3', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'urls', 'targetb', '', '', 0, '0000-00-00 00:00:00'),
(418, 'Article UrlC', 'art_urlc', 28, 'text', '', 1, 'Link C', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'urls', 'urlc', '', '', 0, '0000-00-00 00:00:00'),
(419, 'Article UrlC Text', 'art_urlc_text', 28, 'text', '', 1, 'Link C Text', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'urls', 'urlctext', '', '', 0, '0000-00-00 00:00:00'),
(420, 'Article UrlC Target', 'art_urlc_target', 28, 'select_simple', '', 1, 'URL Target Window', 'Use Global', 3, '', '', '', 'Open in Parent Window=0||Open in New Window=1||Open in Popup=2||Open in Modal=3', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'urls', 'targetc', '', '', 0, '0000-00-00 00:00:00'),
(421, 'Article Urls Position', 'art_urls_position', 16, 'select_simple', '', 1, 'Positioning of the Links', 'Use Global', 3, '', '', '', 'Above=0||Below=1', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'json', 'joomla_article', '#__content', 'attribs', 'urls_position', '', '', 0, '0000-00-00 00:00:00'),
(255, 'Core Options Format File', 'core_options_format_file', 3, 'select_simple', '', 0, 'Storage Format', ' ', 3, '', '', '0', 'Filename=1||Full Path=0', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'json[options2][storage_format]', '', '', '', 0, '0000-00-00 00:00:00');

INSERT IGNORE INTO `#__cck_core_types` (`id`, `asset_id`, `title`, `name`, `folder`, `template_admin`, `template_site`, `template_content`, `template_intro`, `description`, `published`, `options_admin`, `options_site`, `options_content`, `options_intro`, `checked_out`, `checked_out_time`) VALUES
(30, 0, 'Article Grp Images & Links', 'article_grp_images_links', 10, 7, 7, 7, 7, '', 0, '', '', '', '', 0, '0000-00-00 00:00:00');

INSERT IGNORE INTO `#__cck_core_type_field` (`typeid`, `fieldid`, `client`, `ordering`, `label`, `variation`, `required`, `required_alert`, `validation`, `validation_options`, `link`, `link_options`, `live`, `live_value`, `markup_class`, `typo`, `typo_label`, `typo_options`, `stage`, `access`, `conditional`, `conditional_options`, `position`) VALUES
(30, 404, 'admin', 1, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 407, 'admin', 2, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 405, 'admin', 3, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 406, 'admin', 4, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 408, 'admin', 5, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 411, 'admin', 6, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 409, 'admin', 7, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 410, 'admin', 8, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 412, 'admin', 9, '', '', '', '', 'url', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 413, 'admin', 10, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 414, 'admin', 11, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 415, 'admin', 12, '', '', '', '', 'url', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 416, 'admin', 13, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 417, 'admin', 14, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 418, 'admin', 15, '', '', '', '', 'url', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 419, 'admin', 16, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 420, 'admin', 17, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 404, 'site', 1, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 407, 'site', 2, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 405, 'site', 3, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 406, 'site', 4, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 408, 'site', 5, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 411, 'site', 6, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 409, 'site', 7, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 410, 'site', 8, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 412, 'site', 9, '', '', '', '', 'url', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 413, 'site', 10, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 414, 'site', 11, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 415, 'site', 12, '', '', '', '', 'url', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 416, 'site', 13, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 417, 'site', 14, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 418, 'site', 15, '', '', '', '', 'url', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 419, 'site', 16, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody'),
(30, 420, 'site', 17, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 1, '', '', 'mainbody');

INSERT INTO `#__cck_core_type_position` (`typeid`, `position`, `client`, `legend`, `variation`, `variation_options`, `width`, `height`, `css`) VALUES
(30, 'mainbody', 'admin', '', '', '', '', '', ''),
(30, 'hidden', 'admin', '', '', '', '', '', ''),
(30, 'mainbody', 'site', '', '', '', '', '', ''),
(30, 'hidden', 'site', '', '', '', '', '', '');

UPDATE `#__cck_core_fields` SET `options2` = '{"preparecontent":"","prepareform":"require_once JPATH_ADMINISTRATOR.DS. ''components'' .DS. CCK_COM .DS. ''models'' .DS. ''fields'' .DS. ''core.php'';\\r\\n$value = ( $value ) ? $value : ''custom'';\\r\\n$options = array();\\r\\n$options[] = JHtml::_( ''select.option'', ''none'', ''- ''.JText::_( ''COM_CCK_NONE'' ).'' -'', ''value'', ''text'' );\\r\\n$my = JFactory::getUser();\\r\\n$iAmSuperAdmin = $my->authorise( ''core.admin'' );\\r\\nif ( ( JCck::getConfig_Param( ''storage_dev'', ''0'' ) == 1 && $iAmSuperAdmin === TRUE ) || ( $value == ''dev'' ) ) { $options[] = JHtml::_( ''select.option'', ''dev'', JText::_ ( ''COM_CCK_DEVELOPMENT'' ), ''value'', ''text'' );\\r\\n}\\r\\n$options = array_merge( $options, JFormFieldCore::getPluginTypes( ''storage'', ''cck_'', false, false, true ) );\\r\\n$form = JHtml::_( ''select.genericlist'', $options, $name, ''class=\\"inputbox select\\" size=\\"1\\" ''.$field->attributes, ''value'', ''text'', $value );","preparestore":""}' WHERE `id` = 28;
UPDATE `#__cck_core_fields` SET `options2` = '{"preparecontent":"","prepareform":"$uix = JCck::getUIX();\\r\\n$checked1 = ( $config[''item'']->client == ''admin'' ) ? ''checked=\\"checked\\"'' : '''';\\r\\n$checked2 = ( $config[''item'']->client == ''site'' ) ? ''checked=\\"checked\\"'' : '''';\\r\\n$selected1 = ( $config[''item'']->client == ''admin'' ) ? ''selected'' : '''';\\r\\n$selected2 = ( $config[''item'']->client == ''site'' ) ? ''selected'' : '''';\\r\\nif ( $uix == ''full'' ) {\\r\\n  $checked3 = ( $config[''item'']->client == ''intro'' ) ? ''checked=\\"checked\\"'' : '''';\\r\\n  $checked4 = ( $config[''item'']->client == ''content'' ) ? ''checked=\\"checked\\"'' : '''';\\r\\n  $selected3 = ( $config[''item'']->client == ''intro'' ) ? ''selected'' : '''';\\r\\n  $selected4 = ( $config[''item'']->client == ''content'' ) ? ''selected'' : '''';\\r\\n}\\r\\n\\r\\n$form = ''<fieldset id=\\"client\\" class=\\"toggle\\">''\\r\\n      . ''<input type=\\"radio\\" id=\\"client1\\" name=\\"client\\" value=\\"admin\\" ''\\r\\n      . ''style=\\"display: none\\" ''.$checked1.'' \\/>''\\r\\n      . ''<input type=\\"radio\\" id=\\"client2\\" name=\\"client\\" value=\\"site\\" ''\\r\\n      . ''style=\\"display: none\\" ''.$checked2.'' \\/>'';\\r\\nif ( $uix == ''full'' ) {\\r\\n   $form .= ''<input type=\\"radio\\" id=\\"client3\\" name=\\"client\\" value=\\"intro\\" ''\\r\\n         .  ''style=\\"display: none\\" ''.$checked3.'' \\/>''\\r\\n         .  ''<input type=\\"radio\\" id=\\"client4\\" name=\\"client\\" value=\\"content\\" ''\\r\\n         .  ''style=\\"display: none\\" ''.$checked4.'' \\/>'';\\r\\n}\\r\\n$form .= ''<label for=\\"client1\\" class=\\"toggle first ''.$selected1.''\\">''\\r\\n      .  JText::_( ''COM_CCK_ADMIN_FORM'' ).''<\\/label>''\\r\\n      .  ''<label for=\\"client2\\" class=\\"toggle ''.$selected2.''\\">''\\r\\n      .  JText::_( ''COM_CCK_SITE_FORM'' ).''<\\/label>'';\\r\\nif ( $uix == ''full'' ) {\\r\\n   $form .= ''<label for=\\"client3\\" class=\\"toggle ''.$selected3.''\\">''\\r\\n         .  JText::_( ''COM_CCK_INTRO'' ).''<\\/label>''\\r\\n         .  ''<label for=\\"client4\\" class=\\"toggle last ''.$selected4.''\\">''\\r\\n         .  JText::_( ''COM_CCK_CONTENT'' ).''<\\/label>'';\\r\\n}\\r\\n$form .= ''<div align=\\"center\\" class=\\"subtabs\\">''\\r\\n      .  ''<div id=\\"subtab1\\"><\\/div>''\\r\\n      .  ''<div id=\\"subtab2\\">''.JText::_( ''COM_CCK_VIEWS'' ).''<\\/div>''\\r\\n      .  ''<div id=\\"subtab3\\"><\\/div>''\\r\\n      .  ''<\\/div>''\\r\\n      .  ''<\\/fieldset>'';","preparestore":""}' WHERE `id` = 67;

-- --------------------------------------------------------
