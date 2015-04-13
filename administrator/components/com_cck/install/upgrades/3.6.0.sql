
UPDATE `#__cck_core_fields` SET `options` = 'Hide=-2||Standard=optgroup||Above=-1||Below=0||Both=1||Infinite=optgroup||Click=2' WHERE `id` = 244;

UPDATE `#__cck_core_fields` SET `published` = 1 WHERE `id` IN (508,509,510,511,512,513,514,515,516);

UPDATE `#__cck_core_fields` SET `options` = 'Task Cancel=cancel||Task Save=apply||Task Save and Close=save||Task Save and New=save2new||Task Save and Redirect=save2redirect||Task Save and Skip=save2skip||Task Save and View=save2view||Task Save as New=save2copy||SEBLOD Exporter Addon=optgroup||Task Export=export||SEBLOD Toolbox Addon=optgroup||Task Processing=process' WHERE `id` = 486;

UPDATE `#__cck_core_fields` SET `options` = REPLACE( `options`, 'JFactory::getApplication()->getCfg(', 'JFactory::getConfig()->get(' ) WHERE `id` = 274;