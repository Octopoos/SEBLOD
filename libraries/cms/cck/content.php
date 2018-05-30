<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: content.php oliviernolbert / sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Registry\Registry;

// JCckContent
class JCckContent
{
	protected static $instances			=	array();
	protected static $instances_map		=	array();
	protected static $objects			=	array();

	protected $_dispatcher				=	null;
	protected $_options					=	null;

	protected $_columns					=	array();
	protected $_data					=	null;
	protected $_data_map				=	array();
	protected $_data_preset				=	array();
	protected $_error					=	false;
	protected $_id						=	0;
	protected $_instance_base			=	null;
	protected $_instance_core			=	null;
	protected $_instance_more			=	null;
	protected $_instance_more_parent	=	null;
	protected $_instance_more2			=	null;
	protected $_is_new					=	false;
	protected $_logs					=	array();
	protected $_object					=	'';
	protected $_pk						=	0;
	protected $_results					=	array();
	protected $_table 					=	'';
	protected $_type					=	'';
	protected $_type_id					=	0;
	protected $_type_parent				=	'';
	protected $_type_permissions		=	'';

	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct

	// __construct
	public function __construct()
	{
		$this->_dispatcher		=	JEventDispatcher::getInstance();
		$this->_instance_core	=	JCckTable::getInstance( '#__cck_core', 'id' );
		$this->_options			=	new Registry;

		/* TODO#SEBLOD: $this->_object ??  */

		$this->initialize();
	}
	
	// getInstance
	public static function getInstance( $identifier = '' )
	{
		if ( !$identifier ) {
			if ( ( $classname = get_called_class() ) != 'JCckContent' ) {
				return new $classname;
			}

			return new JCckContent;
		}

		if ( !is_array( $identifier ) && ( $classname = substr( strtolower( get_called_class() ), 11 ) ) != '' ) {
			$identifier		=	array( 0=>$classname, 1=>$identifier );
		}
		if ( is_array( $identifier ) ) {
			$key	=	implode( '_', $identifier );
		} else {
			$key	=	$identifier;

			if ( isset( self::$instances_map[$key] ) ) {
				$key	=	self::$instances_map[$key];
			}
		}

		if ( !isset( self::$instances[$key] ) ) {
			$classname	=	'JCckContent';
			$object		=	'';

			if ( is_array( $identifier ) ) {
				if ( isset( $identifier[0] ) ) {
					$object	=	$identifier[0];
				}
			} else {
				$core	=	JCckDatabase::loadObject( 'SELECT pk, storage_location FROM #__cck_core WHERE id = '.(int)$identifier );

				if ( is_object( $core ) && $core->pk ) {
					self::$instances_map[$key]	=	$core->storage_location.'_'.$core->pk;
					$identifier					=	array( 0=>$core->storage_location, 1=>$core->pk );
					$key						=	$core->storage_location.'_'.$core->pk;
					$object						=	$core->storage_location;
				}
			}
			if ( $object != '' && is_file( JPATH_SITE.'/plugins/cck_storage_location/'.$object.'/classes/content.php' ) ) {
				require_once JPATH_SITE.'/plugins/cck_storage_location/'.$object.'/classes/content.php';
				
				$classname		=	'JCckContent'.$object;
			} else {
				$this->_error	=	true;
				
				return new JCckContent;
			}

			self::$instances[$key]	=	new $classname;

			if ( $identifier ) {
				self::$instances[$key]->load( $identifier );
			}
		}

		return self::$instances[$key];
	}

	// initialize
	protected function initialize()
	{
		JPluginHelper::importPlugin( 'content' );
	}

	// reloadInstance
	public static function reloadInstance( $identifier = array() )
	{
		if ( !is_array( $identifier ) ) {
			$identifier	=	JCckDatabase::loadObject( 'SELECT pk, storage_location FROM #__cck_core WHERE id = '.(int)$identifier );

			if ( !is_object( $identifier ) ) {
				return false;
			}

			$identifier	=	array( 0=>$identifier->storage_location, 1=>$identifier->pk );
		}

		$identifier	=	implode( '_', $identifier );

		if ( isset( self::$instances[$identifier] ) && self::$instances[$identifier]->_pk ) {
			if ( self::$instances[$identifier]->_instance_base ) {
				self::$instances[$identifier]->_instance_base->load( self::$instances[$identifier]->_pk );
			}
			if ( self::$instances[$identifier]->_instance_core ) {
				self::$instances[$identifier]->_instance_core->load( self::$instances[$identifier]->_pk );
			}
			if ( self::$instances[$identifier]->_instance_more ) {
				self::$instances[$identifier]->_instance_more->load( self::$instances[$identifier]->_pk );
			}
			if ( self::$instances[$identifier]->_instance_more_parent ) {
				self::$instances[$identifier]->_instance_more_parent->load( self::$instances[$identifier]->_pk );
			}
			if ( self::$instances[$identifier]->_instance_more2 ) {
				self::$instances[$identifier]->_instance_more2->load( self::$instances[$identifier]->_pk );
			}
		}
	}

	// setInstance
	protected function setInstance( $instance_name, $load = false )
	{
		$method	=	'setInstance'.ucwords( $instance_name, '_' );

		if ( $this->$method() ) {
			if ( $load && $this->_pk ) {
				return $this->{'_instance_'.$instance_name}->load( $this->_pk );
			}
		}
		
		return true;
	}

	// setInstanceBase
	protected function setInstanceBase()
	{
		$this->_instance_base	=	JTable::getInstance( $this->_columns['table_object'][0], $this->_columns['table_object'][1] );
		$this->_setDataMap( 'base' );

		return true;
	}

	// setInstanceMore
	protected function setInstanceMore()
	{
		$table	=	'cck_store_form_'.$this->_type;

		if ( !$this->hasTable( $table ) ) {
			return false;
		}

		$this->_instance_more	=	JCckTable::getInstance( '#__'.$table );
		$this->_setDataMap( 'more' );

		return true;
	}

