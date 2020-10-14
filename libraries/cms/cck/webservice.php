<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: webservice.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckWebservice
abstract class JCckWebservice
{
	public static $_me			=	'cck_webservices';
	public static $_config		=	null;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Config
	
	// _setConfig
	public static function _setConfig()
	{		
		if ( self::$_config ) {
			return self::$_config;
		}
		
		if ( JCckDatabaseCache::loadResult( 'SELECT extension_id FROM #__extensions WHERE type = "component" AND element = "'.'com_'.self::$_me.'"' ) > 0 ) {
			$config			=	new stdClass;
			$config->params =	JComponentHelper::getParams( 'com_'.self::$_me );
		} else {
			$config			=	new stdClass;
			
			$config->params	=	new JRegistry;
			$config->params->set( 'KO', true );
		}
		
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

	// call
	public static function call( $name, $data = array(), $fields = array() )
	{
		$response		=	null;
		$webservice		=	self::getCall( $name );

		if ( !is_object( $webservice ) ) {
			return;
		}
		$allowed		=	array(
								'request'=>'',
								'response'=>'',
								'response_format'=>''
							);
		$config			=	array();
		$identifier		=	0;

		// Override
		if ( count( $data ) ) {
			foreach ( $data as $k=>$v ) {
				if ( !isset( $allowed[$k] ) ) {
					continue;
				}
				if ( isset( $webservice->$k ) ) {
					$webservice->$k	=	$v;
				}
			}
		}
		if ( isset( $data['request_id'] ) && $data['request_id'] != '' ) {
			$identifier		=	$data['request_id'];
		}
		$webservice->request	=	str_replace( '{id}', (string)$identifier, $webservice->request );
		
		JPluginHelper::importPlugin( 'cck_webservice' );

		JEventDispatcher::getInstance()->trigger( 'onCCK_WebserviceCall', array( &$webservice, $fields, $config ) );

		if ( isset( $webservice->response ) ) {
			$response	=	$webservice->response;
		}

		return $response;
	}

	// getCall
	public static function getCall( $name )
	{
		static $cache	=	array();
		
		if ( !isset( $cache[$name] ) ) {
			$cache[$name]	=	JCckDatabase::loadObject( 'SELECT a.id, b.name, b.type, b.options, a.options as options2, a.request, a.request_object, a.request_options, a.response, a.response_format'
														. ' FROM #__cck_more_webservices_calls AS a'
														. ' LEFT JOIN #__cck_more_webservices AS b ON b.id = a.webservice'
														. ' WHERE a.name = "'.$name.'" AND a.published = 1' );
		}
		
		return $cache[$name];
	}

	// run
	public static function run()
	{
		JPluginHelper::importPlugin( 'cck_webservice' );

		$config	=	array();
		$fields	=	array();
		$items	=	JCckDatabase::loadObjectList( 'SELECT id, webservice_object'
												. ' FROM #__cck_more_webservices_stack'
												. ' WHERE published = 1'
												. ' LIMIT 25' );

		JLoader::register( 'CCK_TableStack', JPATH_ADMINISTRATOR.'/components/com_cck_webservices/tables/stack.php' );

		foreach ( $items as $item ) {
			$table	=	JTable::getInstance( 'Stack', 'CCK_Table' );

			if ( !$table->load( $item->id ) ) {
				continue;
			}

			$webservice	=	json_decode( $item->webservice_object );

			JEventDispatcher::getInstance()->trigger( 'onCCK_WebserviceCall', array( &$webservice, $fields, $config ) );

			$table->bind( array( 'response'=>json_encode( $webservice->response ) ) );
			$table->store();

			if ( isset( $webservice->response_format ) && $webservice->response_format == 'json' ) {
				if ( isset( $webservice->response->status ) && $webservice->response->status != 'error' ) {
					$table->updateStatus( true );
				} else {
					$table->updateStatus( false );
				}
			} elseif ( 1 == 1 ) { // OK
				$table->updateStatus( true );
			}
		}
	}

	// stack
	public static function stack( $name, $data = array(), $fields = array() )
	{
		$response		=	null;
		$webservice		=	self::getCall( $name );

		if ( !is_object( $webservice ) ) {
			return false;
		}
		$allowed		=	array(
								'request'=>'',
								'response'=>'',
								'response_format'=>''
							);
		$config			=	array();
		$identifier		=	0;

		// Override
		if ( count( $data ) ) {
			foreach ( $data as $k=>$v ) {
				if ( !isset( $allowed[$k] ) ) {
					continue;
				}
				if ( isset( $webservice->$k ) ) {
					$webservice->$k	=	$v;
				}
			}
		}
		if ( isset( $data['request_id'] ) && $data['request_id'] != '' ) {
			$identifier		=	$data['request_id'];
		}
		$webservice->request	=	str_replace( '{id}', (string)$identifier, $webservice->request );
		
		JLoader::register( 'CCK_TableStack', JPATH_ADMINISTRATOR.'/components/com_cck_webservices/tables/stack.php' );

		$table	=	JTable::getInstance( 'Stack', 'CCK_Table' );
		
		$data	=	array(
						'webservice'=>$webservice->id,
						'webservice_object'=>json_encode( $webservice ),
						'request'=>$webservice->request,
						'published'=>1
					);

		$table->bind( $data );
		$table->check();

		return $table->store();
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Resource

	// input
	public static function input( $resource_name, $data )
	{
		if ( !is_file( JPATH_SITE.'/components/com_cck_webservices/models/api.php' ) ) {
			return false;
		}

		JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_cck_webservices/models' );

		$model	=	JModelLegacy::getInstance( 'Api', 'CCK_WebservicesModel', array( 'ignore_request'=>true ) );
		
		return $model->doInput( $resource_name, $data );
	}

	// inputFromFile
	public static function inputFromFile( $resource_name, $path )
	{
		if ( !is_file( JPATH_SITE.'/components/com_cck_webservices/models/api.php' ) ) {
			return false;
		}
		if ( !is_file( $path ) ) {
			return false;
		}

		JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_cck_webservices/models' );

		$buffer	=	file_get_contents( $path );
		$model	=	JModelLegacy::getInstance( 'Api', 'CCK_WebservicesModel', array( 'ignore_request'=>true ) );
		$res	=	$model->doInput( $resource_name, $buffer );

		return true;
	}

	// output
	public static function output( $resource_name, $resource_config = array() )
	{
		if ( !is_file( JPATH_SITE.'/components/com_cck_webservices/models/api.php' ) ) {
			return false;
		}

		JModelLegacy::addIncludePath( JPATH_SITE.'/components/com_cck_webservices/models' );

		$model	=	JModelLegacy::getInstance( 'Api', 'CCK_WebservicesModel', array( 'ignore_request'=>true ) );

		return $model->doOutput( $resource_name, $resource_config );
	}
}
?>