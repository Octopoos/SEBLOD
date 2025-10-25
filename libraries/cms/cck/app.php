<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: list.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Registry\Registry;

// JCckApp
class JCckApp
{
	protected static $callables			=	array();
	protected static $callables_map		=	array();
	protected static $data_map			=	array();
	protected static $incognito			=	array(
												'__call'=>'',
												'__construct'=>'',
												'_setTypeByName'=>'',
												'_setCallable'=>'',
												'_setMixin'=>'',
											);
	protected static $instances			=	array();

	protected $_options					=	null;

	protected $_callables				=	array();

	protected $_crypt					=	null;
	protected $_crypt_key				=	null;

	protected $_data					=	null;
	protected $_data_preset				=	array();
	protected $_data_preset_null		=	false;
	protected $_error					=	false;
	protected $_id						=	0;
	protected $_instance_base			=	null;
	protected $_is_new					=	false; /* TODO#SEBLOD: reset? */
	protected $_logs					=	array(); /* TODO#SEBLOD: reset? */
	protected $_name					=	'';
	protected $_object					=	'';
	protected $_pk						=	0;
	protected $_search_query			=	null; /* TODO#SEBLOD: reset? */
	protected $_search_results			=	null;

	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct

	// __construct
	public function __construct()
	{
		$this->_options			=	new Registry;
	}

	// getInstance
	public static function getInstance( $identifier = 'default' )
	{
		if ( $identifier == 'default' ) {
			$key	=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_more_webservices_apps WHERE type = "platform" AND featured = 1' );
		} else {
			$key	=	$identifier;
		}

		if ( !isset( self::$instances[$key] ) ) {
			self::$instances[$key]	=	new JCckApp;

			self::$instances[$key]->load( $key );
		}

		return self::$instances[$key];
	}

