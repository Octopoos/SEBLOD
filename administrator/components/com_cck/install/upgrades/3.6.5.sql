
ALTER TABLE `#__cck_core_fields` ADD `storage_cck` VARCHAR(50) NOT NULL AFTER `storage`;

-- --------

UPDATE `#__cck_core_fields` SET `options` = 'Component=component||Raw=raw' WHERE `id` = 230;
UPDATE `#__cck_core_fields` SET `options` = 'Joomla=optgroup||Checkbox=selection||Checkbox Label For=selection_label||Featured=featured||Increment=increment||Reordering=sort||Status=state||SEBLOD=optgroup||Form=form||Hidden=form_hidden||Form Disabled=form_disabled' WHERE `id` = 271;
