
CREATE TABLE IF NOT EXISTS `#__cck_more_processings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `folder` int(11) NOT NULL DEFAULT '1',
  `type` varchar(50) NOT NULL,
  `description` varchar(5120) NOT NULL,
  `options` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `scriptfile` text NOT NULL,
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=500 ;

INSERT INTO `#__cck_more_processings` (`id`, `title`, `name`, `folder`, `type`, `description`, `options`, `ordering`, `published`, `scriptfile`, `checked_out`, `checked_out_time`) VALUES
(1, 'Customize (Store)', 'customize', 3, 'onCckPostBeforeStore', '', '{"output":"","output_path":"tmp\\/","output_extension":"txt","output_filename_date":"","content_types":"seb_site","manager":{"email":"seb_site_manager_email","password":"seb_site_manager_password","username":"","name":"seb_site_manager_name","first_name":"seb_site_manager_first_name","last_name":"seb_site_manager_last_name","bridge":"0","force_password":"0","set_author":"1"},"type":"6"}', 0, 1, '/media/cck/processings/sites/customize/customize.php', 0, '0000-00-00 00:00:00'),
(2, 'Complete', 'complete', 3, 'onCckConstructionBeforeSave', '', '{"output":"","output_path":"tmp\\/","output_extension":"txt","output_filename_date":""}', 0, 1, '/media/cck/processings/sites/complete/complete.php', 0, '0000-00-00 00:00:00'),
(3, 'Customize (Import)', 'customize', 3, 'onCckPostBeforeImport', '', '{"output":"","output_path":"tmp\\/","output_extension":"txt","output_filename_date":"","content_types":"seb_site","manager":{"email":"seb_site_manager_email","password":"seb_site_manager_password","username":"","name":"seb_site_manager_name","first_name":"seb_site_manager_first_name","last_name":"seb_site_manager_last_name","bridge":"0","force_password":"0","set_author":"1"},"type":"6"}', 0, 1, '/media/cck/processings/sites/customize/customize.php', 0, '0000-00-00 00:00:00');