	// setOptions
	public function setOptions( $options )
	{
		$this->_options	=	new Registry( $options );

		return $this;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Do

	// clear
	public function clear()
	{
		$this->_error	=	false;

		return $this;
	}

	// load (^)
	public function load( $identifier, $items = null )
	{
		$this->reset();

		if ( !$this->_setTypeByName( $identifier ) ) {
			$this->_error	=	true;

			return $this;
		}

		return $this;
	}

	// loadDefault (^)
	public function loadDefault()
	{
		$this->reset();

		$identifier	=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_more_webservices_apps WHERE type = "platform" AND featured = 1' );

		if ( !$this->_setTypeByName( $identifier ) ) {
			$this->_error	=	true;

			return $this;
		}

		return $this;
	}

	// log
	protected function log( $type, $message )
	{
		if ( !isset( $this->_logs[$type] ) ) {
			$this->_logs[$type]	=	array();
		}

		$this->_logs[$type][]	=	$message;
	}

	// reset
	public function reset( $complete = false )
	{
		$this->clear();

		$this->_data				=	null;
		$this->_id					=	0;
		$this->_pk					=	0;

		return $this;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Get

	// getPk
	public function getPk()
	{
		return (int)$this->_pk;
	}

	// getLog
	public function getLog()
	{
		return $this->_logs;
	}

	// getName
	public function getName()
	{
		return $this->_name;
	}

	// hasCallable
	public function hasCallable( $name )
	{
		$scope	=	self::$callables_map[$name];

		if ( $scope == 'global' ) {
			if ( !isset( self::$callables[$name] ) ) {
				return false;
			}
		} else {
			if ( !isset( $this->_callables[$name] ) ) {
				return false;
			}
		}

		return true;
	}

	// isSuccessful
	public function isSuccessful()
	{
		return $this->_error ? false : true;
	}

	// isNew
	protected function isNew()
	{
		return $this->_is_new;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Crypt

	// decrypt
	public function decrypt( $data )
	{
		if ( $this->_crypt === null ) {
			return;
		}

		return $this->_crypt->decrypt( $data, $this->_crypt_key );
	}

	// encrypt
	public function encrypt( $data )
	{
		return $this->_crypt->encrypt( $data, $this->_crypt_key );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Misc

	// __call
	public function __call( $method, $parameters )
	{
		if ( !$this->hasCallable( $method ) ) {
			throw new BadMethodCallException( 'Method not found: '.$method );
		}

		$scope	=	self::$callables_map[$method];

		if ( $scope == 'global' ) {
			$callable	=	self::$callables[$method];
		} else {
			$callable	=	$this->_callables[$method];
		}

		if ( $callable instanceof Closure ) {
			return call_user_func_array( $callable->bindTo( $this, static::class ), $parameters );
		}

		return call_user_func_array( $callable, $parameters );
	}

	// dump
	public function dump( $scope = 'this' )
	{
		if ( !function_exists( 'dump' ) ) {
			$this->log( 'notice', 'Function not found.' );

			return false;
		}

		if ( $scope == 'self' ) {
			/* TODO#SEBLOD4 */
		} elseif ( $scope == 'callable' ) {
			dump( $this->getCallable() );
		} elseif ( $scope == 'log' ) {
			dump( $this->getLog() );
		} else {
			dump( $this->_callables, 'callables' );
			dump( $this->_error, 'error' );
			dump( $this->_id, 'id' );
			dump( $this->_logs, 'logs' );
			dump( $this->_name, 'name' );
			dump( $this->_object, 'object' );
			dump( $this->_pk, 'pk' );

			if ( $this->_instance_base ) {
				dump( $this->_instance_base, 'base' );
			}

			/* TODO#SEBLOD4 */
		}

		return true;
	}

	// extend
	public function extend( $path, $scope = 'instance' )
	{
		if ( !is_file( $path ) ) {
			$this->_error	=	true;

			return $this;
		}

		ob_start();
		include $path;
		ob_get_clean();

		$this->_setMixin( $mixin, $scope );
	}

	// _setCallable
	protected function _setCallable( $name, $callable, $scope )
	{
		if ( $scope == 'global' ) {
			self::$callables[$name]		=	$callable;
		} else {
			$this->_callables[$name]	=	$callable;
		}

		self::$callables_map[$name]	=	$scope;
	}

	// _setMixin
	protected function _setMixin( $mixin, $scope )
	{
		$methods	=	(new ReflectionClass( $mixin ) )->getMethods( ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED );

		foreach ( $methods as $method ) {
			$method->setAccessible( true );

			$this->_setCallable( $method->name, $method->invoke( $mixin ), $scope );
		}
	}

	// _setTypeByName
	protected function _setTypeByName( $identifier )
	{
		$query	=	'SELECT a.id AS pk, a.nonce, a.options'
				.	' FROM #__cck_more_webservices_apps AS a';

		if ( is_numeric( $identifier) ) {
			$query	.=	' WHERE a.id = '.(int)$identifier;
		} else {
			$query	.=	' WHERE a.name = "'.$identifier.'"';
		}
		
		$core	=	JCckDatabase::loadObject( $query );
		
		if ( !( is_object( $core ) && $core->pk ) ) {
			return false;
		}

		$this->_id					=	0;
		$this->_pk					=	$core->pk;
		$this->_name				=	$identifier;

		$options    =   json_decode( $core->options, true );

		if ( $core->nonce !== null ) {
			if ( JCck::on( '4' ) ) {
				require_once JPATH_SITE.'/libraries/cck/base/app_crypt.php';
			} else {
				require_once JPATH_SITE.'/libraries/cck/base/app_crypt3.php';
			}

			$this->_crypt		=	new JcckAppCrypt;

			$this->_crypt->setNonce( $core->nonce );

			$this->_crypt_key	=	$this->_crypt->getkey(
										$this->_convertKey( $this->_getKey( $options['key_private'] ?? '' ) ),
										$this->_convertKey( $this->_getKey( $options['key_public'] ?? '' ) )
									);
		}

		return true;
	}

	// _convertKey
	protected function _convertKey( $data )
	{
		if ( JCck::is( '7.0' ) ) { // UpSideDown
			return base64_encode( $data );
		} else {
			return strlen( $data ) % 2 === 0 ? hex2bin( $data ) : '';
		}
	}

	// _getKey
	protected function _getKey( $key )
	{
		if ( isset( $key[0] ) && $key[0] === '@' ) {
			$key	=	getenv( substr( $key, 1 ) );
		} elseif ( strpos( $key, '/' ) !== false && strpos( $key, '.' ) !== false ) {
			$dir	=	JPATH_SITE;
			$path	=	$key;

			if ( strpos( $path, '../' ) !== false ) {
				while ( 1 ) {
					if ( strpos( $path, '../' ) !== false ) {
						$dir	=	dirname( $dir );
						$path	=	substr( $path, 3 );
					} else {
						break;
					}
				}
			} elseif ( $path[0] === '.' ) {
				$path	=	substr( $path, 1 );
			}
			if ( $path[0] !== '/' ) {
				$path	=	'/'.$path;
			}

			if ( is_file( $dir.$path ) ) {
				$key	=	trim( file_get_contents( $dir.$path ) );
			}

			return $key;
		} else {
			return '';
		}

		return $key;
	}
}
?>