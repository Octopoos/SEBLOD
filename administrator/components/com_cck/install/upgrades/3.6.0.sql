
ALTER TABLE `#__cck_core_types` ADD `parent` VARCHAR(50) NOT NULL AFTER `location`;
ALTER TABLE `#__cck_core_types` ADD `access` INT(10) UNSIGNED NOT NULL DEFAULT '3' AFTER `checked_out_time`;
ALTER TABLE `#__cck_core_types` ADD `created_date` DATETIME NOT NULL AFTER `access`;
ALTER TABLE `#__cck_core_types` ADD `created_user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `created_date`;
ALTER TABLE `#__cck_core_types` ADD `modified_date` DATETIME NOT NULL AFTER `created_user_id`;
ALTER TABLE `#__cck_core_types` ADD `modified_user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `modified_date`;

-- --------

ALTER TABLE `#__cck_core_objects` ADD `context` VARCHAR(50) NOT NULL AFTER `component`;
UPDATE `#__cck_core_objects` SET `context` = "com_content.article" WHERE `name` = "joomla_article";
UPDATE `#__cck_core_objects` SET `context` = "com_categories.category" WHERE `name` = "joomla_category";

-- --------

UPDATE `#__cck_core_fields` SET `options` = 'Hide=-2||Standard=optgroup||Above=-1||Below=0||Both=1||Infinite=optgroup||Click=2' WHERE `id` = 244;

UPDATE `#__cck_core_fields` SET `published` = 1 WHERE `id` IN (508,509,510,511,512,513,514,515,516);

UPDATE `#__cck_core_fields` SET `options` = 'Task Cancel=cancel||Task Save=apply||Task Save and Close=save||Task Save and New=save2new||Task Save and Redirect=save2redirect||Task Save and Skip=save2skip||Task Save and View=save2view||Task Save as New=save2copy||SEBLOD Exporter Addon=optgroup||Task Export=export||SEBLOD Toolbox Addon=optgroup||Task Processing=process' WHERE `id` = 486;

UPDATE `#__cck_core_fields` SET `options` = REPLACE( `options2`, 'JFactory::getApplication()->getCfg(', 'JFactory::getConfig()->get(' ) WHERE `id` = 274;

INSERT IGNORE INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES
(530, 'Button Save as New', 'button_save_as_new', 3, 'button_submit', '', 1, 'Save as New', ' ', 3, '', '', '', '', '{"icon":"copy","task":"save2copy","task_id_export":"","task_id_process":"","alt_link_text":"","alt_link":"","alt_link_options":"","itemid":"","custom":"","task_id":""}', 0, 255, 32, 0, 0, 0, 0, '', 1, '', '', '', '', 0, 0, 0, 0, 1, 0, 1, '', '', 'none', '', '', 'button_save_as_new', '', '', '', 0, '0000-00-00 00:00:00'),
(531, 'Core Parent (Type)', 'core_parent_type', 3, 'select_dynamic', '', 0, 'Parent', 'None', 3, '', '', '', '', '{"query":"","table":"#__cck_core_types","name":"title","where":"parent = \\"\\" AND storage_location != \\"none\\"","value":"name","orderby":"title","orderby_direction":"ASC","limit":"","language_detection":"joomla","language_codes":"EN,GB,US,FR","language_default":"EN","attr1":"","attr2":"","attr3":"","attr4":"","attr5":"","attr6":""}', 0, 255, 32, 0, 0, 0, 0, ',', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'dev', '', '', 'parent', '', '', '', 0, '0000-00-00 00:00:00'),
(532, 'Icon Add', 'icon_add', 3, 'icon', '', 1, '', ' ', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, 'plus', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'none', '', '', 'icon_add', '', '', '', 0, '0000-00-00 00:00:00');

-- --------

UPDATE `#__cck_core_fields` SET `options` = 'Never=0||Always=3||Workflow=optgroup||Add=1||Edit=2' WHERE `id` = 123;
UPDATE `#__cck_core_fields` SET `options` = 'Joomla=optgroup||Checkbox=selection||Checkbox Label For=selection_label||Featured=featured||Increment=increment||Sort=sort||Status=state||SEBLOD=optgroup||Form=form' WHERE `id` = 271;
UPDATE `#__cck_core_fields` SET `defaultvalue` = 'none', `options` = 'None=none||Smart Search Indexing=optgroup||Content=content||Intro=intro' WHERE `id` = 257;