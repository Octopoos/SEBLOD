#!/usr/bin/env php
<?php
defined( '_JEXEC' ) || define( '_JEXEC', 1 );

const JOOMLA_MINIMUM_PHP	=	'8.3.0';

if ( version_compare( PHP_VERSION, JOOMLA_MINIMUM_PHP, '<' ) ) {
	echo 'PHP version too old. Minimum required: '.JOOMLA_MINIMUM_PHP.PHP_EOL;
	exit;
}

if ( file_exists( dirname( __DIR__ ).'/defines.php' ) ) {
	require_once dirname( __DIR__ ).'/defines.php';
}
if ( !defined( '_JDEFINES' ) ) {
	define( 'JPATH_BASE', dirname( __DIR__ ) );
	require_once JPATH_BASE.'/includes/defines.php';
}
if ( !file_exists( JPATH_LIBRARIES.'/vendor/autoload.php' ) || !is_dir( JPATH_ROOT.'/media/vendor' ) ) {
	echo 'Missing vendor dependencies.'.PHP_EOL;
	exit;
}
if ( !file_exists( JPATH_CONFIGURATION.'/configuration.php' ) || filesize( JPATH_CONFIGURATION.'/configuration.php' ) < 10 ) {
	echo 'Joomla is not installed.'.PHP_EOL;
	exit;
}

require_once JPATH_BASE.'/includes/framework.php';

// Bootstrap SEBLOD
require_once JPATH_LIBRARIES.'/cck/_/cck.php';
\JLoader::registerPrefix( 'JCck', JPATH_LIBRARIES.'/cck/_' );

// Bootstrap Joomla!
$container	=	\Joomla\CMS\Factory::getContainer();

// Joomla! Session Aliases 
$container->alias( 'session', 'session.cli' )
		  ->alias( 'JSession', 'session.cli' )
		  ->alias( \Joomla\CMS\Session\Session::class, 'session.cli' )
		  ->alias( \Joomla\Session\Session::class, 'session.cli' )
		  ->alias( \Joomla\Session\SessionInterface::class, 'session.cli' );

// Joomla! Application Console
$app								=	$container->get(\Joomla\Console\Application::class);
\Joomla\CMS\Factory::$application	=	$app;

// SEBLOD Stuff
\Joomla\CMS\Plugin\PluginHelper::importPlugin( 'cck_storage_location' );

// Cli
class CckJobCli
{
	// doExecute
	public function doExecute()
	{
		JCckToolbox::run( substr( get_called_class(), 10 ) );
	}
}
?>