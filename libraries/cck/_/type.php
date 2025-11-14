<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: type.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

// JCckType
class JCckType
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
	protected $_parent					=	'';
	protected $_pk						=	0;
	protected $_relationships			=	array();

	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct

	// __construct
	public function __construct()
	{
		$this->_options			=	new Registry;
	}

	// getInstance
	public static function getInstance( $identifier = '' )
	{
		if ( !$identifier ) {
			return new JCckType;
		}

		$key	=	$identifier;

		if ( !isset( self::$instances[$key] ) ) {
			self::$instances[$key]	=	new JCckType;

			self::$instances[$key]->load( $identifier );
		}

		return self::$instances[$key];

	}

	// setInstance
	protected function setInstance( $table_instance_name, $load = false )
	{
		$method	=	'setInstance'.ucwords( $table_instance_name, '_' );

		if ( $this->$method() ) {
			if ( $load && $this->_pk ) {
				return $this->{'_instance_'.$table_instance_name}->load( $this->_pk );
			}
		}
		
		return true;
	}

	// setInstanceBase
	protected function setInstanceBase()
	{
		\JLoader::register( 'CCK_TableType', JPATH_ADMINISTRATOR.'/components/com_cck/tables/type.php' );

		$this->_instance_base	=	Table::getInstance( 'Type', 'CCK_Table' );
		$this->_setDataMap( 'base' );

		return true;
	}

	// unsetInstance
	protected function unsetInstance( $table_instance_name )
	{
		$this->{'_instance_'.$table_instance_name}	=	null;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Do

	// clear
	public function clear()
	{
		$this->_error	=	false;

		return $this;
	}

	// create (^)
	public function create( $content_type, $data )
	{
		$this->reset();

		// if ( !$this->_setContentByType( $content_type ) ) {
		// 	$this->reset();

		// 	$this->_error	=	true;

		// 	return $this;
		// }

		// if ( !$this->can( 'create' ) ) {
		// 	$this->log( 'error', 'Permissions denied.' );
		// 	$this->reset();

		// 	$this->_error	=	true;

		// 	return $this;
		// }
		
		$this->setInstance( 'base' );

		$data	=	$this->_getDataDispatch( $content_type, $data );

		// Preset may set an error
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		$this->_is_new	=	true;

		// Base
		if ( !( $this->save( 'base', $data['base'] ) ) ) {
			$this->_error	=	true;
			$this->_is_new	=	false;

			return $this;
		}

		// Core
		// $data_core	=	array(
		// 					'cck'=>$this->_type,
		// 					'pk'=>$this->_pk,
		// 					'storage_location'=>$this->_object,
		// 					'author_id'=>$this->getAuthor(),
		// 					'parent_id'=>$data['core']['parent_id'],
		// 					'date_time'=>$data['core']['date_time']
		// 				);
		// if ( !$data_core['author_id'] ) {
		// 	$data_core['author_id']	=	JFactory::getUser()->id;
		// }
		// if ( !( $this->save( 'core', $data_core ) ) ) {
		// 	$this->_error	=	true;
		// 	$this->_is_new	=	false;

		// 	return $this;
		// }
		
		$this->_is_new	=	false;

		// Keep it for later
		// self::$instances_map[$this->_id]				=	$this->_object.'_'.$this->_pk;
		// self::$instances[$this->_object.'_'.$this->_pk]	=	$this;
		
		return $this;
	}

	// load (^)
	public function load( $identifier )
	{
		$this->reset();

		if ( !$this->_setTypeByName( $identifier ) ) {
			$this->_error	=	true;

			return $this;
		}
		// if ( !$this->_instance_core->load( $this->_id ) ) {
		// 	$this->reset();

		// 	$this->_error	=	true;

		// 	return $this;
		// }
		if ( !$this->setInstance( 'base', true ) ) {
			$this->reset();

			$this->_error	=	true;
		
			return $this;
		}

		// if ( !isset( self::$instances_map[$this->_id] ) ) {
		// 	self::$instances_map[$this->_id]	=	$this->_object.'_'.$this->_pk;
		// }

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

		/* TODO#SEBLOD4 */
		$this->unsetInstance( 'base' );
		/* TODO#SEBLOD4 */

		if ( $complete ) {
			$this->_object	=	'';
			/* TODO#SEBLOD4 */
		}

		return $this;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Get

	// get
	public function get( $table_instance_name, $property = '', $default = '' )
	{
		static $names	=	array(
								'base'=>''
							);

		if ( isset( $names[$table_instance_name] ) ) {
			return $this->{'_instance_'.$table_instance_name}->get( $property, $default );
		} else {
			$this->log( 'notice', 'Instance unknown.' );

			return $default;
		}
	}

	// getCallable
	public function getCallable()
	{
		$items		=	array();

		/* TODO#SEBLOD4 */

		return $items;
	}

	// getContentInstance
	public function getContentInstance()
	{
		$content_object		=	$this->getContentObject();

		$content_instance	=	new $content_object;

		if ( $this->_object == 'free' ) {
			$content_instance->setTable( '#__cck_store_form_'.( $this->_parent ? $this->_parent : $this->_name ) );
		}

		// TODO: extend mixin(s)

		return $content_instance;
	}

	// getContentObject
	public function getContentObject()
	{
		if ( !is_file( JPATH_SITE.'/plugins/cck_storage_location/'.$this->_object.'/'.$this->_object.'.php' ) ) {
			return false;
		}

		require_once JPATH_SITE.'/plugins/cck_storage_location/'.$this->_object.'/'.$this->_object.'.php';

		$properties	=	array(
							'type_alias'
						);
		$properties	=	JCck::callFunc( 'plgCCK_Storage_Location'.$this->_object, 'getStaticProperties', $properties );

		return 'JCckContent'.$properties['type_alias'];
	}

	// getData
	public function getData()
	{
		if ( !isset( $this->_data ) ) {
			$this->_data	=	array();

			static $names	=	array(
									'base'=>''
								);
			
			foreach ( $names as $table_instance_name=>$null ) {
				if ( is_object( $this->{'_instance_'.$table_instance_name} ) ) {
					$data	=	$this->{'_instance_'.$table_instance_name}->getProperties();

					unset( $data['id'], $data['cck'] );

					$this->_data	=	array_merge( $this->_data, $data );
				}
			}
		}

		return $this->_data;
	}

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

	// getObject
	public function getObject()
	{
		return $this->_object;
	}

	// getProperty
	public function getProperty( $property, $default = '' )
	{
		if ( isset( self::$data_map[$property] ) ) {
			return $this->get( self::$data_map[$property], $property, $default );
		} else {
			$this->log( 'notice', 'Property unknown.' );
		}

		return $default;
	}

	// getRelationship
	public function getRelationship( $name )
	{
		if ( isset( $this->_relationships[$name] ) ) {
			return $this->_relationships[$name];
		}

		return null;
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

	// loadContentItem
	public function loadContentItem( $pk )
	{

	}

	// loadItem
	public function loadItem( $pk )
	{

	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Save

	// bind
	public function bind( $table_instance_name, $data )
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		$result	=	$this->{'_instance_'.$table_instance_name}->bind( $data );

		if ( !$result ) {
			$this->_error	=	true;
		}

		return $this;
	}

	// check
	public function check( $table_instance_name )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		return $this->{'_instance_'.$table_instance_name}->check();
	}

	// postSave
	protected function postSave( $table_instance_name, $data ) {}
	
	// preSave
	protected function preSave( $table_instance_name, &$data ) {}
	
	// save ($)
	public function save( $table_instance_name, $data = array() )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		if ( !$this->isNew() ) {
			// if ( !$this->can( 'save' ) ) {
			// 	$this->log( 'error', 'Permissions denied.' );

			// 	return false;
			// }
		}

		$this->preSave( $table_instance_name, $data );

		$this->bind( $table_instance_name, $data );
		$this->check( $table_instance_name );

		if ( $table_instance_name == 'base' ) {
			$result	=	$this->trigger( 'save', 'before' );

			if ( is_array( $result ) && in_array( false, $result, true ) ) {
				return false;
			}
		}

		// Let's make sure we have a valid instance		/* TODO#SEBLOD: should we move this check to the suitable function(s) */
		if ( !( $table_instance_name == 'base' || $table_instance_name == 'core' ) && empty( $this->{'_instance_'.$table_instance_name}->id ) ) {
			// $this->_fixDatabase( $table_instance_name );
		}

		$method	=	'save'.ucfirst( $table_instance_name );
		$status	=	$this->$method();

		if ( !$status ) {
			return $status;
		}

		switch ( $table_instance_name ) {
			case 'base':
				$this->_pk	=	$this->{'_instance_'.$table_instance_name}->id;
				
				// if ( $this->_instance_core->id ) {
				// 	$data_core	=	array();

				// 	if ( self::$objects[$this->_object]['properties']['author'] == self::$objects[$this->_object]['properties']['key'] ) {
				// 		$data_core['author_id']	=	$this->{'_instance_'.$table_instance_name}->get( self::$objects[$this->_object]['properties']['key'], 0 );
				// 	} elseif ( isset( $data[self::$objects[$this->_object]['properties']['author']] ) ) {
				// 		$data_core['author_id']	=	$data[self::$objects[$this->_object]['properties']['author']];
				// 	}
				// 	if ( isset( $data[self::$objects[$this->_object]['properties']['parent']] ) ) {
				// 		$data_core['parent_id']	=	$data[self::$objects[$this->_object]['properties']['parent']];
				// 	}
				// 	if ( count( $data_core ) ) {
				// 		$this->save( 'core', $data_core );
				// 	}
				// }
				break;
			case 'core':
				// $this->_id	=	$this->{'_instance_'.$table_instance_name}->id;
				
				// if ( property_exists( $this->_instance_base, self::$objects[$this->_object]['properties']['custom'] ) ) {
				// 	$this->_instance_base->{self::$objects[$this->_object]['properties']['custom']}	=	'::cck::'.$this->_id.'::/cck::';
				// 	$this->store( 'base' );
				// }
				break;
			default:
				break;
		}

		$this->postSave( $table_instance_name, $data );
		
		if ( $table_instance_name == 'base' ) {
			$this->trigger( 'save', 'after' );
		}

		return $status;
	}

	// saveBase
	protected function saveBase()
	{
		return $this->_instance_base->store();
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Trigger

	// trigger
	public function trigger( $task, $event )
	{
		/* TODO#SEBLOD4 */
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Do More

	// assign
	public function assign( $field_identifier, $client, $params = array() )
	{
		if ( !is_numeric( $field_identifier ) ) {
			$field_identifier	=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_core_fields WHERE name = "'.$field_identifier.'"' );
		}
		
		if ( $field_identifier ) {
			$table	=	JCckTableBatch::getInstance( '#__cck_core_type_field' );

			$count				=	$table->count( 'typeid = '.$this->getPk().' AND client = "'.$client.'"' );
			$params['client']	=	$client;
			$params['fieldid']	=	(string)$field_identifier;
			$params['ordering']	=	(string)( $count + 1 );

			$table->bind( array(
							$count=>(object)$params
						  ) );
			$table->check( array(
							'typeid'=>(string)$this->getPk()
						   ) );
			$table->store();
		}

		return $this;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Misc

	// __call
	public function __call( $method, $parameters )
	{
		if ( !$this->hasCallable( $method ) ) {
			throw new BadMethodCallException( 'Method not found.' );
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
		$dump	=	JCck::on( '4.0' ) ? 'dumpVar' : 'dump';

		if ( !function_exists( $dump ) ) {
			if ( $dump == 'dumpVar' ) {
				$dump	=	'dump';
			} else {
				$this->log( 'notice', 'Function not found.' );

				return false;	
			}
		}

		if ( $scope == 'self' ) {
			/* TODO#SEBLOD4 */
		} elseif ( $scope == 'callable' ) {
			$dump( $this->getCallable() );
		} elseif ( $scope == 'log' ) {
			$dump( $this->getLog() );
		} else {
			$dump( $this->_callables, 'callables' );
			$dump( $this->_error, 'error' );
			$dump( $this->_id, 'id' );
			$dump( $this->_logs, 'logs' );
			$dump( $this->_name, 'name' );
			$dump( $this->_object, 'object' );
			$dump( $this->_parent, 'parent' );
			$dump( $this->_pk, 'pk' );

			if ( $this->_instance_base ) {
				$dump( $this->_instance_base, 'base' );
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

	// _getDataDispatch
	protected function _getDataDispatch( $content_type, $data_base )
	{
		$data				=	array(
									'base'=>array()
								);

		if ( count( $this->_data_preset ) ) {
			foreach ( $this->_data_preset as $k=>$v ) {
				if ( !isset( self::$data_map[$k] ) ) {
					continue;
				}
				if ( $this->_data_preset_null ) {
					if ( empty( $v ) || $v == '0000-00-00' || $v == '0000-00-00 00:00:00' ) {
						$this->_error	=	true;
					}
				}

				$table_instance_name			=	self::$data_map[$k];
				$data[$table_instance_name][$k]	=	$v;
			}

			$this->_data_preset			=	array();
			$this->_data_preset_null	=	false;
		}
		foreach ( $data as $name=>$array ) {
			$data_array	=	${'data_'.$name};

			if ( count( $data_array ) ) {
				foreach ( $data_array as $k=>$v ) {
					if ( !isset( self::$data_map[$k] ) ) {
						continue;
					}

					$table_instance_name				=	self::$data_map[$k];
					$data[$table_instance_name][$k]	=	$v;
				}
			}
		}

		// Core
		// $data['core']	=	array(
		// 						'author_id'=>0,
		// 						'date_time'=>JFactory::getDate()->toSql(),
		// 						'parent_id'=>0
		// 					);

		// if ( isset( self::$objects[$this->_object]['properties']['author'] ) && self::$objects[$this->_object]['properties']['author']
		//   && isset( $data['base'][self::$objects[$this->_object]['properties']['author']] ) ) {
		// 	$data['core']['author_id']	=	$data['base'][self::$objects[$this->_object]['properties']['author']];
		// }
		// if ( !$data['core']['author_id'] ) {
		// 	$data['core']['author_id']	=	JFactory::getUser()->id;
		// }
		// if ( isset( self::$objects[$this->_object]['properties']['parent'] ) && self::$objects[$this->_object]['properties']['parent']
		//   && isset( $data['base'][self::$objects[$this->_object]['properties']['parent']] ) ) {
		// 	$data['core']['parent_id']	=	$data['base'][self::$objects[$this->_object]['properties']['parent']];
		// }

		/* TODO#SEBLOD: force to default author id when null? */
		/* TODO#SEBLOD: force to default parent_id when null? */

		return $data;
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

	// _setDataMap
	protected function _setDataMap( $table_instance_name )
	{
		if ( !is_object( $this->{'_instance_'.$table_instance_name} ) ) {
			return false;
		}

		$fields	=	$this->{'_instance_'.$table_instance_name}->getFields();

		foreach ( $fields as $k=>$v ) {
			if ( !isset( self::$data_map[$k] ) ) {
				self::$data_map[$k]	=	$table_instance_name;
			}
		}

		unset( self::$data_map['id'], self::$data_map['cck'] ); /* TODO#SEBLOD: remove "cck" column */

		return true;
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
		$query	=	'SELECT id AS pk, parent, storage_location, relationships'
				.	' FROM #__cck_core_types'
				.	' WHERE name = "'.$identifier.'"';

		$core	=	JCckDatabase::loadObject( $query );

		/* TODO#SEBLOD4: join #__cck_core and get id */
		
		if ( !( is_object( $core ) && $core->pk ) ) {
			return false;
		}

		$this->_object	=	$core->storage_location;

		/* TODO#SEBLOD4 */

		$this->_id					=	0;
		$this->_parent				=	$core->parent;
		$this->_pk					=	$core->pk;
		$this->_name				=	$identifier;

		// Prepare Relationships
		if ( $core->relationships == '' ) {
			$core->relationships	=	'[]';
		}
		$core->relationships		=	json_decode( $core->relationships );

		foreach ( $core->relationships as $relationship ) {
			$this->_relationships[$relationship->name]	=	$relationship;
		}

		/* TODO#SEBLOD4 */

		return true;
	}
}
?>