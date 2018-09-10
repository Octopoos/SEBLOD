<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: content.php oliviernolbert / sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Registry\Registry;

// JCckContent
class JCckContent
{
	protected static $incognito			=	array(
												'__construct'=>'',
												'_findResults'=>'',
												'_fixDatabase'=>'',
												'_getDataDispatch'=>'',
												'_getSearchQuery'=>'',
												'_getSearchQueryIndex'=>'',
												'_saveLegacy'=>'',
												'_setContentById'=>'',
												'_setContentByType'=>'',
												'_setDataMap'=>'',
												'_setObjectMap'=>'',
												'_setTypeMap'=>''
											);
	protected static $instances			=	array();
	protected static $instances_map		=	array();
	protected static $objects			=	array();
	protected static $types				=	array();

	protected $_dispatcher				=	null;
	protected $_options					=	null;

	protected $_data					=	null;
	protected $_data_preset				=	array();
	protected $_data_registry			=	array();
	protected $_data_update				=	array();
	protected $_error					=	false;
	protected $_id						=	0;
	protected $_instance_base			=	null;
	protected $_instance_core			=	null;
	protected $_instance_more			=	null;
	protected $_instance_more_parent	=	null;
	protected $_instance_more2			=	null;
	protected $_is_new					=	false; /* TODO#SEBLOD: reset? */
	protected $_logs					=	array(); /* TODO#SEBLOD: reset? */
	protected $_object					=	'';
	protected $_pk						=	0;
	protected $_search_query			=	null; /* TODO#SEBLOD: reset? */
	protected $_search_results			=	array(); /* TODO#SEBLOD: reset? */
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
		$this->_instance_base	=	JTable::getInstance( self::$objects[$this->_object]['properties']['table_object'][0], self::$objects[$this->_object]['properties']['table_object'][1] );
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
	protected function unsetInstance( $table_instance_name )
	{
		$this->{'_instance_'.$table_instance_name}	=	null;
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

			if ( !$this->_setContentById( $identifier ) ) {
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

	// batchAll
	public function batchAll()
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		$args	=	func_get_args();

		array_unshift( $args, $this->_search_results );

		call_user_func_array( array( $this, 'batch' ), $args );

		return $this;
	}

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

	// call
	public function call()
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		$args	=	func_get_args();
		$task	=	'_'.array_shift( $args );
		
		
		if ( isset( self::$incognito[$task] ) ) {
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
			$this->_search_results	=	array();
		} else {
			$this->_error	=	false;
		}

		return $this;
	}

