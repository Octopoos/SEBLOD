
ALTER TABLE `#__cck_core` ADD `author_session` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `author_id`;

ALTER TABLE `#__cck_core_types` ADD `parent_inherit` TINYINT(3) NOT NULL AFTER `parent`;

ALTER TABLE `#__cck_core_searchs` ADD `sef_route_aliases` TINYINT(3) NOT NULL DEFAULT '0' AFTER `sef_route`;

ALTER TABLE `#__cck_more_jobs` ADD `run_url` INT(11) NOT NULL DEFAULT '0' AFTER `run_as`;
ALTER TABLE `#__cck_more_jobs` ADD `run_url_custom` VARCHAR(255) NOT NULL AFTER `run_url`;

UPDATE `#__cck_core_fields` SET `bool7` = '1' WHERE `type` = "textarea";

UPDATE `#__cck_core_fields` SET `defaultvalue` = '', `selectlabel` = 'Use Global', `options` = 'Email=1||Field=3' WHERE `id` = 124;

UPDATE `#__cck_core_fields` SET `script` = REPLACE( `script`, 'task=ajax&format=raw"', 'task=ajax&format=raw&"+Joomla.getOptions("csrf.token")+"=1"' ) WHERE `id` IN (58,59);
UPDATE `#__cck_core_fields` SET `script` = REPLACE( `script`, 'file+', 'file+"&referrer=component.com_cck"+' ) WHERE `id` IN (58,59);

UPDATE `#__cck_core_types` SET `parent_inherit`=1 WHERE `parent` != "";

UPDATE `#__cck_core_fields` SET `script` = '', `attributes` = '' WHERE `id` = 27;
UPDATE `#__cck_core_fields` SET `options2` = '{"preparecontent":"","prepareform":"$value = ( $value ) ? $value : ''custom'';\\r\\n$options = array();\\r\\n$options[] = JHtml::_( ''select.option'', ''none'', ''- ''.JText::_( ''COM_CCK_NONE'' ).'' -'', ''value'', ''text'' );\\r\\nif ( ( JCck::getConfig_Param( ''storage_dev'', ''0'' ) == 3) || ( $value == ''dev'' ) ) { $options[] = JHtml::_( ''select.option'', ''dev'', JText::_ ( ''COM_CCK_DEVELOPMENT'' ), ''value'', ''text'' );\\r\\n}\\r\\n$options = array_merge( $options, Helper_Admin::getPluginOptions( ''storage'', ''cck_'', false, false, true ) );\\r\\n$form = JHtml::_( ''select.genericlist'', $options, $name, ''class=\\"inputbox select\\" ''.$field->attributes, ''value'', ''text'', $value );","preparestore":""}' WHERE `id` = 28;

UPDATE `#__cck_core_fields` SET `options` = 'Allowed=||Allowed Hidden=hidden||Not Allowed=none||location=optgroup||Administrator Only=admin||Site Only=site' WHERE `id` = 276;
UPDATE `#__cck_core_fields` SET `options` = 'No=0||Yes=optgroup||Yes for Everyone=1||Yes for Super Admin=2||Config No Search=optgroup||Yes for Everyone=-1||Yes for Super Admin=-2' WHERE `id` = 174;

UPDATE `#__cck_core_fields` SET `options` = 'Alter Table=1' WHERE `id` = 75;
UPDATE `#__cck_core_fields` SET `options` = 'Alter Original Type=0||Alter Original Field=2||Base Table=optgroup||Alter Original Table=1' WHERE `id` = 192;

UPDATE `#__cck_core_fields` SET `options` = 'Custom=custom||Path=path||Presets=optgroup||Base=base||Current=current||Root=root' WHERE `id` = 279;

INSERT IGNORE INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_cck`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES
(624, 'Article Alias (EN)', 'art_alias_en', 22, 'text', '', 1, 'Alias EN', '', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'standard', '', 'joomla_article', '#__cck_store_item_content', 'alias_en', '', '', '', 0, '0000-00-00 00:00:00'),
(625, 'Article Alias (FR)', 'art_alias_fr', 22, 'text', '', 1, 'Alias FR', '', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'standard', '', 'joomla_article', '#__cck_store_item_content', 'alias_fr', '', '', '', 0, '0000-00-00 00:00:00'),
(626, 'Category Alias (EN)', 'cat_alias_en', 23, 'text', '', 1, 'Alias EN', '', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'standard', '', 'joomla_category', '#__cck_store_item_categories', 'alias_en', '', '', '', 0, '0000-00-00 00:00:00'),
(627, 'Category Alias (FR)', 'cat_alias_fr', 23, 'text', '', 1, 'Alias FR', '', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'standard', '', 'joomla_category', '#__cck_store_item_categories', 'alias_fr', '', '', '', 0, '0000-00-00 00:00:00'),
(628, 'Core MetaDesc', 'core_metadesc', 3, 'text', '', 0, 'Override MetaDesc', '', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'dev', '', '', '', 'options[metadesc]', '', '', '', 0, '0000-00-00 00:00:00'),
(629, 'Core SEF Canonical', 'core_sef_canonical', 3, 'select_simple', '', 0, 'SEF Canonical List', 'Use Global', 3, '', '', '', 'Canonical List All=0||Use Canonical=optgroup||Canonical List One=1||Canonical List Pages=2||Canonical List Pages Nav=3', '{\"options\":[]}', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'dev', '', '', '', 'options[sef_canonical]', '', '', '', 0, '0000-00-00 00:00:00'),
(630, 'Button Grp (Form) Alternative', 'button_grp_form_alternative', 3, 'group', '', 1, 'clear', '', 3, '', '', '', '', '', 0, 255, 32, 0, 1, 0, 0, '', 0, '', 'button_grp_form_alternative', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'none', '', '', '', 'button_grp_form_alternative', '', '', '', 0, '0000-00-00 00:00:00');

CREATE TABLE IF NOT EXISTS `#__cck_store_item_content` (
  `id` int(10) unsigned NOT NULL,
  `cck` varchar(50) NOT NULL,
  `alias_en` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `alias_fr` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_alias_en` (`alias_en`(191)),
  KEY `idx_alias_fr` (`alias_fr`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cck_store_item_categories` (
  `id` int(10) unsigned NOT NULL,
  `cck` varchar(50) NOT NULL,
  `alias_en` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `alias_fr` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_alias_en` (`alias_en`(191)),
  KEY `idx_alias_fr` (`alias_fr`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `#__cck_core_types` (`id`, `asset_id`, `title`, `name`, `alias`, `folder`, `template_admin`, `template_site`, `template_content`, `template_intro`, `description`, `indexed`, `published`, `options_admin`, `options_site`, `options_content`, `options_intro`, `location`, `locked`, `parent`, `parent_inherit`, `storage_location`, `stylesheets`, `version`, `checked_out`, `checked_out_time`, `access`, `created_date`, `created_user_id`, `modified_date`, `modified_user_id`) VALUES
(39, 0, 'Button Grp (Form) Alternative', 'button_grp_form_alternative', '', 3, 15, 16, 9, 9, '', 'none', 0, '', '', '', '', 'none', 1, '', 0, 'none', '', 3, 0, '0000-00-00 00:00:00', 3, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0);
