
CREATE TABLE IF NOT EXISTS `#__cck_more_toolbox_processings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` varchar(5120) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `scriptfile` text NOT NULL,
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=50 ;

RENAME TABLE `#__cck_more_toolbox_processings` TO `#__cck_more_processings`;

ALTER TABLE `#__cck_more_processings` ADD `folder` INT NOT NULL DEFAULT '1' AFTER `name`;

UPDATE `#__cck_core_fields` SET `options2` = '{"query":"","table":"#__cck_more_processings","name":"title","where":"published=1","value":"id","orderby":"title","orderby_direction":"ASC","attr1":"","attr2":"","attr3":"","limit":"","language_detection":"joomla","language_codes":"EN,GB,US,FR","language_default":"EN"}' WHERE `id` = 522;

UPDATE `#__extensions` SET `enabled` = '0' WHERE `type` = "plugin" AND `element` = "cck_toolbox" AND `folder` = "user";