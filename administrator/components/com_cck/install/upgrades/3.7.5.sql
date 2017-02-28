
UPDATE `#__cck_core_fields` SET `options` = 'All Items=0||Standard=optgroup||1||2||3||4||5||6||8||9||10||12||15||20||25||30||50||100||Advanced=optgroup||200||300||400||500||600||700||800||900||1000||2000||3000||4000||5000||endgroup||Use Native=-1' WHERE `id` = 172;
UPDATE `#__cck_core_fields` SET `options` = 'Hide=-2||Standard=optgroup||Above=-1||Below=0||Both=1||Infinite=optgroup||Click=2||Load=8' WHERE `id` = 244;

CREATE TABLE IF NOT EXISTS `#__cck_more_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `folder` int(11) NOT NULL DEFAULT '1',
  `type` varchar(50) NOT NULL,
  `description` varchar(5120) NOT NULL,
  `options` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=50 ;


CREATE TABLE IF NOT EXISTS `#__cck_more_job_processing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `processing_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`),
  KEY `processing_id` (`processing_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------