
CREATE TABLE IF NOT EXISTS `#__cck_more_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `extension` varchar(50) NOT NULL,
  `folder` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `options` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_extension` (`extension`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=500 ;