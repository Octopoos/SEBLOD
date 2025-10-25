
ALTER TABLE `#__cck_core_type_field` ADD `notes` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `access`;
ALTER TABLE `#__cck_core_search_field` ADD `notes` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `access`;

ALTER TABLE `#__cck_more_jobs` ADD `cron_task_state` TINYINT(3) NOT NULL DEFAULT '0' AFTER `checked_out_time`;
ALTER TABLE `#__cck_more_jobs` ADD `cron_task_executed` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `cron_task_state`;

ALTER TABLE `#__cck_more_processings` ADD `cron_task_state` TINYINT(3) NOT NULL DEFAULT '0' AFTER `checked_out_time`;
ALTER TABLE `#__cck_more_processings` ADD `cron_task_executed` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `cron_task_state`;