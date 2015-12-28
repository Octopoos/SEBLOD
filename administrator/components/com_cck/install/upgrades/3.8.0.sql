
UPDATE `#__cck_core_fields` SET `bool3` = '1' WHERE `type` = "jform_tag";
UPDATE `#__cck_core_fields` SET `selectlabel` = 'None', `options` = 'Auto=-1||Component=component||Raw=raw' WHERE `id` = 230;

UPDATE `#__cck_core_fields` SET `options` = 'Joomla=optgroup||Activation=activation||Block=block||Checkbox=selection||Checkbox Label For=selection_label||Featured=featured||Increment=increment||Reordering=sort||Status=state||SEBLOD=optgroup||Form=form||Hidden=form_hidden||Form Disabled=form_disabled' WHERE `id` = 271;

INSERT IGNORE INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_cck`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES
(542, 'Core List Display Alt', 'core_list_display_alt', 3, 'select_simple', '', 0, 'Display Alt', ' ', 3, '', '', '0', 'Yes=1||No=0', '{"options":[]}', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'dev', '', '', '', 'display_alt', '', '', '', 0, '0000-00-00 00:00:00');

-- --------