
ALTER TABLE `#__cck_core_searchs` ADD `sef_route` VARCHAR( 50 ) NOT NULL AFTER `location`;

UPDATE `#__cck_core_fields` SET `options` = "Joomla=optgroup||Use Native=0||SEBLOD=optgroup||SEF Mode Alias=23||SEF Mode Alias Safe=24||SEF Mode Id=22||SEF Mode Id Alias=2||SEBLOD Advanced=optgroup||SEF Mode Parent Alias=43||SEF Mode Parent Id=42||SEF Mode Parent Id Alias=4||SEF Mode Type Alias=33||SEF Mode Type Id=32||SEF Mode Type Id Alias=3||SEBLOD Deprecated=optgroup||Optimized=1" WHERE `id` = 177;