
ALTER TABLE  `#__cck_core_sites` ADD  `aliases` VARCHAR( 512 ) NOT NULL AFTER `name`;

ALTER TABLE  `#__cck_core_searchs` ADD  `stylesheets` VARCHAR( 5 ) NOT NULL AFTER `storage_location`;
ALTER TABLE  `#__cck_core_types` ADD  `stylesheets` VARCHAR( 5 ) NOT NULL AFTER `storage_location`;

ALTER TABLE  `#__cck_core_type_field` ADD  `markup` VARCHAR( 50 ) NOT NULL COMMENT 'admin,site,intro,content' AFTER `live_value`;
ALTER TABLE  `#__cck_core_search_field` ADD  `markup` VARCHAR( 50 ) NOT NULL COMMENT 'search,list,item' AFTER `live_value`;

INSERT IGNORE INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES
(523, 'Core CSS Core', 'core_css_core', 3, 'select_simple', '', 0, 'Core CSS', 'Use Global', 3, '', '', '', 'Base=8||None=0||All Views=optgroup||All=1||Specific=-1||Content Views Only=optgroup||All=2||Specific=-2||Form Views Only=optgroup||All=3||Specific=-3', '{"options":[]}', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'dev', '', '', 'css_core', '', '', '', 0, '0000-00-00 00:00:00'),
(526, 'Icon Edit', 'icon_edit', 3, 'icon', '', 1, 'Edit', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, 'edit', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'none', '', '', 'icon_edit', '', '', '', 0, '0000-00-00 00:00:00'),
(524, 'Icon Delete', 'icon_delete', 3, 'icon', '', 1, 'Delete', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, 'delete', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'none', '', '', 'icon_delete', '', '', '', 0, '0000-00-00 00:00:00'),
(529, 'Icon View', 'icon_view', 3, 'icon', '', 1, 'View', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, 'eye', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'none', '', '', 'icon_view', '', '', '', 0, '0000-00-00 00:00:00'),
(527, 'Icon Preview', 'icon_preview', 3, 'icon', '', 1, 'Preview', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, 'zoom-in', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'none', '', '', 'icon_preview', '', '', '', 0, '0000-00-00 00:00:00'),
(528, 'Icon Trash', 'icon_trash', 3, 'icon', '', 1, 'Trash', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, 'trash', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'none', '', '', 'icon_trash', '', '', '', 0, '0000-00-00 00:00:00'),
(525, 'Icon Download', 'icon_download', 3, 'icon', '', 1, 'Download', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, 'download', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'none', '', '', 'icon_download', '', '', '', 0, '0000-00-00 00:00:00');

UPDATE `#__cck_core_fields` SET `options` = 'Checkbox=selection||Checkbox Label For=selection_label||Featured=featured||Increment=increment||Status=state' WHERE `id` = 271;
UPDATE `#__cck_core_fields` SET `options` = REPLACE( `options`, 'equalizer', 'edit||equalizer' ) WHERE `id` = 289;
UPDATE `#__cck_core_fields` SET `options` = 'Standard List View=0||Intermediate List View=2||Advanced Item View=1', `css` = 'max-width-180' WHERE `id` = 454;

UPDATE `#__extensions` SET `enabled` = '1' WHERE `folder` = 'cck_field' AND `element` IN ("icon");
UPDATE `#__extensions` SET `enabled` = '1' WHERE `folder` = 'cck_field_restriction' AND `element` IN ("cck_workflow");