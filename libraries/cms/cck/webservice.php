<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: webservice.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckWebservice
abstract class JCckWebservice
{
	public static $_me			=	'cck_webservices';
	public static $_config		=	NULL;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Config
	
	// _setConfig
	public static function _setConfig()
	{		
		if ( self::$_config ) {
			return self::$_config;
		}

		$config			=	new stdClass;
		$config->params =	JComponentHelper::getParams( 'com_'.self::$_me );
		
		self::$_config	=&	$config;
	}
	
	// getConfig
	public static function getConfig()
	{		
		if ( ! self::$_config ) {
			self::_setConfig();
		}
		
		return self::$_config;
	}
	
	// getConfig_Param
	public static function getConfig_Param( $name, $default = '' )
	{
		if ( ! self::$_config ) {
			self::_setConfig();
		}
		
		return self::$_config->params->get( $name, $default );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Call

	// getCall
	public static function getCall( $name )
	{
		static $cache	=	array();
		
		if ( !isset( $cache[$name] ) ) {
			$cache[$name]	=	JCckDatabase::loadObject( 'SELECT b.name, b.type, b.options, a.request, a.request_object, a.request_options, a.response, a.response_format'
														. 'FROM #__cck_more_webservices_calls AS a'
														. ' LEFT JOIN #__cck_more_webservices AS b ON b.id = a.webservice'
														. ' WHERE a.name = "'.$name.'"' );
		}
		
		return $cache[$name];
	}
}
?>