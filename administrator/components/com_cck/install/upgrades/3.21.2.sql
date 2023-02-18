
UPDATE `#__cck_core_fields` SET `options2` = REPLACE( `options2`, '"task_id_process":', '"task_id":' ) WHERE `type` = "button_submit";
UPDATE `#__cck_core_fields` SET `options2` = REPLACE( `options2`, '"task_id_export":', '"task_id":' ) WHERE `type` = "button_submit";