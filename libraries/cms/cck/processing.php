<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: processing.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Registry\Registry;

// JCckProcessing
class JCckProcessing
{
	protected static $apps		=	array();
	protected static $apps_map	=	array();

	protected $_event			=	null;
	protected $_mode			=	null;
	protected $_options			=	null;
	protected $_path			=	null;

	protected $_data			=	array();
	protected $_error			=	false;
	protected $_instance		=	null;
	protected $_pk				=	0;
	protected $_type			=	'';

	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct

	// __construct
	public function __construct( $event, $path, $options = array(), $multiple = false )
	{
		$this->_event	=	$event;
		$this->_mode	=	$multiple;
		$this->_path	=	$path;
		$this->_options	=	new Registry( $options );
	}

	// execute
	public function execute( &$config, &$fields )
	{
		if ( !is_file( $this->_path ) ) {
			return;
		}

		// $args	=	func_get_args();

		$this->_data['config']	=	$config;
		$this->_data['fields']	=	$fields;

		if ( !isset( $this->_data['config']['tmp'] ) ) {
			$this->_data['config']['tmp']	=	array();
		}

		$this->_pk				=	isset( $this->_data['config']['pk'] ) ? $this->_data['config']['pk'] : 0;
		$this->_type			=	isset( $this->_data['config']['content_type'] ) ? $this->_data['config']['content_type'] : $this->_data['config']['type'];

		$this->run();

		$config	=	$this->_data['config'];
		$fields	=	$this->_data['fields'];
		
		unset( $this->_data );
		
		return $this->_error ? false : true;
	}

	// loadPropertyByKey
	protected function loadPropertyByKey( $key, $name, $table )
	{
		static $loaded	=	array();
		
		$idx		=	base64_encode( $name.'|'.$table );

		if ( !isset( $loaded[$idx] ) ) {
			$loaded[$idx]	=	array(
									'items'=>JCckDatabase::loadObjectList( 'SELECT id, '.JCckDatabase::quoteName( $name ).' FROM '.JCckDatabase::quoteName( $table ), 'id' ),
									'name'=>$name
								);
		}

		return isset( $loaded[$idx]['items'][$key] ) ? $loaded[$idx]['items'][$key]->{$loaded[$idx]['name']} : '';
	}

	// loadField
	protected function loadField( $name )
	{
		return JCckDatabase::loadObject( 'SELECT storage, storage_table, storage_field, storage_field2 FROM #__cck_core_fields WHERE name = "'.$name.'" AND storage_field != ""' );
	}

