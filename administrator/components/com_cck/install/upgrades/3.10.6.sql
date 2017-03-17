
ALTER TABLE `#__cck_core_sites` ADD `context` VARCHAR(20) NOT NULL AFTER `name`;

UPDATE `#__cck_core_fields` SET `options` = 'None=none||Basic=optgroup||Config Option Alphabetical=alpha||Config Option Most Popular=popular||Config Option Most Recent First=newest||Config Option Oldest First=oldest||Config Option Ordering=ordering||Advanced=optgroup||Ordering View Inherited=' WHERE `id` = 245;
UPDATE `#__cck_core_fields` SET `options` = 'Basic=optgroup||Config Option Alphabetical=alpha||Config Option Most Popular=popular||Config Option Most Recent First=newest||Config Option Oldest First=oldest||Config Option Ordering=ordering||Advanced=optgroup||Custom=-1' WHERE `id` = 246;