	// create (^)
	public function create( $content_type, $data, $data_more = array(), $data_more2 = array() )
	{
		$this->reset();

		if ( !$this->_setContentByType( $content_type ) ) {
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
				$results	=	$this->_search_results;

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

					foreach ( $names as $table_instance_name=>$null ) {
						if ( count( $data[$table_instance_name] ) ) {
							$this->save( $table_instance_name, $data[$table_instance_name] );
						}
					}

					// Keep it for later
					self::$instances_map[$this->_id]				=	$this->_object.'_'.$this->_pk;
					self::$instances[$this->_object.'_'.$this->_pk]	=	$this;

					$this->_search_results	=	$results;

					return $this->_options->get( 'chain_methods', 1 ) ? $this : $this->_pk;
				} else {
					$this->_error			=	false;
					$this->_search_results	=	$results;
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
		$data_core	=	array(
							'cck'=>$this->_type,
							'pk'=>$this->_pk,
							'storage_location'=>$this->_object,
							'author_id'=>$this->getAuthor(),
							'parent_id'=>$data['core']['parent_id'],
							'date_time'=>$data['core']['date_time']
						);
		if ( !$data_core['author_id'] ) {
			$data_core['author_id']	=	JFactory::getUser()->id;
		}
		if ( !( $this->save( 'core', $data_core ) ) ) {
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

		foreach ( $names as $table_instance_name=>$null ) {
			if ( count( $data[$table_instance_name] ) ) {
				$this->{'_instance_'.$table_instance_name}->load( $this->_pk, true );
			
				if ( !( $this->save( $table_instance_name, $data[$table_instance_name] ) ) ) {
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

		if ( !$this->_setContentByType( $content_type ) ) {
			$this->reset();

			return false;
		}

		$this->setInstance( 'base' );
		$this->setInstance( 'more' );
		$this->setInstance( 'more_parent' );
		$this->setInstance( 'more2' );

		$db		=	JFactory::getDbo();
		$query	=	$this->_getSearchQuery( $content_type, $data, false );

		if ( $query === false ) {
			return false;
		}

		$query->select( 'COUNT('.$db->quoteName( 'a.pk' ).')' );

		$db->setQuery( $query );

		return (int)$db->loadResult();
	}

	// import (^)
	public function import( $content_type, $identifier )
	{
		$this->reset();

		if ( !$this->_setContentByType( $content_type ) ) {
			$this->reset();

			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		$this->_pk	=	$identifier;

		if ( !$this->setInstance( 'base', true ) ) {
			$this->reset();

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
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

		foreach ( $names as $table_instance_name=>$null ) {
			if ( is_object( $this->{'_instance_'.$table_instance_name} ) ) {
				$this->{'_instance_'.$table_instance_name}->load( $this->_pk, true );
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

		if ( !$this->_setContentById( $identifier ) ) {
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

		if ( !count( $this->_search_results ) ) {
			$this->_error	=	true;

			return $this;
		}

		$this->load( $this->_search_results[0] );

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

		$this->_data				=	null;
		$this->_data_registry		=	array();
		$this->_data_update			=	array();
		$this->_id					=	0;
		$this->_pk					=	0;
		$this->_type				=	'';
		$this->_type_id				=	0;
		$this->_type_parent			=	'';
		$this->_type_permissions	=	'';

		$this->_instance_core		=	JCckTable::getInstance( '#__cck_core', 'id' );

		$this->unsetInstance( 'base' );
		$this->unsetInstance( 'more' );
		$this->unsetInstance( 'more_parent' );
		$this->unsetInstance( 'more2' );

		/* TODO#SEBLOD: We may be able to refactor... */
		// if ( !$complete ) {
			// Same Content Type (content/item)
			// $this->resetInstance( 'base' );
			// $this->resetInstance( 'more' );
			// $this->resetInstance( 'more_parent' );
			// $this->resetInstance( 'more2' );		
		// } elseif ( $complete ) {
			// Different Content Type (object)
			// $this->resetInstance( 'base' );
			// $this->unsetInstance( 'more' );
			// $this->unsetInstance( 'more_parent' );
			// $this->resetInstance( 'more2' );
		// }

		if ( $complete ) {
			$this->_object			=	'';
			$this->_table 			=	'';			
		}

		return $this;
	}

	// set
	public function set( $table_instance_name, $property, $value )
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		if ( property_exists( $this->{'_instance_'.$table_instance_name}, $property ) ) {
			$this->{'_instance_'.$table_instance_name}->$property					=	$value;
			$this->_data_update[self::$types[$this->_type]['data_map'][$property]]	=	true;
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

		if ( isset( self::$types[$this->_type]['data_map'][$property] ) ) {
			$this->set( self::$types[$this->_type]['data_map'][$property], $property, $value );
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
			if ( !$this->_setContentByType( $content_type ) ) {
				$this->reset();

				$this->_error	=	true;

				return $this;
			}
			$this->_data	=	null;

			$this->unsetInstance( 'more' );
			$this->unsetInstance( 'more_parent' );

			$this->_setDataMap( 'base' );
			$this->setInstance( 'more', true );
			$this->setInstance( 'more_parent', true );
			$this->_setDataMap( 'more2' );
		}

		return $this;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Find

	// by
	public function by( $key, $direction = 'asc' )
	{
		if ( !isset( $this->_search_query ) ) {
			/* TODO#SEBLOD: error? */
			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		$this->_search_query['order'][$key]	=	$direction;

		return $this->_options->get( 'chain_methods', 1 ) ? $this : true;
	}

	// find (^)
	public function find( $content_type, $data = array() )
	{
		return $this->_findResults( 'find', true, $content_type, $data );
	}

	// findMore (^)
	public function findMore( $content_type, $data = array() )
	{
		return $this->_findResults( 'more', true, $content_type, $data );
	}

	// findPks ($)
	public function findPks()
	{
		return $this->_findResults( 'pks', false );
	}

	// search (^)
	public function search( $content_type, $data = array() )
	{
		$this->_search_query	=	array(
										'content_type'=>$content_type,
										'data'=>$data,
										'match'=>array(),
										'order'=>array()
									);

		return $this->_options->get( 'chain_methods', 1 ) ? $this : true;
	}

	// with
	public function with( $key, $match, $value = null )
	{
		if ( !isset( $this->_search_query ) ) {
			/* TODO#SEBLOD: error? */
			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		$this->_search_query['match'][$key]	=	$match;

		if ( isset( $value ) ) {
			$this->_search_query['data'][$key]	=	$value;
		}

		return $this->_options->get( 'chain_methods', 1 ) ? $this : true;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Get

	// get
	public function get( $table_instance_name, $property = '', $default = '' )
	{
		static $names	=	array(
								'base'=>'',
								'core'=>'',
								'more'=>'',
								'more_parent'=>'',
								'more2'=>'',
							);

		if ( isset( $names[$table_instance_name] ) ) {
			return $this->{'_instance_'.$table_instance_name}->get( $property, $default );
		} else {
			$this->log( 'notice', 'Usage deprecated.' );

			$default	=	$property;
			$property	=	$table_instance_name;

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

		if ( isset( self::$objects[$this->_object]['properties']['author'] ) && self::$objects[$this->_object]['properties']['author'] ) {
			$author_id	=	(int)$this->get( 'base', self::$objects[$this->_object]['properties']['author'], 0 );
		} elseif ( !$this->isNew() ) {
			$author_id	=	(int)$this->_instance_core->author_id;
		}

		return $author_id;
	}

	// getCallable
	public function getCallable()
	{
		$items		=	array();
		$methods	=	get_class_methods( $this );

		foreach ( $methods as $method ) {
			$pos	=	strpos( $method, '_' );
			
			if ( $pos !== false && $pos == 0 ) {
				if ( !isset( self::$incognito[$method] ) ) {
					$items[]	=	substr( $method, 1 );
				}
			}
		}

		return $items;
	}

	// getData
	public function getData( $table_instance_name = '' )
	{
		if ( $table_instance_name ) {
			$data	=	$this->{'_instance_'.$table_instance_name}->getProperties();

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

	// getDataObject
	public function getDataObject( $table_instance_name = '' )
	{
		return (object)$this->getData( $table_instance_name );
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

		if ( isset( self::$objects[$this->_object]['properties']['parent'] ) && self::$objects[$this->_object]['properties']['parent'] ) {
			$parent_id	=	(int)$this->get( 'base', self::$objects[$this->_object]['properties']['parent'], 0 );
		}

		return $parent_id;
	}

	// getPk
	public function getPk()
	{
		return (int)$this->_pk;
	}

	// getPks
	public function getPks()
	{
		return $this->_search_results;
	}

	// getProperty
	public function getProperty( $property, $default = '' )
	{
		if ( isset( self::$types[$this->_type]['data_map'][$property] ) ) {
			return $this->get( self::$types[$this->_type]['data_map'][$property], $property, $default );
		} else {
			$this->log( 'notice', 'Property unknown.' );
		}

		return $default;
	}

	// getRegistry
	public function getRegistry( $property )
	{
		if ( isset( self::$types[$this->_type]['data_map'][$property] ) ) {
			if ( !isset( $this->_data_registry[$property] ) ) {
				$this->_data_registry[$property]	=	new Registry( $this->get( self::$types[$this->_type]['data_map'][$property], $property ) );
			}

			return $this->_data_registry[$property];
		} else {
			$this->log( 'notice', 'Property unknown.' );
		}

		return new Registry;
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
			if ( !$this->can( 'save' ) ) {
				$this->log( 'error', 'Permissions denied.' );

				return false;
			}
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

		if ( get_class( $this ) == 'JCckContent' ) {
			$status	=	$this->_saveLegacy( $table_instance_name, $data );
		} else {
			// Let's make sure we have a valid instance		/* TODO#SEBLOD: should we move this check to the suitable function(s) */
			if ( !( $table_instance_name == 'base' || $table_instance_name == 'core' ) && empty( $this->{'_instance_'.$table_instance_name}->id ) ) {
				$this->_fixDatabase( $table_instance_name );
			}

			$method	=	'save'.ucfirst( $table_instance_name );
			$status	=	$this->$method();
		}

		if ( !$status ) {
			return $status;
		}

		switch ( $table_instance_name ) {
			case 'base':
				$this->_pk	=	$this->{'_instance_'.$table_instance_name}->{self::$objects[$this->_object]['properties']['key']};
				
				if ( $this->_instance_core->id ) {
					$data_core	=	array();

					if ( self::$objects[$this->_object]['properties']['author'] == self::$objects[$this->_object]['properties']['key'] ) {
						$data_core['author_id']	=	$this->{'_instance_'.$table_instance_name}->get( self::$objects[$this->_object]['properties']['key'], 0 );
					} elseif ( isset( $data[self::$objects[$this->_object]['properties']['author']] ) ) {
						$data_core['author_id']	=	$data[self::$objects[$this->_object]['properties']['author']];
					}
					if ( isset( $data[self::$objects[$this->_object]['properties']['parent']] ) ) {
						$data_core['parent_id']	=	$data[self::$objects[$this->_object]['properties']['parent']];
					}
					if ( count( $data_core ) ) {
						$this->save( 'core', $data_core );
					}
				}
				break;
			case 'core':
				$this->_id	=	$this->{'_instance_'.$table_instance_name}->id;
				
				if ( property_exists( $this->_instance_base, self::$objects[$this->_object]['properties']['custom'] ) ) {
					$this->_instance_base->{self::$objects[$this->_object]['properties']['custom']}	=	'::cck::'.$this->_id.'::/cck::';
					$this->store( 'base' );
				}
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
	public function store( $table_instance_name = '' )
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

		if ( !$table_instance_name ) {
			if ( !count( $this->_data_update ) ) {
				return false;
			}

			$successful	=	true;

			foreach ( $this->_data_update as $table_instance_name=>$null ) {
				if ( !$this->isNew() ) {
					// Let's make sure we have a valid instance
					if ( !( $table_instance_name == 'base' || $table_instance_name == 'core' ) && empty( $this->{'_instance_'.$table_instance_name}->id ) ) {
						$this->_fixDatabase( $table_instance_name );
					}
				}
				if ( $table_instance_name == 'base' ) {
					$successful	=	$this->storeBase();
				} else {
					$successful	=	$this->{'_instance_'.$table_instance_name}->store();	
				}
				if ( !$successful ) {
					$successful	=	false;
				} else {
					unset( $this->_data_update[$table_instance_name] );
				}
			}

			return $successful;
		}

		if ( !$this->isNew() ) {
			// Let's make sure we have a valid instance
			if ( !( $table_instance_name == 'base' || $table_instance_name == 'core' ) && empty( $this->{'_instance_'.$table_instance_name}->id ) ) {
				$this->_fixDatabase( $table_instance_name );
			}
		}

		if ( $table_instance_name == 'base' ) {
			return $this->storeBase();
		} else {
			return $this->{'_instance_'.$table_instance_name}->store();
		}
	}

	// storeBase
	protected function storeBase()
	{
		return $this->_instance_base->store();
	}

	// update ($)
	public function update( $table_instance_name, $property, $value )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		if ( !$this->can( 'update', $property ) ) {
			$this->log( 'error', 'Permissions denied.' );

			return false;
		}

		$check_permissions	=	$this->_options->get( 'check_permissions', 1 );
		$pre_update			=	$this->{'_instance_'.$table_instance_name}->$property;

		if ( $check_permissions ) {
			$this->_options->set( 'check_permissions', 0 );
		}
		
		$this->{'_instance_'.$table_instance_name}->$property	=	$value;

		if ( !( $result = $this->store( $table_instance_name ) ) ) {
			$this->{'_instance_'.$table_instance_name}->$property	=	$pre_update;
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

		$pre_update	=	$this->get( 'base', self::$objects[$this->_object]['properties']['author'], 0 );

		if ( isset( self::$objects[$this->_object]['properties']['author'] ) && self::$objects[$this->_object]['properties']['author'] ) {
			$property								=	self::$objects[$this->_object]['properties']['author'];
			$this->_instance_base->$property		=	$author_id;

			if ( !$this->store( 'base' ) ) {
				$this->_instance_base->$property	=	$pre_update;

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

		if ( isset( self::$types[$this->_type]['data_map'][$property] ) ) {
			return $this->update( self::$types[$this->_type]['data_map'][$property], $property, $value );
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

		if ( !( $event != '' && isset( self::$objects[$this->_object]['properties']['events'][$event] ) ) ) {
			return false;
		}

		return $this->$method( $event );
	}

	// triggerDelete
	public function triggerDelete( $event )
	{
		return $this->_dispatcher->trigger( self::$objects[$this->_object]['properties']['events'][$event], array( self::$objects[$this->_object]['properties']['context'], $this->_instance_base ) );
	}

	// triggerSave
	public function triggerSave( $event )
	{
		return $this->_dispatcher->trigger( self::$objects[$this->_object]['properties']['events'][$event], array( self::$objects[$this->_object]['properties']['context'], $this->_instance_base, $this->_is_new ) );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Misc
	
	// dump
	public function dump( $scope = 'this' )
	{
		if ( !function_exists( 'dump' ) ) {
			$this->log( 'notice', 'Function not found.' );

			return false;
		}

		if ( $scope == 'self' ) {
			dump( self::$objects, 'objects' );
			dump( self::$types, 'types' );
		} elseif ( $scope == 'callable' ) {
			dump( $this->getCallable() );
		} elseif ( $scope == 'log' ) {
			dump( $this->getLog() );
		} else {
			dump( $this->_data, 'data' );
			dump( $this->_error, 'error' );
			dump( $this->_id, 'id' );
			dump( $this->_is_new, 'isnew' );
			dump( $this->_logs, 'logs' );
			dump( $this->_object, 'object' );
			dump( $this->_pk, 'pk' );
			dump( $this->_search_results, 'results' );
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
		}

		return true;
	}

	// _findResults
	protected function _findResults( $method, $mode, $content_type = '', $data = array() )
	{
		$this->clear();

		if ( $method != 'more' ) {
			$this->clear( 'results' );
		}

		if ( $mode === true ) {
			$chain_methods		=	$this->_options->get( 'chain_methods', 1 );

			if ( $data === true ) {
				$data			=	array();
			}
		} else {
			$chain_methods		=	false;

			if ( isset( $this->_search_query ) ) {
				$content_type	=	$this->_search_query['content_type'];
				$data			=	true;
			}
		}

		if ( !$this->_setContentByType( $content_type ) ) {
			$this->reset();

			$this->_error	=	true;

			return $chain_methods ? $this : array();
		}

		$this->setInstance( 'base' );
		$this->setInstance( 'more' );
		$this->setInstance( 'more_parent' );
		$this->setInstance( 'more2' );

		$db		=	JFactory::getDbo();
		$query	=	$this->_getSearchQuery( $content_type, $data );

		if ( $query === false ) {
			$this->_error	=	true;

			return $chain_methods ? $this : array();
		}

		$query->select( $db->quoteName( 'a.pk' ) );

		$db->setQuery( $query );

		if ( $method == 'more' ) {
			$pks		=	array_flip( $this->_search_results );
			$results	=	$db->loadColumn();

			foreach ( $results as $pk ) {
				if ( !isset( $pks[$pk] ) ) {
					$this->_search_results[]	=	$pk;
				}
			}
		} else {
			$this->_search_results	=	$db->loadColumn();
		}

		if ( $mode === true ) {
			return $this->_options->get( 'chain_methods', 1 ) ? $this : $this->_search_results;
		}

		$this->_search_query	=	null;

		return $this->_search_results;
	}

	// _fixDatabase
	protected function _fixDatabase( $table_instance_name )
	{
		$data	=	$this->{'_instance_'.$table_instance_name}->getProperties();

		$this->{'_instance_'.$table_instance_name}->load( $this->_pk, true );
		$this->{'_instance_'.$table_instance_name}->bind( $data );
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
				if ( !isset( self::$types[$this->_type]['data_map'][$k] ) ) {
					continue;
				}

				$table_instance_name			=	self::$types[$this->_type]['data_map'][$k];
				$data[$table_instance_name][$k]	=	$v;
			}

			$this->_data_preset	=	array();
		}
		foreach ( $data as $name=>$array ) {
			$data_array	=	${'data_'.$name};

			if ( count( $data_array ) ) {
				foreach ( $data_array as $k=>$v ) {
					if ( !isset( self::$types[$this->_type]['data_map'][$k] ) ) {
						continue;
					}

					$table_instance_name				=	self::$types[$this->_type]['data_map'][$k];
					$data[$table_instance_name][$k]	=	$v;
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

		if ( isset( self::$objects[$this->_object]['properties']['author'] ) && self::$objects[$this->_object]['properties']['author']
		  && isset( $data['base'][self::$objects[$this->_object]['properties']['author']] ) ) {
			$data['core']['author_id']	=	$data['base'][self::$objects[$this->_object]['properties']['author']];
		}
		if ( !$data['core']['author_id'] ) {
			$data['core']['author_id']	=	JFactory::getUser()->id;
		}
		if ( isset( self::$objects[$this->_object]['properties']['parent'] ) && self::$objects[$this->_object]['properties']['parent']
		  && isset( $data['base'][self::$objects[$this->_object]['properties']['parent']] ) ) {
			$data['core']['parent_id']	=	$data['base'][self::$objects[$this->_object]['properties']['parent']];
		}

		/* TODO#SEBLOD: force to default author id when null? */
		/* TODO#SEBLOD: force to default parent_id when null? */

		return $data;
	}

	// _getSearchQuery
	protected function _getSearchQuery( $content_type, $data, $order = array() )
	{
		$db		=	JFactory::getDbo();
		$query	=	$db->getQuery( true );
		
		$tables	=	array(
						'base'=>'b'
					);

		$query->from( $db->quoteName( '#__cck_core', 'a' ) );
		$query->join( 'left', $db->quoteName( $this->_table, 'b' ).' ON '.$db->quoteName( 'b.'.self::$objects[$this->_object]['properties']['key'] ).' = '.$db->quoteName( 'a.pk' ) );
		$query->where( $db->quoteName( 'a.cck' ).' = '.$db->quote( $content_type ) );

		if ( $data === true ) {
			$data	=	$this->_search_query['data'];
			$match	=	$this->_search_query['match'];
			$order	=	$this->_search_query['order'];
		} else {
			$match	=	array();
		}

		foreach ( $data as $k=>$v ) {
			$index		=	$this->_getSearchQueryIndex( $query, $tables, $k );
			$operator	=	'';
			$where		=	'';

			if ( $index === false ) {
				return false;
			} elseif ( $index === '' ) {
				continue;
			}

			if ( isset( $match[$k] ) ) {
				$operator	=	$match[$k];
			}

			switch ( $operator ) {
				case '<':
				case '<=':
				case '>=':
				case '>':
					$where	=	' ' . $operator . ' ' . $db->quote( $v );
					break;
				case '=':
				default:
					$where	=	' = ' . $db->quote( $v );
					break;
			}

			$query->where( $db->quoteName( $index.'.'.$k ) . $where );
		}

		// Order
		$isOrdered	=	false;

		if ( is_array( $order ) ) {
			foreach ( $order as $k=>$v ) {
				$index	=	$this->_getSearchQueryIndex( $query, $tables, $k );

				if ( $index === false ) {
					return false;
				} elseif ( $index === '' ) {
					continue;
				}

				$query->order( $db->quoteName( $index.'.'.$k ) . strtoupper( $v ) );

				$isOrdered	=	true;
			}
		}
		if ( !$isOrdered && !is_bool( $order ) ) {
			$query->order( $db->quoteName( 'b.'.self::$objects[$this->_object]['properties']['key'] ) . ' DESC' );
		}

		return $query;
	}

	// _getSearchQueryIndex
	protected function _getSearchQueryIndex( &$query, &$tables, $key )
	{
		$db	=	JFactory::getDbo();

		if ( $key == self::$objects[$this->_object]['properties']['key'] ) {
			$table_instance_name	=	'base';
		} else {
			if ( !isset( self::$types[$this->_type]['data_map'][$key] ) ) {
				return false;
			}
			$table_instance_name	=	self::$types[$this->_type]['data_map'][$key];
		}

		if ( !isset( $tables[$table_instance_name] ) ) {
			switch ( $table_instance_name ) {
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

		return isset( $tables[$table_instance_name] ) ? $tables[$table_instance_name] : '';
	}

	// _setContentById
	protected function _setContentById( $identifier )
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

		if ( self::$objects[$this->_object]['properties']['table'] != '' ) {
			$this->_table			=	self::$objects[$this->_object]['properties']['table'];
		}

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

		$this->_setTypeMap();

		return true;
	}

	// _setContentByType
	protected function _setContentByType( $content_type )
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

			if ( self::$objects[$this->_object]['properties']['table'] != '' ) {
				$this->_table	=	self::$objects[$this->_object]['properties']['table'];	
			}
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

		$this->_setTypeMap();

		return true;
	}

	// _setDataMap
	protected function _setDataMap( $table_instance_name, $force = false )
	{
		if ( !is_object( $this->{'_instance_'.$table_instance_name} ) ) {
			return false;
		}

		$fields	=	$this->{'_instance_'.$table_instance_name}->getFields();

		foreach ( $fields as $k=>$v ) {
			if ( !isset( self::$types[$this->_type]['data_map'][$k] ) ) {
				self::$types[$this->_type]['data_map'][$k]	=	$table_instance_name;
			}
		}

		unset( self::$types[$this->_type]['data_map']['id'], self::$types[$this->_type]['data_map']['cck'] ); /* TODO#SEBLOD: remove "cck" column */

		return true;
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

		if ( isset( self::$objects[$object] ) ) {
			return true;
		}

		require_once JPATH_SITE.'/plugins/cck_storage_location/'.$object.'/'.$object.'.php';

		$properties	=	array(
							'author',
							'context',
							'context2',
							'custom',
							'events',
							'key',
							'parent',
							'table',
							'table_object'
						);

		self::$objects[$object]	=	array(
										'columns'=>array(),
										'properties'=>JCck::callFunc( 'plgCCK_Storage_Location'.$object, 'getStaticProperties', $properties )
									);

		if ( !( isset( self::$objects[$object]['properties']['context2'] ) && self::$objects[$object]['properties']['context2'] != '' ) ) {
			self::$objects[$object]['properties']['context2']	=	self::$objects[$object]['properties']['context'];
		}

		return true;
	}

	// _setTypeMap
	protected function _setTypeMap()
	{
		if ( !$this->_type ) {
			return false;
		}

		if ( isset( self::$types[$this->_type] ) ) {
			return true;
		}

		self::$types[$this->_type]	=	array(
											'data_map'=>array()
										);

		return true;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Deprecated

	// batchResults (deprecated)
	public function batchResults()
	{
		$args	=	func_get_args();

		call_user_func_array( array( $this, 'batchAll' ), $args );

		return $this;
	}

	// getResults (deprecated)
	public function getResults()
	{
		return $this->getPks();
	}

	// _saveLegacy (deprecated)
	protected function _saveLegacy( $table_instance_name, $data )
	{
		$this->log( 'notice', 'Usage deprecated.' );

		if ( $table_instance_name == 'base' ) {
			if ( property_exists( $this->{'_instance_'.$table_instance_name}, 'language' ) && $this->{'_instance_'.$table_instance_name}->language == '' ) {
				$this->{'_instance_'.$table_instance_name}->language	=	'*';
			}
			$status			=	$this->store( $table_instance_name );

			if ( !$this->_pk && !$status && ( $this->_object == 'joomla_article' || $this->_object == 'joomla_category' ) ) {
				$i			=	2;
				$alias		=	$this->{'_instance_'.$table_instance_name}->alias.'-'.$i;
				$property	=	self::$objects[$this->_object]['properties']['parent'];
				$test		=	JTable::getInstance( 'Content' );
				
				while ( $test->load( array( 'alias'=>$alias, $property=>$this->{'_instance_'.$table_instance_name}->$property ) ) ) {
					$alias	=	$this->{'_instance_'.$table_instance_name}->alias.'-'.$i++;
				}
				$this->{'_instance_'.$table_instance_name}->alias	=	$alias;

				$status		=	$this->store( $table_instance_name );
			}
		} else {
			$status			=	$this->store( $table_instance_name );
		}

		return $status;
	}
}
?>