	// run
	public function run()
	{
		if ( $this->_mode ) {
			include $this->_path;
		} else {
			include_once $this->_path;
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Check

	// outProperty
	public function outProperty( $name )
	{
		if ( $this->_data['config']['output_attributes'] === null ) {
			return true;
		} elseif ( $this->_data['config']['output_attributes']['_'] === 1 && isset( $this->_data['config']['output_attributes'][$name] ) ) {
			return true;
		} elseif ( $this->_data['config']['output_attributes']['_'] === -1 && !isset( $this->_data['config']['output_attributes'][$name] ) ) {
			return true;
		}

		return false;
	}

	// isApi
	public function isApi()
	{
		return (bool)JFactory::getApplication()->input->getInt( 'cck_is_api', 0 );
	}

	// isJob
	public function isJob()
	{
		return (bool)JFactory::getApplication()->input->getInt( 'cck_is_job', 0 );
	}

	// isNew
	public function isNew()
	{
		if ( isset( $this->_data['config']['isNew'] ) ) {
			return (bool)$this->_data['config']['isNew'];
		} else {
			return $this->getPk() ? false : true;
		}
	}

	// isFirstItem
	// public function isFirstItem() {}

	// isLastItem
	// public function isLastItem() {}

	// isSecure
	public function isSecure()
	{
		if ( $this->_event ) {
			return true;
		}

		return false;
	}

	// isUi
	public function isUi()
	{
		return true;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Do

	// continue
	public function continue()
	{
		$this->_error	=	true;
	}	

	// sendMail
	// public function sendMail() {}

	// setError
	public function setError( $key = '', $value = '' )
	{
		// JCckTable::getInstance( '#__aa' )->save( array( 'data'=>'** ERROR **', 'note'=>$key.' -- '.$value ) );

		$this->_error	=	true;

		if ( array_key_exists( 'error', $this->_data['config'] ) ) {
			if ( $key != '' ) {
				$this->_data['config']['error']	=	array( $key=>$value );
			} else {
				$this->_data['config']['error']	=	true;
			}
		}
	}

	// setOutput
	public function setOutput( $output = array() )
	{
		if ( $output === 0 ) {
			$this->_data['config']['message_style']	=	0;
		} else {
			$this->_data['config']['error_output']	=	$output;
			$this->_error							=	true;
		}
	}

	// setProperty
	public function setProperty( $name, $value )
	{
		if ( is_array( $this->_data['fields'] ) ) {
			if ( is_array( $name ) ) {
				$this->_data['fields'][$name[0]]->{$name[1]}	=	$value;
			} else {
				$this->_data['fields'][$name]	=	$value;
			}
		} else {
			$this->_data['fields']->$name	=	$value;
		}
	}

	// setType
	public function setType( $content_type )
	{
		$this->_data['config']['type'] 	=	$content_type;
		
		JCckDatabase::execute( 'UPDATE #__cck_core SET cck="'.$content_type.'" WHERE id='.(int)$this->_data['config']['id'] );
	}

	// setValue
	public function setValue( $name, $value )
	{
		if ( isset( $this->_data['fields'][$name] ) ) {
			$this->_data['fields'][$name]->value	=	$value;

			$field	=	$this->loadField( $name );

			if ( is_object( $field ) && $field->storage_table && $field->storage_field ) {
				switch ( $field->storage ) {
					case 'json':
						$json	=	new Registry( $this->_data['config']['storages'][$field->storage_table][$field->storage_field] );
						$json->set( $field->storage_field2, $value );

						$this->_data['config']['storages'][$field->storage_table][$field->storage_field]	=	$json->toString( 'JSON', array( 'bitmask'=>JSON_UNESCAPED_UNICODE ) );

						if ( stripos( $this->_event, 'afterStore' ) !== false && $this->_pk ) {
							JCckDatabase::execute( 'UPDATE '.$field->storage_table.' SET '.$field->storage_field.'= "'.JCckDatabase::escape( $value ).'" WHERE id = '.(int)$this->_pk );
						}
						break;
					case 'standard':
						if ( isset( $this->_data['config']['storages'] ) ) {
							$this->_data['config']['storages'][$field->storage_table][$field->storage_field]	=	$value;
						}

						if ( stripos( $this->_event, 'afterStore' ) !== false && $this->_pk ) {
							JCckDatabase::execute( 'UPDATE '.$field->storage_table.' SET '.$field->storage_field.'= "'.$value.'" WHERE id = '.(int)$this->_pk );
						}
						break;
					default:
						break;
				}
			}
		}
	}

	// unset
	public function unset( $name )
	{
		if ( is_array( $this->_data['fields'] ) ) {
			unset( $this->_data['fields'][$name] );
		} else {
			unset( $this->_data['fields']->$name );
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Get

	// getApp
	public function getApp()
	{
		if ( !$this->_type ) {
			return (object)array( 'name'=>'' );
		}

		if ( !isset( self::$apps_map[$this->_type] ) ) {
			self::$apps_map[$this->_type]	=	JCckDatabase::loadResult( 'SELECT folder FROM #__cck_core_types WHERE name = "'.$this->_type.'"' );
		}
		if ( !isset( self::$apps[self::$apps_map[$this->_type]] ) ) {
			self::$apps[self::$apps_map[$this->_type]]			=	JCckDatabase::loadObject( 'SELECT name, params FROM #__cck_core_folders WHERE id = "'.self::$apps_map[$this->_type].'"' );
			self::$apps[self::$apps_map[$this->_type]]->params	=	new Registry( self::$apps[self::$apps_map[$this->_type]]->params );
		}

		return self::$apps[self::$apps_map[$this->_type]];
	}

	// getData
	public function getData()
	{
		return $this->_data['fields'];
	}

	// getMethod
	public function getMethod()
	{
		if ( isset( $this->_data['config']['method'] ) ) {
			return $this->_data['config']['method'];
		}

		return '';
	}

	// getId
	public function getId()
	{
		if ( isset( $this->_data['config']['id'] ) ) {
			return (int)$this->_data['config']['id'];
		}

		return 0;
	}

	// getIdentifier
	public function getIdentifier()
	{
		if ( isset( $this->_data['config']['resource_id'] ) && $this->_data['config']['resource_id'] ) {
			return $this->_data['config']['resource_id'];
		}

		return 0;
	}

	// getObject
	public function getObject()
	{
		if ( isset( $this->_data['config']['location'] ) ) {
			return $this->_data['config']['location'];
		}

		return '';
	}

	// getProperty
	public function getProperty( $name )
	{
		if ( is_array( $this->_data['fields'] ) ) {
			if ( isset( $this->_data['fields'][$name] ) ) {
				return $this->_data['fields'][$name];
			}
		} else {
			if ( isset( $this->_data['fields']->$name ) ) {
				return $this->_data['fields']->$name;
			}
		}
		
	}

	// getPk
	public function getPk()
	{
		if ( isset( $this->_data['config']['resource_id'] ) && $this->_data['config']['resource_id']
		  && !( isset( $this->_data['config']['resource_identifier'] ) && $this->_data['config']['resource_identifier'] != '' ) ) {
			return (int)$this->_data['config']['resource_id'];
		} elseif ( isset( $this->_data['config']['pk'] ) ) {
			return (int)$this->_data['config']['pk'];
		}

		return 0;
	}

	// getRedirectionUrl
	public function getRedirectionUrl()
	{
		if ( isset( $this->_data['config']['url'] ) ) {
			return $this->_data['config']['url'];
		}

		return '';
	}

	// getStage
	public function getStage()
	{
		if ( isset( $this->_data['config']['stage'] ) ) {
			return $this->_data['config']['stage'];
		}

		return -1;
	}

	// getTask
	public function getTask()
	{
		if ( isset( $this->_data['config']['task'] ) ) {
			return $this->_data['config']['task'];
		}

		return '';
	}

	// getTmp
	public function getTmp( $key = '' )
	{
		if ( $key ) {
			if ( isset( $this->_data['config']['tmp'][$key] ) ) {
				return $this->_data['config']['tmp'][$key];
			} else {
				return null;
			}
		} else {
			if ( isset( $this->_data['config']['tmp'] ) ) {
				return $this->_data['config']['tmp'];
			} else {
				return array();
			}
		}
	}

	// getType
	public function getType()
	{
		return $this->_type;
	}

	// getValue
	public function getValue( $name )
	{
		if ( isset( $this->_data['fields'][$name] ) ) {
			return isset( $this->_data['fields'][$name]->value ) ? $this->_data['fields'][$name]->value : '';
		}

		return '';
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Set

	// setDataLayer
	public function setDataLayer( $data )
	{
		// if ( isset( $this->_data['config']['stage'] ) && (int)$this->_data['config']['stage'] == -1 ) {
		// JCckDev::aa( $this->_data['config'], '@'.$this->getType().' '.$this->getPk() );
		JFactory::getSession()->set( 'cck.data_layer', $data );
	}

	// setTmp
	public function setTmp( $key, $data )
	{
		if ( isset( $this->_data['config']['tmp'] ) ) {
			$this->_data['config']['tmp'][$key]	=	$data;

			return true;
		}

		return false;
	}

	// setRedirectionUrl
	public function setRedirectionUrl( $url )
	{
		if ( isset( $this->_data['config']['url'] ) ) {
			$this->_data['config']['url']	=	$url;

			return true;
		}

		return false;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Trigger

	/*
	// trigger
	public function trigger( $event )
	{
		$path	=	JPATH_SITE.'/project/apps/'.$this->getApp()->name.'/trigger/'.$event.'/'.$this->getType().'.php';

		if ( is_file( $path ) ) {
			include_once $path;
		} else {
			$path	=	JPATH_SITE.'/project/apps/'.$this->getApp()->name.'/trigger/'.$event.'.php';

			if ( is_file( $path ) ) {
				include_once $path;
			}
		}
	}
	*/

	// -------- -------- -------- -------- -------- -------- -------- -------- // Misc

	// dump
	public function dump()
	{
		dump( $this->_data['config'], 'config' );
		dump( $this->_data['fields'], 'fields' );
	}
}
?>