	// setInstanceMore_Parent
	protected function setInstanceMore_Parent()
	{
		if ( !$this->_type_parent ) {
			return false;
		}
		$table	=	'cck_store_form_'.$this->_type_parent;

		if ( !$this->hasTable( $table ) ) {
			return false;
		}

		$this->_instance_more_parent	=	JCckTable::getInstance( '#__'.$table );
		$this->_setDataMap( 'more_parent' );

		return true;
	}

	// setInstanceMore2
	protected function setInstanceMore2()
	{
		$table	=	'cck_store_item_'.str_replace( '#__', '', $this->_table );

		if ( !$this->hasTable( $table ) ) {
			return false;
		}

		$this->_instance_more2	=	JCckTable::getInstance( '#__'.$table );
		$this->_setDataMap( 'more2' );

		return true;
	}

	// unsetInstance
	protected function unsetInstance( $instance_name )
	{
		$this->{'_instance_'.$instance_name}	=	null;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Can

	// can
	public function can( $task, $target = '' )
	{
		if ( !$this->_options->get( 'check_permissions', 1 ) ) {
			return true;
		}

		$method	=	'can'.ucfirst( $task );

		return ( $task == 'update' ) ? $this->$method( $target ) : $this->$method();
	}

	// canCreate
	public function canCreate()
	{
		if ( !JFactory::getUser()->authorise( 'core.create', 'com_cck.form.'.$this->_type_id ) ) {
			return false;
		}

		return true;
	}

	// canDelete
	public function canDelete()
	{
		$author_id		=	$this->getAuthor();

		if ( !$author_id ) {
			$author_id	=	-1;
		}
		
		$user			=	JFactory::getUser();
		$canDelete		=	$user->authorise( 'core.delete', 'com_cck.form.'.$this->_type_id );
		$canDeleteOwn	=	$user->authorise( 'core.delete.own', 'com_cck.form.'.$this->_type_id );

		if ( ( !$canDelete && !$canDeleteOwn ) ||
			 ( !$canDelete && $canDeleteOwn && $author_id != $user->id ) ||
			 ( $canDelete && !$canDeleteOwn && $author_id == $user->id ) ) {
			return false;
		}

		return true;
	}

	// canSave
	public function canSave()
	{
		$author_id		=	$this->getAuthor();

		if ( !$author_id ) {
			$author_id	=	-1;
		}

		$user				=	JFactory::getUser();
		$canEdit			=	$user->authorise( 'core.edit', 'com_cck.form.'.$this->_type_id );
		$canEditOwn			=	$user->authorise( 'core.edit.own', 'com_cck.form.'.$this->_type_id );
		$canEditOwnContent	=	'';

		jimport( 'cck.joomla.access.access' );
		$canEditOwnContent	=	CCKAccess::check( $user->id, 'core.edit.own.content', 'com_cck.form.'.$this->_type_id );

		if ( $canEditOwnContent ) {
			$parts				=	explode( '@', $canEditOwnContent );
			$remote_field		=	JCckDatabaseCache::loadObject( 'SELECT storage, storage_table, storage_field FROM #__cck_core_fields WHERE name = "'.$parts[0].'"' );
			$canEditOwnContent	=	false;

			if ( is_object( $remote_field ) && $remote_field->storage == 'standard' ) {
				$related_content_id		=	JCckDatabase::loadResult( 'SELECT '.$remote_field->storage_field.' FROM '.$remote_field->storage_table.' WHERE id = '.(int)$this->_pk );
				$related_content		=	JCckDatabase::loadObject( 'SELECT author_id, pk FROM #__cck_core WHERE storage_location = "'.( isset( $parts[1] ) && $parts[1] != '' ? $parts[1] : 'joomla_article' ).'" AND pk = '.(int)$related_content_id );

				if ( $related_content->author_id == $user->id ) {
					$canEditOwnContent	=	true;
				}
			}
		}

		if ( !( $canEdit && $canEditOwn
			|| ( $canEdit && !$canEditOwn && ( $author_id != $user->id ) )
			|| ( $canEditOwn && ( $author_id == $user->id ) )
			|| ( $canEditOwnContent ) ) ) {
			return false;
		}

		return true;
	}

	// canUpdate
	public function canUpdate( $property )
	{
		if ( !$property ) {
			return false;
		}

		static $types = array();

		if ( !isset( $types[$this->_type] ) ) {
			if ( $this->_type_permissions == '' ) {
				$this->_type_permissions	=	'{}';
			}
			$types[$this->_type]	=	json_decode( $this->_type_permissions, true );
		}
		if ( !isset( $types[$this->_type][$property] ) ) {
			return false;
		}
		$property	=	$types[$this->_type][$property];
		
		if ( !JFactory::getUser()->authorise( 'core.edit.'.$property, 'com_cck.form.'.$this->_type_id ) ) {
			return false;
		}

		return true;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Delete

	// delete ($)
	public function delete( $identifier = 0 )
	{
		if ( $identifier ) {
			$this->reset();

			if ( !$this->_setObjectById( $identifier ) ) {
				return false;
			}
			if ( $this->_instance_core->id ) {
				if ( !$this->_instance_core->load( $this->_id ) ) {
					return false;
				}
			}
			if ( !$this->setInstance( 'base', true ) ) {
				$this->reset();

				return false;
			}
		}
		if ( !$this->_object ) {
			return false;
		}
		if ( !( $this->_id && $this->_pk ) ) {
			return false;
		}
		if ( !$this->can( 'delete' ) ) {
			$this->log( 'error', 'Permissions denied.' );

			return false;
		}
		
		$result	=	$this->trigger( 'delete', 'before' );

		if ( is_array( $result ) && in_array( false, $result, true ) ) {
			return false;
		}

		if ( !$this->remove() ) {
			return false;
		}

		$this->trigger( 'delete', 'after' );

		return true;
	}

	// remove
	protected function remove()
	{
		return $this->_instance_base->delete( $this->_pk );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Do

	// batch (^)
	public function batch( $identifiers, $task )
	{
		$this->clear();

		if ( !( is_array( $identifiers ) && count( $identifiers ) ) ) {
			return $this->_options->get( 'chain_methods', 1 ) ? $this : true;
		}
		if ( !$task ) {
			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		static $tasks	=	array(
								'delete'=>false,
								'dump'=>false,
								'updateProperty'=>true
							);
		/* TODO#SEBLOD: call=true, triggerSave=true, updateType=true */

		if ( !isset( $tasks[$task] ) ) {
			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		$args	=	func_get_args();

		array_shift( $args );
		array_shift( $args );

		if ( method_exists( $this, $task ) ) {
			$count	=	0;

			foreach ( $identifiers as $identifier ) {
				$this->load( $identifier );

				if ( $tasks[$task] ) {
					if ( call_user_func_array( array( $this, $task ), $args ) ) {
						$count++;
					}
				} else {
					if ( $this->$task() ) {
						$count++;
					}	
				}
			}
		}

		/* TODO#SEBLOD: $this->log( '...', $count ); */

		return $this->_options->get( 'chain_methods', 1 ) ? $this : ( $count ? $count : false );
	}

	// batchResults
	public function batchResults()
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		$args	=	func_get_args();

		array_unshift( $args, $this->_results );

		call_user_func_array( array( $this, 'batch' ), $args );

		return $this;
	}

	// bind
	public function bind( $instance_name, $data )
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		$result	=	$this->{'_instance_'.$instance_name}->bind( $data );

		if ( !$result ) {
			$this->_error	=	true;
		}

		return $this;
	}

	// call
	public function call()
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		$args	=	func_get_args();
		$task	=	'_'.array_shift( $args );
		
		static $excluded	=	array(
									'_getDataDispatch'=>'',
									'_getDataQuery'=>'',
									'_saveLegacy'=>'',
									'_setDataMap'=>'',
									'_setObjectById'=>'',
									'_setObjectByType'=>'',
									'_setObjectMap'=>''
								);
		if ( isset( $excluded[$task] ) ) {
			$this->_error	=	true;

			return $this;
		}

		if ( method_exists( $this, $task ) ) {
			$result	=	call_user_func_array( array( $this, $task ), $args );

			if ( !$result ) {
				$this->_error	=	true;
			}
		} else {
			$this->log( 'error', 'Method not found.' );

			$this->_error	=	true;
		}

		return $this;
	}

	// clear
	public function clear( $property = '' )
	{
		if ( $property == 'results' ) {
			$this->_results	=	array();
		} else {
			$this->_error	=	false;
		}

		return $this;
	}

	// create (^)
	public function create( $content_type, $data, $data_more = array(), $data_more2 = array() )
	{
		if ( $this->_id ) {
			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		$this->clear();

		if ( !$this->_setObjectByType( $content_type ) ) {
			$this->reset();

			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		if ( !$this->can( 'create' ) ) {
			$this->log( 'error', 'Permissions denied.' );
			$this->reset();

			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}
		
		$this->setInstance( 'base' );
		$this->setInstance( 'more' );
		$this->setInstance( 'more_parent' );
		$this->setInstance( 'more2' );

		if ( is_bool( $data_more ) ) {
			$data		=	$this->_getDataDispatch( $content_type, $data );

			if ( $data_more === true && is_array( $data_more2 ) && count( $data_more2 ) ) {
				$results	=	$this->_results;

				$this->find( $content_type, $data_more2 )->loadOne();

				if ( $this->isSuccessful() ) {
					$this->save( 'base', $data['base'] );
					$this->save( 'core', array(
											'cck'=>$this->_type,
											'pk'=>$this->_pk,
											'storage_location'=>$this->_object,
											'author_id'=>$this->getAuthor(),
											'parent_id'=>$data['core']['parent_id'],
											'date_time'=>$data['core']['date_time']
						   				 ) );

					static $names	=	array(
											'more'=>'',
											'more_parent'=>'',
											'more2'=>''
										);

					foreach ( $names as $instance_name=>$null ) {
						if ( count( $data[$instance_name] ) ) {
							$this->save( $instance_name, $data[$instance_name] );
						}
					}

					// Keep it for later
					self::$instances_map[$this->_id]				=	$this->_object.'_'.$this->_pk;
					self::$instances[$this->_object.'_'.$this->_pk]	=	$this;

					$this->_results	=	$results;

					return $this->_options->get( 'chain_methods', 1 ) ? $this : $this->_pk;
				} else {
					$this->_error	=	false;
					$this->_results	=	$results;
				}
			}
		} else {
			$data		=	$this->_getDataDispatch( $content_type, $data, $data_more, $data_more2 );
		}

		$this->_is_new	=	true;

		// Base
		if ( !( $this->save( 'base', $data['base'] ) ) ) {
			$this->_error	=	true;
			$this->_is_new	=	false;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		// Core
		if ( !( $this->save( 'core', array(
										'cck'=>$this->_type,
										'pk'=>$this->_pk,
										'storage_location'=>$this->_object,
										'author_id'=>$this->getAuthor(),
										'parent_id'=>$data['core']['parent_id'],
										'date_time'=>$data['core']['date_time']
						   ) ) ) ) {
			$this->_error	=	true;
			$this->_is_new	=	false;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		// More
		static $names	=	array(
								'more'=>'',
								'more_parent'=>'',
								'more2'=>''
							);

		foreach ( $names as $instance_name=>$null ) {
			if ( count( $data[$instance_name] ) ) {
				$this->{'_instance_'.$instance_name}->load( $this->_pk, true );
			
				if ( !( $this->save( $instance_name, $data[$instance_name] ) ) ) {
					$this->_error	=	true;
					$this->_is_new	=	false;

					return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
				}
			}
		}
		
		$this->_is_new	=	false;

		// Keep it for later
		self::$instances_map[$this->_id]				=	$this->_object.'_'.$this->_pk;
		self::$instances[$this->_object.'_'.$this->_pk]	=	$this;
		
		return $this->_options->get( 'chain_methods', 1 ) ? $this : $this->_pk;
	}

	// count (^$)
	public function count( $content_type, $data = array() )
	{
		$this->clear();

		if ( !$this->_setObjectByType( $content_type ) ) {
			$this->reset();

			return false;
		}

		$this->setInstance( 'base' );
		$this->setInstance( 'more' );
		$this->setInstance( 'more_parent' );
		$this->setInstance( 'more2' );

		$db		=	JFactory::getDbo();
		$query	=	$this->_getDataQuery( $content_type, $data );

		if ( $query === false ) {
			return false;
		}

		$query->select( 'COUNT('.$db->quoteName( 'a.pk' ).')' );

		$db->setQuery( $query );

		return (int)$db->loadResult();
	}

	// find (^)
	public function find( $content_type, $data = array() )
	{
		$this->clear();
		$this->clear( 'results' );
		
		if ( !$this->_setObjectByType( $content_type ) ) {
			$this->reset();

			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}
		
		$this->setInstance( 'base' );
		$this->setInstance( 'more' );
		$this->setInstance( 'more_parent' );
		$this->setInstance( 'more2' );

		$db		=	JFactory::getDbo();
		$query	=	$this->_getDataQuery( $content_type, $data );

		if ( $query === false ) {
			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		$query->select( $db->quoteName( 'a.pk' ) );

		$db->setQuery( $query );

		$this->_results	=	$db->loadColumn();

		return $this->_options->get( 'chain_methods', 1 ) ? $this : $this->_results;
	}

	// findMore (^)
	public function findMore( $content_type, $data = array() )
	{
		$this->clear();
		
		if ( !$this->_setObjectByType( $content_type ) ) {
			$this->reset();

			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}
		
		$this->setInstance( 'base' );
		$this->setInstance( 'more' );
		$this->setInstance( 'more_parent' );
		$this->setInstance( 'more2' );

		$db		=	JFactory::getDbo();
		$query	=	$this->_getDataQuery( $content_type, $data );

		if ( $query === false ) {
			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		$query->select( $db->quoteName( 'a.pk' ) );

		$db->setQuery( $query );

		$pks		=	array_flip( $this->_results );
		$results	=	$db->loadColumn();

		foreach ( $results as $pk ) {
			if ( !isset( $pks[$pk] ) ) {
				$this->_results[]	=	$pk;
			}
		}

		return $this->_options->get( 'chain_methods', 1 ) ? $this : $this->_results;
	}

	// import (^)
	public function import( $content_type, $identifier )
	{
		$this->reset();

		if ( !$this->_setObjectByType( $content_type ) ) {
			$this->reset();

			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		$this->_pk	=	$identifier;

		if ( !$this->setInstance( 'base', true ) ) {
			$this->reset();

			return false;
		}

		if ( JCckDatabase::loadResult( 'SELECT COUNT(id) FROM #__cck_core WHERE storage_location = "'.(string)$this->_object.'" AND pk = '.(int)$this->_pk ) ) {
			$this->reset();

			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		if ( !$this->can( 'create' ) ) {
			$this->log( 'error', 'Permissions denied.' );
			$this->reset();

			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}
		
		$this->setInstance( 'more' );
		$this->setInstance( 'more_parent' );
		$this->setInstance( 'more2' );

		$this->_is_new	=	true;

		// Core
		if ( !( $this->save( 'core', array(
										'cck'=>$this->_type,
										'pk'=>$this->_pk,
										'storage_location'=>$this->_object,
										'author_id'=>$this->getAuthor(),
										'parent_id'=>$this->getParent(),
										'date_time'=>JFactory::getDate()->toSql()
						   ) ) ) ) {
			$this->_error	=	true;
			$this->_is_new	=	false;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		// More
		static $names	=	array(
								'more'=>'',
								'more_parent'=>'',
								'more2'=>''
							);

		foreach ( $names as $instance_name=>$null ) {
			if ( is_object( $this->{'_instance_'.$instance_name} ) ) {
				$this->{'_instance_'.$instance_name}->load( $this->_pk, true );
			}
		}
		
		$this->_is_new	=	false;

		// Keep it for later
		self::$instances_map[$this->_id]				=	$this->_object.'_'.$this->_pk;
		self::$instances[$this->_object.'_'.$this->_pk]	=	$this;
		
		return $this->_options->get( 'chain_methods', 1 ) ? $this : $this->_pk;
	}

	// load (^)
	public function load( $identifier )
	{
		$this->reset();

		if ( !$this->_setObjectById( $identifier ) ) {
			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}
		if ( !$this->_instance_core->load( $this->_id ) ) {
			$this->reset();

			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}
		if ( !$this->setInstance( 'base', true ) ) {
			$this->reset();

			$this->_error	=	true;
		
			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		$this->setInstance( 'more', true );
		$this->setInstance( 'more_parent', true );
		$this->setInstance( 'more2', true );

		if ( !isset( self::$instances_map[$this->_id] ) ) {
			self::$instances_map[$this->_id]	=	$this->_object.'_'.$this->_pk;
		}

		return $this->_options->get( 'chain_methods', 1 ) ? $this : true;
	}

	// loadOne
	public function loadOne()
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		if ( !count( $this->_results ) ) {
			$this->_error	=	true;

			return $this;
		}

		$this->load( $this->_results[0] );

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

	// preset
	public function preset( $data )
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		$this->_data_preset	=	$data;

		return $this;
	}

	// reset
	public function reset( $complete = false )
	{
		$this->clear();

		$this->_id					=	0;
		$this->_pk					=	0;
		$this->_type				=	'';
		$this->_type_id				=	0;
		$this->_type_parent			=	'';
		$this->_type_permissions	=	'';

		if ( $complete ) {
			$this->_columns			=	array();
			$this->_object			=	'';
			$this->_table 			=	'';

			$this->_data			=	null;
			$this->_data_map		=	array(); /* TODO#SEBLOD: per content type or per item ? */
			$this->_instance_core	=	JCckTable::getInstance( '#__cck_core', 'id' );

			$this->unsetInstance( 'base' );
			$this->unsetInstance( 'more' );
			$this->unsetInstance( 'more_parent' );
			$this->unsetInstance( 'more2' );
		}

		return $this;
	}

	// set
	public function set( $instance_name, $property, $value )
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		if ( property_exists( $this->{'_instance_'.$instance_name}, $property ) ) {
			$this->{'_instance_'.$instance_name}->$property	=	$value;
		} else {
			$this->log( 'error', 'Property unknown.' );

			$this->_error	=	true;
		}
		
		return $this;
	}

	// setOptions
	public function setOptions( $options )
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		$this->_options	=	new Registry( $options );

		return $this;
	}

	// setProperty
	public function setProperty( $property, $value )
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}
		
		if ( isset( $this->_data_map[$property] ) ) {
			$this->set( $this->_data_map[$property], $property, $value );
		} else {
			$this->log( 'error', 'Property unknown.' );

			$this->_error	=	true;
		}

		return $this;
	}

	// setType
	public function setType( $content_type, $reload = true )
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		$this->_instance_core->cck	=	$content_type;
		$this->_type				=	$content_type;
		
		if ( $reload ) {
			if ( !$this->_setObjectByType( $content_type ) ) {
				$this->reset();

				$this->_error	=	true;

				return $this;
			}
			$this->_data	=	null;

			$this->unsetInstance( 'more' );
			$this->unsetInstance( 'more_parent' );

			$this->setInstance( 'more', true );
			$this->setInstance( 'more_parent', true );
		}

		return $this;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Get

	// get
	public function get( $instance_name, $property = '', $default = '' )
	{
		static $names	=	array(
								'base'=>'',
								'core'=>'',
								'more'=>'',
								'more_parent'=>'',
								'more2'=>'',
							);

		if ( isset( $names[$instance_name] ) ) {
			return $this->{'_instance_'.$instance_name}->get( $property, $default );
		} else {
			$this->log( 'notice', 'Usage deprecated.' );

			$default	=	$property;
			$property	=	$instance_name;

			if ( !isset( $this->_data ) ) {
				$this->getData();
			}

			if ( isset( $this->_data[$property] ) ) {
				return $this->_data[$property];
			}

			return $default;
		}
	}

	// getAuthor
	public function getAuthor()
	{
		$author_id	=	0;

		if ( isset( $this->_columns['author'] ) && $this->_columns['author'] ) {
			$author_id	=	(int)$this->get( 'base', $this->_columns['author'], 0 );
		}

		return $author_id;
	}

	// getData
	public function getData( $instance_name = '' )
	{
		if ( $instance_name ) {
			$data	=	$this->{'_instance_'.$instance_name}->getProperties();

			unset( $data['id'], $data['cck'] );

			return $data;
		} elseif ( !isset( $this->_data ) ) {
			$this->_data	=	array();

			static $names	=	array(
									'base'=>'',
									'more'=>'',
									'more_parent'=>'',
									'more2'=>'',
								);
			
			foreach ( $names as $instance_name=>$null ) {
				if ( is_object( $this->{'_instance_'.$instance_name} ) ) {
					$data	=	$this->{'_instance_'.$instance_name}->getProperties();

					unset( $data['id'], $data['cck'] );

					$this->_data	=	array_merge( $this->_data, $data );
				}
			}
		}

		return $this->_data;
	}

	// getDataObject
	public function getDataObject( $instance_name = '' )
	{
		return (object)$this->getData( $instance_name );
	}

	// getId
	public function getId()
	{
		return (int)$this->_id;
	}

	// getLog
	public function getLog()
	{
		return $this->_logs;
	}

	// getObject
	public function getObject()
	{
		return $this->_object;
	}

	// getParent
	public function getParent()
	{
		$parent_id	=	0;

		if ( isset( $this->_columns['parent'] ) && $this->_columns['parent'] ) {
			$parent_id	=	(int)$this->get( 'base', $this->_columns['parent'], 0 );
		}

		return $parent_id;
	}

	// getPk
	public function getPk()
	{
		return (int)$this->_pk;
	}
	
	// getProperty
	public function getProperty( $property, $default = '' )
	{
		if ( isset( $this->_data_map[$property] ) ) {
			return $this->get( $this->_data_map[$property], $property, $default );
		} else {
			$this->log( 'notice', 'Property unknown.' );
		}

		return $default;
	}

	// getResults
	public function getResults()
	{
		return $this->_results;
	}

	// getTable
	public function getTable()
	{
		return $this->_table;
	}

	// getType
	public function getType()
	{
		return $this->_type;
	}

	// hasTable
	protected function hasTable( $table )
	{
		$db_prefix	=	JFactory::getConfig()->get( 'dbprefix' );
		$tables		=	JCckDatabaseCache::getTableList( true );

		if ( !isset( $tables[$db_prefix.$table] ) ) {
			return false;
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

	// -------- -------- -------- -------- -------- -------- -------- -------- // Save

	// check
	public function check( $instance_name )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		return $this->{'_instance_'.$instance_name}->check();
	}

	// postSave
	protected function postSave( $instance_name, $data ) {}
	
	// preSave
	protected function preSave( $instance_name, &$data ) {}
	
	// save ($)
	public function save( $instance_name, $data = array() )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		if ( !$this->isNew() ) {
			if ( !$this->can( 'save' ) ) {
				$this->log( 'error', 'Permissions denied.' );

				return false;
			}
		}

		$this->preSave( $instance_name, $data );

		$this->bind( $instance_name, $data );
		$this->check( $instance_name );

		if ( $instance_name == 'base' ) {
			$result	=	$this->trigger( 'save', 'before' );

			if ( is_array( $result ) && in_array( false, $result, true ) ) {
				return false;
			}
		}

		if ( get_class( $this ) == 'JCckContent' ) {
			$status	=	$this->_saveLegacy( $instance_name, $data );
		} else {
			$method	=	'save'.ucfirst( $instance_name );
			$status	=	$this->$method();
		}

		if ( !$status ) {
			return $status;
		}

		switch( $instance_name ) {
			case 'base':
				$this->_pk	=	$this->{'_instance_'.$instance_name}->{$this->_columns['key']};
				
				if ( $this->_instance_core->id ) {
					$data_core	=	array();

					if ( $this->_columns['author'] == $this->_columns['key'] ) {
						$data_core['author_id']	=	$this->{'_instance_'.$instance_name}->get( $this->_columns['key'], 0 );
					} elseif ( isset( $data[$this->_columns['author']] ) ) {
						$data_core['author_id']	=	$data[$this->_columns['author']];
					}
					if ( isset( $data[$this->_columns['parent']] ) ) {
						$data_core['parent_id']	=	$data[$this->_columns['parent']];
					}
					if ( count( $data_core ) ) {
						$this->save( 'core', $data_core );
					}
				}
				break;
			case 'core':
				$this->_id	=	$this->{'_instance_'.$instance_name}->id;
				
				if ( property_exists( $this->_instance_base, $this->_columns['custom'] ) ) {
					$this->_instance_base->{$this->_columns['custom']}	=	'::cck::'.$this->_id.'::/cck::';
					$this->store( 'base' );
				}
				break;
			case 'more':
			case 'more2':
			default:
				break;
		}

		$this->postSave( $instance_name, $data );
		
		if ( $instance_name == 'base' ) {
			$this->trigger( 'save', 'after' );
		}

		return $status;
	}

	// saveBase
	protected function saveBase()
	{
		return $this->_instance_base->store();
	}

	// saveCore
	protected function saveCore()
	{
		return $this->_instance_core->store();
	}

	// saveMore
	protected function saveMore()
	{
		return $this->_instance_more->store();
	}

	// saveMore_Parent
	protected function saveMore_Parent()
	{
		return $this->_instance_more_parent->store();
	}

	// saveMore2
	protected function saveMore2()
	{
		return $this->_instance_more2->store();
	}

	// store ($)
	public function store( $instance_name )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		if ( !$this->isNew() ) {
			if ( !$this->can( 'save' ) ) {
				$this->log( 'error', 'Permissions denied.' );

				return false;
			}
		}
		
		return $this->{'_instance_'.$instance_name}->store();
	}

	// update ($)
	public function update( $instance_name, $property, $value )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		if ( !$this->can( 'update', $property ) ) {
			$this->log( 'error', 'Permissions denied.' );

			return false;
		}

		$check_permissions	=	$this->_options->get( 'check_permissions', 1 );
		$pre_update			=	$this->{'_instance_'.$instance_name}->$property;

		if ( $check_permissions ) {
			$this->_options->set( 'check_permissions', 0 );
		}
		
		$this->{'_instance_'.$instance_name}->$property	=	$value;

		if ( !( $result = $this->store( $instance_name ) ) ) {
			$this->{'_instance_'.$instance_name}->$property	=	$pre_update;
		}

		if ( $check_permissions ) {
			$this->_options->set( 'check_permissions', $check_permissions );
		}

		return $result;
	}

	// updateAuthor ($)
	public function updateAuthor( $author_id )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		if ( !$this->_pk ) {
			return false;
		}
		if ( !$this->isNew() ) {
			if ( !$this->can( 'save' ) ) {
				$this->log( 'error', 'Permissions denied.' );

				return false;
			}
		}

		$pre_update	=	$this->get( 'base', $this->_columns['author'], 0 );

		if ( isset( $this->_columns['author'] ) && $this->_columns['author'] ) {
			$this->set( 'base', $this->_columns['author'], $author_id );

			if ( !$this->store( 'base' ) ) {
				$this->set( 'base', $this->_columns['author'], $pre_update );

				return false;
			}
		}
		
		$this->_instance_core->author_id	=	$author_id;

		if ( !$this->_instance_core->store() ) {
			$this->_instance_core->author_id	=	$pre_update;

			return false;
		}

		/* TODO#SEBLOD: update bridge author */

		return true;
	}

	// updateProperty ($)
	public function updateProperty( $property, $value )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		if ( isset( $this->_data_map[$property] ) ) {
			return $this->update( $this->_data_map[$property], $property, $value );
		} else {
			$this->log( 'error', 'Property unknown.' );
		}

		return false;
	}

	// updateType ($)
	public function updateType( $content_type, $reload = true )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		if ( !$this->_pk ) {
			return false;
		}
		if ( !$this->isNew() ) {
			if ( !$this->can( 'save' ) ) {
				$this->log( 'error', 'Permissions denied.' );

				return false;
			}
		}

		if ( !$this->setType( $content_type, $reload )->isSuccessful() ) {
			/* TODO#SEBLOD: revert? */

			$this->_error	=	false;

			return false;
		}

		return $this->_instance_core->store(); /* TODO#SEBLOD: revert? */
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Trigger

	// trigger
	public function trigger( $task, $event )
	{
		$check	=	$this->_options->get( 'trigger_events', 1 );

		if ( !( ( is_array( $check ) && in_array( $task, $check ) ) || $check == 1 ) ) {
			return false;
		}

		$task	=	ucfirst( $task );
		$event	=	$event.$task;
		$method	=	'trigger'.$task;

		if ( !( $event != '' && isset( $this->_columns['events'][$event] ) ) ) {
			return false;
		}

		return $this->$method( $event );
	}

	// triggerDelete
	public function triggerDelete( $event )
	{
		return $this->_dispatcher->trigger( $this->_columns['events'][$event], array( $this->_columns['context'], $this->_instance_base ) );
	}

	// triggerSave
	public function triggerSave( $event )
	{
		return $this->_dispatcher->trigger( $this->_columns['events'][$event], array( $this->_columns['context'], $this->_instance_base, $this->_is_new ) );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Misc
	
	// dump
	public function dump()
	{
		if ( !function_exists( 'dump' ) ) {
			$this->log( 'notice', 'Function not found.' );

			return false;
		}

		dump( $this->_columns, 'columns' );
		dump( $this->_data, 'data' );
		dump( $this->_error, 'error' );
		dump( $this->_id, 'id' );
		dump( $this->_is_new, 'isnew' );
		dump( $this->_logs, 'logs' );
		dump( $this->_object, 'object' );
		dump( $this->_pk, 'pk' );
		dump( $this->_results, 'results' );
		dump( $this->_table, 'table' );
		dump( $this->_type, 'type' );
		dump( $this->_type_id, 'type_id' );
		dump( $this->_type_parent, 'type_parent' );
		dump( $this->_type_permissions, 'type_permissions' );

		if ( $this->_instance_base ) {
			dump( $this->_instance_base, 'base' );
		}
		if ( $this->_instance_core ) {
			dump( $this->_instance_core, 'core' );
		}
		if ( $this->_instance_more ) {
			dump( $this->_instance_more, 'more' );
		}
		if ( $this->_instance_more_parent ) {
			dump( $this->_instance_more_parent, 'more_parent' );
		}
		if ( $this->_instance_more2 ) {
			dump( $this->_instance_more2, 'more2' );
		}

		return true;
	}

	// _getDataDispatch
	protected function _getDataDispatch( $content_type, $data_base, $data_more = array(), $data_more2 = array() )
	{
		$data				=	array(
									'base'=>array(),
									'more'=>array(),
									'more_parent'=>array(),
									'more2'=>array()
								);
		$data_more_parent	=	array();

		// Base & More
		if ( $this->_type_parent ) {
			if ( isset( $data_more[$this->_type_parent] ) ) {
				$data_more_parent	=	$data_more[$this->_type_parent];
			}
			if ( isset( $data_more[$this->_type] ) ) {
				$data_more			=	$data_more[$this->_type];
			} else {
				$data_more			=	array();
			}
		}

		if ( count( $this->_data_preset ) ) {
			foreach ( $this->_data_preset as $k=>$v ) {
				if ( !isset( $this->_data_map[$k] ) ) {
					continue;
				}

				$instance_name				=	$this->_data_map[$k];
				$data[$instance_name][$k]	=	$v;
			}

			$this->_data_preset	=	array();
		}
		foreach ( $data as $name=>$array ) {
			$data_array	=	${'data_'.$name};

			if ( count( $data_array ) ) {
				foreach ( $data_array as $k=>$v ) {
					if ( !isset( $this->_data_map[$k] ) ) {
						continue;
					}

					$instance_name				=	$this->_data_map[$k];
					$data[$instance_name][$k]	=	$v;
				}
			}
		}

		if ( count( $data['more2'] ) ) {
			if ( !isset( $data['more2']['cck'] ) ) { /* TODO#SEBLOD: remove "cck" column */
				$data['more2']['cck']	=	$this->_type;
			}
		}

		// Core
		$data['core']	=	array(
								'author_id'=>0,
								'date_time'=>JFactory::getDate()->toSql(),
								'parent_id'=>0
							);

		if ( isset( $this->_columns['author'] ) && $this->_columns['author']
		  && isset( $data['base'][$this->_columns['author']] ) ) {
			$data['core']['author_id']	=	$data['base'][$this->_columns['author']];
		}
		if ( !$data['core']['author_id'] ) {
			$data['core']['author_id']	=	JFactory::getUser()->id;
		}
		if ( isset( $this->_columns['parent'] ) && $this->_columns['parent']
		  && isset( $data['base'][$this->_columns['parent']] ) ) {
			$data['core']['parent_id']	=	$data['base'][$this->_columns['parent']];
		}

		/* TODO#SEBLOD: force to default author id when null? */
		/* TODO#SEBLOD: force to default parent_id when null? */

		return $data;
	}

	// _getDataQuery
	protected function _getDataQuery( $content_type, $data )
	{
		$db		=	JFactory::getDbo();
		$query	=	$db->getQuery( true );
		
		$tables	=	array(
						'base'=>'b'
					);

		$query->from( $db->quoteName( '#__cck_core', 'a' ) );
		$query->join( 'left', $db->quoteName( $this->_table, 'b' ).' ON '.$db->quoteName( 'b.id' ).' = '.$db->quoteName( 'a.pk' ) );
		$query->where( $db->quoteName( 'a.cck' ).' = '.$db->quote( $content_type ) );

		foreach ( $data as $k=>$v ) {
			if ( $k == $this->_columns['key'] ) {
				$instance_name	=	'base';
			} else {
				if ( !isset( $this->_data_map[$k] ) ) {
					return false;
				}
				$instance_name	=	$this->_data_map[$k];
			}

			$index	=	'';

			if ( !isset( $tables[$instance_name] ) ) {
				switch ( $instance_name ) {
					case 'more':
						$tables['more']			=	'c';
						$query->join( 'left', $db->quoteName( '#__cck_store_form_'.$this->_type, $tables['more'] ).' ON '.$db->quoteName( $tables['more'].'.id' ).' = '.$db->quoteName( 'a.pk' ) );
						break;
					case 'more_parent':
						$tables['more_parent']	=	'd';
						$query->join( 'left', $db->quoteName( '#__cck_store_form_'.$this->_type_parent, $tables['more_parent'] ).' ON '.$db->quoteName( $tables['more_parent'].'.id' ).' = '.$db->quoteName( 'a.pk' ) );
						break;
					case 'more2':
						$tables['more2']		=	'e';
						$query->join( 'left', $db->quoteName( '#__cck_store_item_'.str_replace( '#__', '', $this->_table ), $tables['more2'] ).' ON '.$db->quoteName( $tables['more2'].'.id' ).' = '.$db->quoteName( 'a.pk' ) );
						break;
					default:
						break;
				}
			}
			$index	=	$tables[$instance_name];

			if ( !$index ) {
				continue;
			}
			$query->where( $db->quoteName( $index.'.'.$k ) . ' = ' . $db->quote( $v ) );
		}

		return $query;
	}

	// _setDataMap
	protected function _setDataMap( $instance_name, $force = false )
	{
		$fields	=	$this->{'_instance_'.$instance_name}->getFields();

		foreach ( $fields as $k=>$v ) {
			if ( !isset( $this->_data_map[$k] ) ) {
				$this->_data_map[$k]	=	$instance_name;
			}
		}

		unset( $this->_data_map['id'], $this->_data_map['cck'] ); /* TODO#SEBLOD: remove "cck" column */
	}

	// _setObjectMap
	protected function _setObjectMap( $object = '' )
	{
		if ( !$object ) {
			$current	=	true;
			$object		=	$this->_object;
		} else {
			$current	=	false;
		}
		if ( !$object ) {
			return false;
		}

		if ( !is_file( JPATH_SITE.'/plugins/cck_storage_location/'.$object.'/'.$object.'.php' ) ) {
			return false;
		}

		require_once JPATH_SITE.'/plugins/cck_storage_location/'.$object.'/'.$object.'.php';

		$properties	=	array(
							'author',
							'context',
							'custom',
							'events',
							'key',
							'parent',
							'table',
							'table_object'
						);

		$this->_columns	=	JCck::callFunc( 'plgCCK_Storage_Location'.$object, 'getStaticProperties', $properties );

		return true;
	}

	// _setObjectById
	protected function _setObjectById( $identifier )
	{
		$query	=	'SELECT a.id AS id, a.cck AS cck, a.pk AS pk, a.storage_location as storage_location, b.id AS type_id, b.parent AS parent, b.permissions AS permissions'
				.	' FROM #__cck_core AS a'
				.	' JOIN #__cck_core_types AS b ON b.name = a.cck';

		if ( !is_array( $identifier ) && ( $classname = substr( strtolower( get_called_class() ), 11 ) ) != '' ) {
			$identifier		=	array( 0=>$classname, 1=>$identifier );
		}
		if ( is_array( $identifier ) ) {
			if( !isset( $identifier[1] ) ) {
				return false;
			}

			$core					=	JCckDatabase::loadObject( $query.' WHERE a.storage_location = "'.(string)$identifier[0].'" AND a.pk = '.(int)$identifier[1] );

			$this->_object			=	$identifier[0];
		} else {
			$core					=	JCckDatabase::loadObject( $query.' WHERE a.id = '.(int)$identifier );

			$this->_object			=	$core->storage_location;
		}
		if ( !( is_object( $core ) && $core->id && $core->pk ) ) {
			return false;
		}

		$this->_setObjectMap();

		$this->_table				=	$this->_columns['table'];

		if ( !$this->_table ) {
			$this->reset( true );

			return false;
		}

		$this->_id					=	$core->id;
		$this->_pk					=	$core->pk;
		$this->_type				=	$core->cck;
		$this->_type_id				=	$core->type_id;
		$this->_type_parent			=	$core->parent;
		$this->_type_permissions	=	$core->permissions;

		return true;
	}

	// _setObjectByType
	protected function _setObjectByType( $content_type )
	{
		$this->_type	=	$content_type;

		if ( !$this->_object || !$this->_table ) {
			$type		=	JCckDatabaseCache::loadObject( 'SELECT id, storage_location, parent, permissions FROM #__cck_core_types WHERE name = "'.$this->_type.'"' );
			
			if ( !is_object( $type ) ) {
				$this->log( 'error', 'Content Type not found.' );

				return false;
			}

			$this->_object		=	$type->storage_location;

			if ( !$this->_object ) {
				return false;
			}

			$this->_setObjectMap();

			$this->_table				=	$this->_columns['table'];
		} else {
			$type		=	JCckDatabaseCache::loadObject( 'SELECT id, parent, permissions FROM #__cck_core_types WHERE name = "'.$this->_type.'"' );

			if ( !is_object( $type ) ) {
				$this->log( 'error', 'Content Type not found.' );

				return false;
			}
		}

		$this->_type_id				=	$type->id;
		$this->_type_parent			=	$type->parent;
		$this->_type_permissions	=	$type->permissions;

		return true;
	}

	// _saveLegacy (deprecated)
	protected function _saveLegacy( $instance_name, $data )
	{
		$this->log( 'notice', 'Usage deprecated.' );

		if ( $instance_name == 'base' ) {
			if ( property_exists( $this->{'_instance_'.$instance_name}, 'language' ) && $this->{'_instance_'.$instance_name}->language == '' ) {
				$this->{'_instance_'.$instance_name}->language	=	'*';
			}
			$status			=	$this->store( $instance_name );

			if ( !$this->_pk && !$status && ( $this->_object == 'joomla_article' || $this->_object == 'joomla_category' ) ) {
				$i			=	2;
				$alias		=	$this->{'_instance_'.$instance_name}->alias.'-'.$i;
				$property	=	$this->_columns['parent'];
				$test		=	JTable::getInstance( 'Content' );
				
				while ( $test->load( array( 'alias'=>$alias, $property=>$this->{'_instance_'.$instance_name}->$property ) ) ) {
					$alias	=	$this->{'_instance_'.$instance_name}->alias.'-'.$i++;
				}
				$this->{'_instance_'.$instance_name}->alias	=	$alias;

				$status		=	$this->store( $instance_name );
			}
		} else {
			$status			=	$this->store( $instance_name );
		}

		return $status;
	}
}
?>