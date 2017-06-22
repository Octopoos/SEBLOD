<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

// Set flag that this is a parent file.
const _JEXEC = 1;
const _DISPLAY_ERRORS = 0;

if ( defined( '_DISPLAY_ERRORS' ) && constant( '_DISPLAY_ERRORS' ) ) {
	error_reporting( E_ALL | E_NOTICE );
	ini_set( 'display_errors', 1 );
} else {
	error_reporting( 0 );
	ini_set( 'display_errors', 0 );
}

// Load system defines
if ( file_exists( dirname(__DIR__) . '/defines.php' ) ) {
	require_once dirname(__DIR__) . '/defines.php';
}
if ( !defined( '_JDEFINES' ) ) {
	define( 'JPATH_BASE', dirname(__DIR__) );
	require_once JPATH_BASE.'/includes/defines.php';
}

require_once JPATH_LIBRARIES.'/import.legacy.php';
require_once JPATH_LIBRARIES.'/cms.php';
/*
require_once JPATH_LIBRARIES.'/cms/application/helper.php';
require_once JPATH_LIBRARIES.'/joomla/filter/output.php';
require_once JPATH_LIBRARIES.'/joomla/string/string.php';
*/

// Load the configuration
require_once JPATH_CONFIGURATION.'/configuration.php';

JPluginHelper::importPlugin( 'cck_storage_location' );

// Cli
class CckJobCli extends JApplicationCli
{
	// _cleanName
	protected function _cleanName()
	{
		return str_replace( 'cck_job_', '', $this->_getName() );
	}

	// doExecute
	public function doExecute()
	{
		JCckToolbox::run( $this->_cleanName() );
	}
}
?>