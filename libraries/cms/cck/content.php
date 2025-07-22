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
	protected static $callables			=	array();
	protected static $callables_map		=	array();
	protected static $incognito			=	array(
												'__call'=>'',
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
												'_setCallable'=>'',
												'_setMixin'=>'',
												'_setObjectMap'=>'',
												'_setTypeMap'=>''
											);
	protected static $instances			=	array();
	protected static $instances_map		=	array();
	protected static $objects			=	array();
	protected static $types				=	array();

	protected $_options					=	null;

	protected $_callables				=	array();
	protected $_data					=	null;
	protected $_data_preset				=	array();
	protected $_data_preset_null		=	false;
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
	protected $_relationship_nav		=	null; /* TODO#SEBLOD: reset? */
	protected $_search_query			=	null; /* TODO#SEBLOD: reset? */
	protected $_search_relationship		=	null; /* TODO#SEBLOD: reset? */
	protected $_search_results			=	array(); /* TODO#SEBLOD: reset? */
	protected $_table 					=	'';
	protected $_type					=	'';
	protected $_type_id					=	0;
	protected $_type_parent				=	'';
	protected $_type_permissions		=	'';
	protected $_type_properties			=	null;
	protected $_type_relationships		=	null;

	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct

	// __construct
	public function __construct()
	{
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

	// setRelationship
	public function setRelationship( $mode, $alias, $data = array(), $scope = 'type' )
	{
		if ( $scope == 'object' ) {
			/* TODO */
		} elseif ( $scope == 'type' ) {
			if ( !isset( self::$types[$this->_type]['relationships'][$mode][$alias] ) ) {
				self::$types[$this->_type]['relationships'][$mode][$alias]	=	$data;
			}
		}

		// if ( !isset( self::$types[$this->_type]['relationships'][$mode][$alias] ) ) {
			// self::$types[$this->_type]['relationships'][$mode][$alias]	=	$data;

			// if ( !isset( self::$objects[$object]['relations']['many'][$relation] ) ) {
			// 	self::$objects[$object]['relations']['many'][$root_object][$relation]		=	array(
			// 																						'property'=>$property
			// 																					);
			// }
		// }

		return $this;
	}

	// setRelation
	// protected function setRelation( $relation, $object, $property, $root_object = '' )
	// {
	// 	if ( !$root_object ) {
	// 		$root_object	=	$this->_object;
	// 	}
	// 	if ( !$relation || !$object || !$property ) {
	// 		return false;
	// 	}

/*
ARTICLE
{"mode":"one","name":"author","object":"joomla_user","column":"created"}
{"mode":"one","name":"parent","object":"joomla_category","column":"catid"}

{"mode":"many","name":"bottles","object":"joomla_article","column":"id2","table":"#__cck_store_join_o_wine_bottles"}
VV
{"mode":"many","name":"bottles","object":"","type":"o_year","column":"id2","table":"#__cck_store_join_o_wine_bottles"}

USER
{"mode":"many","name":"author","object":"joomla_article","column":"created"}
*/

	// 	if ( !isset( self::$objects[$root_object]['relations']['one'][$relation] ) ) {
	// 		self::$objects[$root_object]['relations']['one'][$relation]	=	array(
	// 																				'object'=>$object,
	// 																				'property'=>$property
	// 																			);

	// 		if ( !isset( self::$objects[$object]['relations']['many'][$relation] ) ) {
	// 			self::$objects[$object]['relations']['many'][$root_object][$relation]		=	array(
	// 																								'property'=>$property
	// 																							);
	// 		}
	// 	}

	// 	return true;
	// }

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
	public function delete( $identifier = 0, $force = '' )
	{
		if ( $identifier ) {
			$this->reset();

			if ( !$this->_setContentById( $identifier ) ) {
				if ( $force ) {
					if ( !$this->import( $force, $identifier )->isSuccessful() ) {
						return false;
					}
				} else {
					return false;
				}
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
		} elseif ( !$this->isSuccessful() ) {
			return false;
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

		if ( !(int)$this->_deleteRelationships( 'before' ) ) {
			return false;
		}

		if ( !$this->remove() ) {
			return false;
		}

		$this->_deleteRelationships();

		$this->trigger( 'delete', 'after' );

		return true;
	}

	// _deleteRelationships
	protected function _deleteRelationships( $event = 'after' )
	{
		if ( !$this->_type_relationships ) {
			return true;
		}

		$do		=	true;

		foreach ( $this->_type_relationships as $relationship ) {
			if ( !isset( $relationship['params']['delete'] ) ) {
				$relationship['params']['delete']	=	true;
			}

			if ( $relationship['params']['delete'] === true ) {
				// OK
			} elseif ( $relationship['params']['delete'] === false ) {
				if ( $event !== 'after' ) {
					if ( $relationship['mode'] === 'many2one' ) {
						$key	=	isset( $relationship['params']['property'] ) && $relationship['params']['property'] ? '"'.$this->getProperty( $relationship['params']['property'] ).'"' : (int)$this->_pk;

						if ( (int)JCckDatabase::loadResult( 'SELECT COUNT(id) FROM '.$relationship['params']['table'].' WHERE '.$relationship['params']['column'].' = '.$key ) > 0 ) {
							return false;
						}
					} else {
						if ( (int)JCckDatabase::loadResult( 'SELECT COUNT(id2) FROM '.$relationship['params']['table'].' WHERE id = '.(int)$this->_pk ) > 0 ) {
							return false;
						}
					}
				}
			} else {
				if ( $event === 'after' ) {
					$pks	=	array();

					if ( $relationship['mode'] === 'many2one' ) {
						if ( is_array( $relationship['params']['delete'] ) ) {
							$key	=	isset( $relationship['params']['property'] ) && $relationship['params']['property'] ? '"'.$this->getProperty( $relationship['params']['property'] ).'"' : (int)$this->_pk;
							$pks	=	(array)JCckDatabase::loadColumn( 'SELECT id FROM '.$relationship['params']['table'].' WHERE '.$relationship['params']['column'].' = '.$key );
						}
					} else {
						if ( is_array( $relationship['params']['delete'] ) ) {
							if ( $relationship['params']['delete']['children'] === true ) {
								$query	=	'SELECT id2'
										.	' FROM '.$relationship['params']['table']
										.	' GROUP BY id2'
										.	' HAVING COUNT(CASE WHEN id = '.(int)$this->_pk.' THEN 1 END) = 1'
										.	' AND COUNT(CASE WHEN id != '.(int)$this->_pk.' THEN 1 END) = 0'
										;

								$pks	=	(array)JCckDatabase::loadColumn( $query );
							}
						}
					}

					if ( count( $pks ) ) {
						if ( isset( $relationship['params']['delete']['do'] ) && $relationship['params']['delete']['do'] != 'delete' ) {
							foreach ( $relationship['params']['delete']['do'] as $do_tasks ) {
								foreach ( $do_tasks as $do_task=>$do_params ) {
									$task	=	'_'.$do_task.'RelationshipsItems';

									$this->$task( $pks, $relationship, $do_params );
								}
							}
						} else {
							$this->_deleteRelationshipsItems( $pks, $relationship );
						}
					}
				}
			}
		}

		return $do;
	}

	// _deleteRelationshipsItems
	protected function _deleteRelationshipsItems( $pks, $relationship, $params = null )
	{
		$content_instance	=	JCckType::getInstance( $relationship['params']['type'] )->getContentInstance();
		$do_children		=	true;

		if ( isset( $relationship['params']['delete']['with'] ) && is_array( $relationship['params']['delete']['with'] ) ) {
			$do_children	=	array(
									'key'=>$relationship['params']['delete']['with'][0],
									'match'=>$relationship['params']['delete']['with'][1],
									'value'=>$relationship['params']['delete']['with'][2],
								);
		}

		foreach ( $pks as $pk ) {
			if ( $do_children === true ) {
				$content_instance->delete( $pk );
			} else {
				if ( $content_instance->load( $pk )->isSuccessful() ) {
					if ( $content->hasProperty( $do_children['key'], $do_children['match'], $do_children['value'] ) ) {
						$content_instance->delete();
					}
				}
			}
		}

		return true;
	}

	// _updatePropertyRelationshipsItems
	protected function _updatePropertyRelationshipsItems( $pks, $relationship, $params = null )
	{
		$content_instance	=	JCckType::getInstance( $relationship['params']['type'] )->getContentInstance();
		$do_params			=	array(
									'key'=>$params[0],
									'value'=>$params[1]
								);

		foreach ( $pks as $pk ) {
			if ( $content_instance->load( $pk )->isSuccessful() ) {
				$content_instance->updateProperty( $do_params['key'], $do_params['value'] );				
			}
		}

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

		static $names	=	array(
								'more'=>'',
								'more_parent'=>'',
								'more2'=>''
							);

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

		// Preset may set an error
		if ( !$this->isSuccessful() ) {
			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		$this->_is_new	=	true;

		// Trigger BeforeStore
		if ( !$this->triggerMore( 'save', 'before_store', $data ) ) {
			$this->_error	=	true;
			$this->_is_new	=	false;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

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
		foreach ( $names as $table_instance_name=>$null ) {
			if ( count( $data[$table_instance_name] ) ) {
				if ( $this->{'_instance_'.$table_instance_name}->load( $this->_pk, true ) ) {
					$names[$table_instance_name]	=	$this->_pk;
				}

				$r	=	false;
			
				try {
					if ( !( $this->save( $table_instance_name, $data[$table_instance_name] ) ) ) {
						$r	=	true;
					}	
				} catch ( Exception $e ) {
					if ( $e->getCode() == 1062 ) {
						$this->log( 'error', 'Duplicate entry.' );
					}

					$r	=	true;
				}

				if ( $r === true ) {
					// Rollback
					if ( $this->_pk ) {
						$this->delete( $this->_pk );
					}

					$this->_error	=	true;
					$this->_id		=	0;
					$this->_is_new	=	false;
					$this->_pk		=	0;
					
					return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
				}
			}
		}

		// Trigger AfterStore
		$this->triggerMore( 'save', 'after_store' );
		
		$this->_is_new	=	false;

		// Keep it for later
		self::$instances_map[$this->_id]				=	$this->_object.'_'.$this->_pk;
		self::$instances[$this->_object.'_'.$this->_pk]	=	$this;
		
		return $this->_options->get( 'chain_methods', 1 ) ? $this : $this->_pk;
	}

	// createFromFile (^)
	public function createFromFile( $content_type, $path )
	{
		if ( !is_file( $path ) ) {
			$this->reset();

			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		$data	=	file_get_contents( $path );
		$data	=	json_decode( $data, true );

		$this->create( $content_type, $data );

		return $this->_options->get( 'chain_methods', 1 ) ? $this : $this->_pk;
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

		$this->_decrypt();

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
	public function log( $type, $message )
	{
		if ( !isset( $this->_logs[$type] ) ) {
			$this->_logs[$type]	=	array();
		}

		$this->_logs[$type][]	=	$message;
	}

	// preset
	public function preset( $data, $check_null = false )
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		$this->_data_preset			=	$data;
		$this->_data_preset_null	=	$check_null;

		return $this;
	}

	// relate
	public function relate( $relationship )
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		if ( isset( self::$objects[$this->_object]['relationships']['one'][$relationship] ) ) {
			if ( isset( self::$objects[$this->_object]['relationships']['one'][$relationship]['object'] )
			  && isset( self::$objects[$this->_object]['relationships']['one'][$relationship]['property'] ) ) {
				return JCckContent::getInstance( array( self::$objects[$this->_object]['relationships']['one'][$relationship]['object'], $this->getProperty( self::$objects[$this->_object]['relationships']['one'][$relationship]['property'] ) ) );
			} else {
				return new JCckContent; /* TODO#SEBLOD */
			}
		// } elseif () {
		} else {
			$this->_relationship_nav	=	$relationship;
		}

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
			if ( is_array( $value ) ) {
				$value	=	json_encode( $value );
			}

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

	// setRegistry
	// public function setRegistry( $property )
	// {
		
	// }

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

	// countRelations
	// public function countRelations( $relation, $content_type )
	// {
	// 	if ( !$this->_pk ) {
	// 		return; /* TODO#SEBLOD */
	// 	}

	// 	$object			=	$this->_getObjectByType( $content_type );

	// 	if ( !( isset( self::$objects[$this->_object]['relations']['many'][$object][$relation] )
	// 		 && isset( self::$objects[$this->_object]['relations']['many'][$object][$relation]['property'] ) ) ) {
	// 		return 0; /* TODO#SEBLOD */
	// 	}

	// 	$classname		=	'JCckContent'.ucwords( $object, '_' );
	// 	$content_item	=	new $classname;
	// 	$data			=	array(
	// 							self::$objects[$this->_object]['relations']['many'][$object][$relation]['property']=>$this->_pk
	// 						);

	// 	return $content_item->count( $content_type, $data );
	// }

	// find (^)
	public function find( $content_type = '', $data = array() )
	{
		if ( $content_type != '' ) {
			return $this->_findResults( 'find', true, $content_type, $data );
		} else {
			return $this->_findResults( 'find', true );
		}
	}

	// findMore (^)
	public function findMore( $content_type = '', $data = array() )
	{
		if ( $content_type != '' ) {
			return $this->_findResults( 'more', true, $content_type, $data );
		} else {
			return $this->_findResults( 'more', true );
		}
	}

	// findOne (^)
	public function findOne( $content_type = '', $data = array() )
	{
		if ( $content_type != '' ) {
			$this->search( $content_type, $data )->limit( 1 );
			$this->_findResults( 'find', true );

			if ( !count( $this->_search_results ) ) {
				$this->_error	=	true;

				return $this;
			} else {
				$this->load( $this->_search_results[0] );
			}
		} else {
			// TODO
		}

		return $this;
	}

	// findPk (^$)
	public function findPk( $content_type = '', $data = array() )
	{
		if ( $content_type != '' ) {
			$this->search( $content_type, $data )->limit( 1 );
			$this->_findResults( 'find', true );

			if ( !count( $this->_search_results ) ) {
				$this->_error	=	true;

				return 0;
			} else {
				return (int)$this->_search_results[0];
			}
		} else {
			// TODO
		}

		return $this;
	}

	// findPks ($)
	public function findPks()
	{
		return $this->_findResults( 'pks', false );
	}

	// limit
	public function limit( $limit = 0 )
	{
		if ( !isset( $this->_search_query ) ) {
			/* TODO#SEBLOD: error? */
			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		$this->_search_query['limit']	=	$limit;

		return $this->_options->get( 'chain_methods', 1 ) ? $this : true;
	}

	// search (^)
	public function search( $content_type, $data = array() )
	{
		$this->_search_query	=	array(
										'content_type'	=> $content_type,
										'data'			=> $data,
										'limit'			=> 0,
										'match'			=> array(),
										'match_each'	=> array(),
										'order'			=> array()
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

		if ( $match == 'empty' ) {
			$value	=	'';
		}
		if ( isset( $value ) ) {
			$this->_search_query['data'][$key]	=	$value;
		}

		return $this->_options->get( 'chain_methods', 1 ) ? $this : true;
	}

	// withEach
	public function withEach( $key, $match, $value = null )
	{
		if ( !isset( $this->_search_query ) ) {
			/* TODO#SEBLOD: error? */
			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		$this->_search_query['match'][$key]			=	$match;
		$this->_search_query['match_each'][$key]	=	true;

		if ( $match == 'empty' ) {
			$value	=	'';
		}
		if ( isset( $value ) ) {
			$this->_search_query['data'][$key]	=	$value;
		}

		return $this->_options->get( 'chain_methods', 1 ) ? $this : true;
	}

	// withRelationship
	public function withRelationship( $relationship, $value = null )
	{
		if ( is_bool( $value ) || is_string( $value ) ) {
			// OK
		} elseif ( is_array( $value ) ) {
			// ...
		}

		if ( !is_array( $this->_search_relationship ) ) {
			$this->_search_relationship	=	array();
		}

		$this->_search_relationship[]	=	array(
												'data'=>$value,
												'name'=>$relationship
											);

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
		if ( strpos( $property, '.' ) !== false ) {
			$parts	=	explode( '.', $property );

			return $this->getRegistry( $parts[0] )->get( $parts[1] );
		} elseif ( isset( self::$types[$this->_type]['data_map'][$property] ) ) {
			return $this->get( self::$types[$this->_type]['data_map'][$property], $property, $default );
		} else {
			$this->log( 'notice', 'Property unknown.' );
		}

		return $default;
	}

	// hasProperty
	public function hasProperty( $property, $match = null, $value = null )
	{
		if ( $match === null ) {
			if ( isset( self::$types[$this->_type]['data_map'][$property] ) ) {
				return true;
			}
		} else {
			switch ( $match ) {
				case 'in':
					$parts	=	explode( ',', $value );

					foreach ( $parts as $part ) {
						if ( $this->getProperty( $property ) == $part ) {
							return true;
						}
					}
					break;
				case '=':
				default:
					if ( $this->getProperty( $property ) == $value ) {
						return true;
					}
					break;
			}
		}

		return false;
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

	// hasCallable
	public function hasCallable( $name )
	{
		$scope	=	self::$callables_map[$name];

		if ( $scope == 'object' ) {
			if ( !isset( self::$objects[$this->_object]['callables'][$name] ) ) {
				return false;
			}
		} elseif ( $scope == 'type' ) {
			if ( !isset( self::$types[$this->_type]['callables'][$name] ) ) {
				return false;
			}
		} elseif ( $scope == 'global' ) {
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
	public function isNew()
	{
		return $this->_is_new;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Relate

	// getAll
	public function getAll()
	{
		$table_name	=	$this->_getRelationshipDefinition( 'table' );

		if ( !$table_name ) {
			$this->_error	=	true;

			return false;
		}

		$pks	=	array();
		$table	=	JCckTableRelationship::getInstance( $table_name );

		$table->load( $this->_pk );

		return $table->getRows();
	}

	// listAll
	public function listAll( $search_type )
	{
		$table_name	=	$this->_getRelationshipDefinition( 'table' );

		if ( !$table_name ) {
			$this->_error	=	true;

			return false;
		}

		$pks	=	array();
		$table	=	JCckTableRelationship::getInstance( $table_name );

		$table->load( $this->_pk );

		foreach ( $table->getRows() as $pk=>$rows ) {
			$pks[]	=	$pk;
		}
		if ( count( $pks ) ) {
			$list	=	new JCckList();

			return $list->load( $search_type, $pks )->output();
		}

		return '';
	}

	// tie
	public function tie( $id, $data = array() )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		$table_name	=	$this->_getRelationshipDefinition( 'table' );

		if ( !$table_name ) {
			$this->_error	=	true;

			return false;
		}

		$table	=	JCckTableRelationship::getInstance( $table_name );

		$table->load( $this->_pk );

		if ( $this->_getRelationshipDefinition( 'multiple' ) ) {
			$table->insertRow( $id, $data );
		} else {
			$table->setRow( $id, $data );
		}

		return $table->store();
	}

	// tieAll
	public function tieAll( $data = array() )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		$table_name	=	$this->_getRelationshipDefinition( 'table' );

		if ( !$table_name ) {
			$this->_error	=	true;

			return false;
		}

		$table	=	JCckTableRelationship::getInstance( $table_name );

		$table->load( $this->_pk );
		$table->setRows( $data );

		return $table->store();
	}

	// untie
	public function untie( $id )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		$table_name	=	$this->_getRelationshipDefinition( 'table' );

		if ( !$table_name ) {
			$this->_error	=	true;

			return false;
		}

		$table	=	JCckTableRelationship::getInstance( $table_name );

		$table->load( $this->_pk, false );

		return $table->deleteRow( $id );
	}

	// untieAll
	public function untieAll()
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		$table_name	=	$this->_getRelationshipDefinition( 'table' );

		if ( !$table_name ) {
			$this->_error	=	true;

			return false;
		}

		$table	=	JCckTableRelationship::getInstance( $table_name );

		return $table->delete( $this->_pk );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Save

	// change ($)
	protected function change( $table_instance_name, $property, $value )
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

		if ( $table_instance_name != 'core' ) {
			$this->_encrypt( $table_instance_name );
		}

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

				$this->_encrypt( $table_instance_name );

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

		$this->_encrypt( $table_instance_name );

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
	public function update( $data = array() )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		if ( !$this->_pk ) {
			return false;
		}

		if ( !$this->can( 'save' ) ) {
			$this->log( 'error', 'Permissions denied.' );

			return false;
		}

		if ( count( $data ) ) {
			foreach ( $data as $k=>$v ) {
				$this->setProperty( $k, $v );
			}
		}
		if ( !count( $this->_data_update ) ) {
			return false;
		}

		// Trigger BeforeStore
		if ( !$this->triggerMore( 'save', 'before_store', $data ) ) {
			$this->_error	=	true;
			$this->_is_new	=	false;

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
				$successful	=	$this->save( 'base' );
			} else {
				$successful	=	$this->save( $table_instance_name );
			}
			if ( !$successful ) {
				$successful	=	false;
			} else {
				unset( $this->_data_update[$table_instance_name] );
			}
		}

		// Trigger AfterStore
		$this->triggerMore( 'save', 'after_store' );

		return $successful;
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
			return $this->change( self::$types[$this->_type]['data_map'][$property], $property, $value );
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
		return $this->_triggerEvent( self::$objects[$this->_object]['properties']['events'][$event], array( self::$objects[$this->_object]['properties']['context'], $this->_instance_base ) );
	}

	// triggerMore
	protected function triggerMore( $task, $event, &$data = array() )
	{
		return true; /* PENDING HERE */

		$check	=	$this->_options->get( 'trigger_events', 1 );

		if ( !( ( is_array( $check ) && in_array( $task, $check ) ) || $check == 1 ) ) {
			return true;
		}

		if ( !JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			return true;
		}

		$events		=	array(
							'after_store'=>'onCckPostAfterStore',
							'before_store'=>'onCckPostBeforeStore'
						);

		if ( isset( $events[$event] ) ) {
			$event			=	$events[$event];
			$processings	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 AND type IN ("onCckPreBeforeStore","onCckPostBeforeStore","onCckPreAfterStore","onCckPostAfterStore") ORDER BY ordering', 'type' );

			if ( isset( $processings[$event] ) ) {
				if ( $event === 'onCckPostAfterStore' ) {
					$this->_options->set( 'encrypt_data', 0 );
				}

				foreach ( $processings[$event] as $p ) {
					$process	=	new JCckProcessingContent( $event, JPATH_SITE.$p->scriptfile, $p->options, true );
					$result		=	call_user_func_array( array( $process, 'execute' ), array( &$this, &$data ) );

					if ( !$result ) {
						return false;
					}
				}
			}
		}

		return true;
	}

	// triggerSave
	public function triggerSave( $event )
	{
		return $this->_triggerEvent( self::$objects[$this->_object]['properties']['events'][$event], array( self::$objects[$this->_object]['properties']['context'], $this->_instance_base, $this->_is_new, $this->getData() ) );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Misc

	// __call
	public function __call( $method, $parameters )
	{
		if ( !$this->hasCallable( $method ) ) {
			throw new BadMethodCallException( 'Method not found.' );
		}

		$scope	=	self::$callables_map[$method];

		if ( $scope == 'object' ) {
			$callable	=	self::$objects[$this->_object]['callables'][$method];
		} elseif ( $scope == 'type' ) {
			$callable	=	self::$types[$this->_type]['callables'][$method];
		} elseif ( $scope == 'global' ) {
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
			$dump( self::$objects, 'objects' );
			$dump( self::$types, 'types' );
		} elseif ( $scope == 'callable' ) {
			$dump( $this->getCallable() );
		} elseif ( $scope == 'log' ) {
			$dump( $this->getLog() );
		} else {
			$dump( $this->_callables, 'callables' );
			$dump( $this->_data, 'data' );
			$dump( $this->_error, 'error' );
			$dump( $this->_id, 'id' );
			$dump( $this->_is_new, 'isnew' );
			$dump( $this->_logs, 'logs' );
			$dump( $this->_object, 'object' );
			$dump( $this->_pk, 'pk' );
			$dump( $this->_search_results, 'results' );
			$dump( $this->_table, 'table' );
			$dump( $this->_type, 'type' );
			$dump( $this->_type_id, 'type_id' );
			$dump( $this->_type_parent, 'type_parent' );
			$dump( $this->_type_permissions, 'type_permissions' );
			$dump( $this->_type_relationships, 'type_relationships' );

			if ( $this->_instance_base ) {
				$dump( $this->_instance_base, 'base' );
			}
			if ( $this->_instance_core ) {
				$dump( $this->_instance_core, 'core' );
			}
			if ( $this->_instance_more ) {
				$dump( $this->_instance_more, 'more' );
			}
			if ( $this->_instance_more_parent ) {
				$dump( $this->_instance_more_parent, 'more_parent' );
			}
			if ( $this->_instance_more2 ) {
				$dump( $this->_instance_more2, 'more2' );
			}
		}

		return true;
	}

	// extend
	public function extend( $path, $scope = 'instance', $scope_target = '' )
	{
		if ( !is_file( $path ) ) {
			$this->_error	=	true;

			return $this->_options->get( 'chain_methods', 1 ) ? $this : false;
		}

		ob_start();
		include $path;
		ob_get_clean();

		$this->_setMixin( $mixin, $scope, $scope_target );
	}

	// _decrypt
	protected function _decrypt()
	{
		if ( $this->_type_properties ) {
			$my_app	=	JCckApp::getInstance();
			$my_app->loadDefault();

			foreach ( $this->_type_properties as $property=>$data ) {
				if ( isset( self::$types[$this->_type]['data_map'][$property] ) && self::$types[$this->_type]['data_map'][$property] ) {
					if ( isset( $data['crypt'] ) ) {
						if ( $data['crypt'] === true ) {
							$table_instance_name	=	self::$types[$this->_type]['data_map'][$property];

							if ( property_exists( $this->{'_instance_'.$table_instance_name}, $property ) ) {
								$value	=	$this->{'_instance_'.$table_instance_name}->$property;

								if ( isset( $value ) && $value !== '' ) {
									$this->{'_instance_'.$table_instance_name}->$property	=	$my_app->decrypt( $value );
								}
							}
						}
					}
				}
			}
		}
	}

	// _encrypt
	protected function _encrypt( $table_instance_name = '' )
	{
		if ( !$this->_options->get( 'encrypt_data', 1 ) ) {
			return;
		}

		if ( $this->_type_properties ) {
			$my_app	=	JCckApp::getInstance();
			$my_app->loadDefault();

			foreach ( $this->_type_properties as $property=>$data ) {
				if ( isset( self::$types[$this->_type]['data_map'][$property] ) && self::$types[$this->_type]['data_map'][$property]
				&& ( $table_instance_name === '' || $table_instance_name == self::$types[$this->_type]['data_map'][$property] ) ) {
					if ( isset( $data['crypt'] ) ) {
						if ( $data['crypt'] === true ) {
							$table_instance_name	=	self::$types[$this->_type]['data_map'][$property];

							if ( property_exists( $this->{'_instance_'.$table_instance_name}, $property ) ) {
								$value	=	$this->{'_instance_'.$table_instance_name}->$property;

								if ( isset( $value ) && $value !== '' ) {
									$this->{'_instance_'.$table_instance_name}->$property	=	$my_app->encrypt( $value );
								}
							}
						}
					}
				}
			}
		}
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
			} elseif ( !$content_type && isset( $this->_search_query ) ) {
				$content_type	=	$this->_search_query['content_type'];
				$data			=	true;
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
				if ( $this->_data_preset_null ) {
					if ( empty( $v ) || $v == '0000-00-00' || $v == '0000-00-00 00:00:00' ) {
						$this->_error	=	true;
					}
				}

				$table_instance_name			=	self::$types[$this->_type]['data_map'][$k];
				$data[$table_instance_name][$k]	=	$v;
			}

			$this->_data_preset			=	array();
			$this->_data_preset_null	=	false;
		}
		foreach ( $data as $name=>$array ) {
			$data_array	=	${'data_'.$name};

			if ( count( $data_array ) ) {
				foreach ( $data_array as $k=>$v ) {
					if ( !isset( self::$types[$this->_type]['data_map'][$k] ) ) {
						continue;
					}

					$table_instance_name			=	self::$types[$this->_type]['data_map'][$k];

					if ( is_array( $v ) ) {
						$v	=	json_encode( $v );
					}

					$data[$table_instance_name][$k]	=	$v;
				}
			}
		}

		if ( !JCck::is( '5.0' ) ) {
			if ( count( $data['more2'] ) ) {
				if ( !isset( $data['more2']['cck'] ) ) { /* TODO#SEBLOD: remove "cck" column */
					$data['more2']['cck']	=	$this->_type;
				}
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

	// _getDataMapInstance
	public function _getDataMapInstance( $property )
	{
		if ( isset( self::$types[$this->_type]['data_map'][$property] ) ) {
			return self::$types[$this->_type]['data_map'][$property];
		}

		return '';
	}

	// _getRelationshipDefinition
	protected function _getRelationshipDefinition( $property )
	{
		if ( isset( self::$objects[$this->_object]['relationships']['many'][$this->_relationship_nav] ) ) {
			return self::$objects[$this->_object]['relationships']['many'][$this->_relationship_nav][$property];
		} elseif ( isset( self::$types[$this->_type]['relationships']['many'][$this->_relationship_nav] ) ) {
			return self::$types[$this->_type]['relationships']['many'][$this->_relationship_nav][$property];
		} else {
			return '';
		}
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

		if ( strpos( $content_type, '|' ) !== false ) {
			$parts		=	explode( '|', $content_type );
			$where_type	=	array();
			
			foreach ( $parts as $part ) {
				$where_type[] 	=	JCckDatabase::quote( $part );
			}

			$where_type	=	' IN ('.implode( ',', $where_type ).')';
		} else {
			$where_type	=	' = '.$db->quote( $content_type );
		}

		$query->where( $db->quoteName( 'a.cck' ).$where_type );

		if ( $data === true ) {
			$data		=	$this->_search_query['data'];
			$limit		=	(int)$this->_search_query['limit'];
			$match		=	$this->_search_query['match'];
			$match_each	=	$this->_search_query['match_each'];
			$order		=	$this->_search_query['order'];
		} else {
			$limit		=	0;
			$match		=	array();
			$match_each	=	array();
		}

		// Where @ data
		foreach ( $data as $k=>$v ) {
			$indexes	=	explode( '|', $k );
			$operator	=	isset( $match[$k] ) ? $match[$k] : '';
			$where		=	array();

			if ( isset( $match_each[$k] ) && ( strpos( $v, ' ' ) !== false ) ) {
				$behavior	=	'AND';
				$values		=	explode( ' ', $v );

				foreach ( $values as $value ) {
					$w	=	array();

					foreach ( $indexes as $k ) {
						$index		=	$this->_getSearchQueryIndex( $query, $tables, $k );

						if ( $index === false ) {
							return false;
						} elseif ( $index === '' ) {
							continue;
						}

						$w[]		=	$this->_getSearchQueryWhere( $index, $k, $operator, $value );
					}

					$where[]	=	'((' . implode( ') OR (', $w ) . '))';
				}
			} else {
				$behavior	=	'OR';

				foreach ( $indexes as $k ) {
					$index		=	$this->_getSearchQueryIndex( $query, $tables, $k );

					if ( $index === false ) {
						return false;
					} elseif ( $index === '' ) {
						continue;
					}

					$where[]		=	$this->_getSearchQueryWhere( $index, $k, $operator, $v );
				}
			}

			if ( ( $count = count( $where ) ) === 1 ) {
				$query->where( $where[0] );
			} elseif ( $count > 1 ) {
				$query->where( '((' . implode( ') '.$behavior.' (', $where ) . '))' );
			}
		}

		// Where @ relationship
		if ( $this->_search_relationship ) {
			$this->_getSearchQueryJoin( $query, $tables );
		}

		// Order
		$isOrdered	=	false;

		if ( is_array( $order ) ) {
			foreach ( $order as $k=>$v ) {
				$k2	=	'';

				if ( strpos( $k, '.' ) !== false ) {
					$parts	=	explode( '.', $k );
					$k		=	$parts[0];

					if ( isset( $parts[1] ) && $parts[1] ) {
						$k2	=	$parts[1];
					}
				}
				$index	=	$this->_getSearchQueryIndex( $query, $tables, $k );

				if ( $index === false ) {
					return false;
				} elseif ( $index === '' ) {
					continue;
				}

				if ( $k2 ) {
					$query->order( 'JSON_EXTRACT('.$db->quoteName( $index.'.'.$k ) . ', '.$db->quote( '$."'.$k2.'"' ).') ' . strtoupper( trim( $v ) ) );
				} else {
					$query->order( $db->quoteName( $index.'.'.$k ) . ' ' . strtoupper( trim( $v ) ) );
				}

				$isOrdered	=	true;
			}
		}
		if ( !$isOrdered && !is_bool( $order ) ) {
			$query->order( $db->quoteName( 'b.'.self::$objects[$this->_object]['properties']['key'] ) . ' DESC' );
		}
		if ( $limit ) {
			$query->setLimit( $limit );
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

	// _getSearchQueryJoin
	protected function _getSearchQueryJoin( &$query, &$tables )
	{
		$db	=	JFactory::getDbo();

		foreach ( $this->_search_relationship as $k=>$relationship ) {
			$this->_relationship_nav	=	$relationship['name'];

			$table_name	=	$this->_getRelationshipDefinition( 'table' );

			if ( !isset( $tables[$table_name] ) ) {
				$tables[$table_name]	=	'j'.$k;

				$query->join( 'left', $db->quoteName( $table_name, $tables[$table_name] ).' ON '.$db->quoteName( $tables[$table_name].'.id' ).' = '.$db->quoteName( 'a.pk' ) );
				$query->group( 'a.pk' ); // TODO: make sure we have it only one time
			}

			if ( is_string( $relationship['data'] ) ) {
				if ( strpos( $relationship['data'], 'false' ) !== false ) {
					$where	=	array(
									0=>$this->_getSearchQueryWhere( $tables[$table_name], 'id2', 'in', str_replace( array( '|false', 'false|' ), '', $relationship['data'] ) ),
									1=>$this->_getSearchQueryWhere( $tables[$table_name], 'id', 'is_null' )
								);

					$query->where( '((' . implode( ') OR (', $where ) . '))' );
				} else {
					$query->where( $this->_getSearchQueryWhere( $tables[$table_name], 'id2', 'in', $relationship['data'] ) );
				}
			} elseif ( $relationship['data'] === true ) {
				$query->where( $this->_getSearchQueryWhere( $tables[$table_name], 'id', 'is_not_null' ) );
			} elseif ( $relationship['data'] === false ) {
				$query->where( $this->_getSearchQueryWhere( $tables[$table_name], 'id', 'is_null' ) );
			}
			
		}
	}

	// _getSearchQueryWhere
	protected function _getSearchQueryWhere( $index, $k, $operator, $v = null )
	{
		$db		=	JFactory::getDbo();
		$where	=	'';
		$wrap	=	false;

		switch ( $operator ) {
			case '<':
			case '<=':
			case '!=':
			case '>=':
			case '>':
				$where	=	' ' . $operator . ' ' . $db->quote( $v );
				break;
			case 'between':
			case 'between<':
			case '>between':
			case '>between<':
				$last	=	strlen( $operator ) - 1;
				$x		=	$operator[0] == '>' ? '>' : '>=';
				$y		=	$operator[$last] == '<' ? '<' : '<=';

				if ( strpos( $v, '|' ) !== false ) {
					$parts	=	explode( '|', $v );
					$where	=	' '.$x.' '.$db->quote( $parts[0] ).' AND '.$db->quoteName( $index.'.'.$k ) .' '.$y.' '. $db->quote( $parts[1] );
				} else {
					$parts	=	explode( ',', $v );
					$where	=	' '.$x.' '.$parts[0].' AND '.$db->quoteName( $index.'.'.$k ) .' '.$y.' '. $parts[1];
				}
				break;
			case 'empty':
				$where	=	' = ""';
				break;
			case 'in':
				if ( strpos( $v, '|' ) !== false ) {
					$parts	=	explode( '|', $v );
					$where	=	' IN ("' .implode( '","', $parts ). '")';
				} else {
					$where	=	' IN (' .$v. ')';
				}
				break;
			case 'is_null':
				$where	=	' IS NULL';
				break;
			case 'is_not_null':
				$where	=	' IS NOT NULL';
				break;
			case 'like%':
			case 'alpha':
				$where	=	' LIKE ' . $db->quote( $db->escape( $v, true ).'%', false );
				break;
			case 'like':
				$where	=	' LIKE ' . $db->quote( '%'.$db->escape( $v, true ).'%', false );
				break;
			case 'likes':
				$where	=	array();
				$values	=	explode( ' ', $v );

				foreach ( $values as $value ) {
					if ( strlen( $value ) > 0 ) {
						$where[] 	=	$db->quoteName( $index.'.'.$k ).' LIKE '.$db->quote( '%'.$db->escape( $value, true ).'%', false );
					}
				}
				break;
			case '%like':
			case 'zeta':
				$where	=	' LIKE ' . $db->quote( '%'.$db->escape( $v, true ), false );
				break;
			case 'not_like':
				$where	=	' NOT LIKE ' . $db->quote( '%'.$db->escape( $v, true ).'%', false );
				break;
			case 'up_since':
				$where		=	array();
				$where[] 	=	'( '.$db->quoteName( $index.'.'.$k ).' = '.$db->quote( $db->getNullDate() ).' OR '.$db->quoteName( $index.'.'.$k ).' <= '.$db->quote( $v ).' )';
				break;
			case 'up_until':
				$where		=	array();
				$where[] 	=	'( '.$db->quoteName( $index.'.'.$k ).' = '.$db->quote( $db->getNullDate() ).' OR '.$db->quoteName( $index.'.'.$k ).' >= '.$db->quote( $v ).' )';
				break;
			case 'within':
				$glue	=	',';
				$values	=	explode( ',', $v );
				$where	=	array();
				
				foreach ( $values as $value ) {
					if ( strlen( $value ) > 0 ) {
						$where[] 	=	$db->quoteName( $index.'.'.$k ).' = '.$db->quote( $value )
									.	' OR '.$db->quoteName( $index.'.'.$k ).' LIKE '.$db->quote( $db->escape( $value, true ).$glue.'%', false )
									.	' OR '.$db->quoteName( $index.'.'.$k ).' LIKE '.$db->quote( '%'.$glue.$db->escape( $value, true ).$glue.'%', false )
									.	' OR '.$db->quoteName( $index.'.'.$k ).' LIKE '.$db->quote( '%'.$glue.$db->escape( $value, true ), false );
						$wrap		=	true;
					}
				}
				break;
			case '=':
			default:
				$where	=	' = ' . $db->quote( $v );
				break;
		}

		if ( is_array( $where ) ) {
			if ( count( $where ) === 1 && $wrap === false ) {
				$where	=	implode( '', $where );
			} else{
				$where	=	'((' . implode( ') OR (', $where ) . '))';
			}
		} else {
			$where	=	$db->quoteName( $index.'.'.$k ) . $where;
		}

		return $where;
	}

	// _setContentById
	protected function _setContentById( $identifier )
	{
		$query	=	'SELECT a.id AS id, a.cck AS cck, a.pk AS pk, a.storage_location as storage_location, a.storage_table as storage_table, b.id AS type_id, b.parent AS parent, b.permissions AS permissions, b.relationships AS relationships'
				.	' FROM #__cck_core AS a'
				.	' JOIN #__cck_core_types AS b ON b.name = a.cck';

		if ( !is_array( $identifier ) && ( $classname = substr( strtolower( get_called_class() ), 11 ) ) != '' ) {
			$identifier		=	array( 0=>$classname, 1=>$identifier );
		}
		if ( is_array( $identifier ) ) {
			if( !isset( $identifier[1] ) ) {
				return false;
			}

			$and					=	( (string)$identifier[0] == 'free' ) ? ' AND storage_table = "'.$this->_table.'"' : '';
			$core					=	JCckDatabase::loadObject( $query.' WHERE a.storage_location = "'.(string)$identifier[0].'"'.$and.' AND a.pk = '.(int)$identifier[1] );

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
		} elseif ( !$this->_table && $core->storage_table ) {
			$this->_table			=	$core->storage_table;
		}

		if ( !$this->_table ) {
			$this->reset( true );

			return false;
		}

		$settings					=	json_decode( $core->relationships, true );
		$this->_id					=	$core->id;
		$this->_pk					=	$core->pk;
		$this->_type				=	$core->cck;
		$this->_type_id				=	$core->type_id;
		$this->_type_parent			=	$core->parent;
		$this->_type_permissions	=	$core->permissions;

		if ( isset( $settings['properties'] ) ) {
			$this->_type_properties	=	$settings['properties'];
		}
		if ( isset( $settings['relationships'] ) ) {
			$this->_type_relationships	=	$settings['relationships'];
		} else {
			$this->_type_relationships	=	$settings;
		}

		$this->_setTypeMap();

		return true;
	}

	// _setContentByType
	protected function _setContentByType( $content_type )
	{
		if ( strpos( $content_type, '|' ) !== false ) {
			$parts			=	explode( '|', $content_type );
			$content_type	=	$parts[0];
		}
		$this->_type	=	$content_type;

		if ( !$this->_object || !$this->_table ) {
			$type		=	JCckDatabaseCache::loadObject( 'SELECT id, storage_location, parent, permissions, relationships FROM #__cck_core_types WHERE name = "'.$this->_type.'"' );
			
			if ( !is_object( $type ) ) {
				$this->log( 'error', 'Content Type not found.' );

				return false;
			}

			$this->_object		=	$type->storage_location;

			if ( !$this->_object || $this->_object == 'none' ) {
				return false;
			}

			$this->_setObjectMap();

			if ( self::$objects[$this->_object]['properties']['table'] != '' ) {
				$this->_table	=	self::$objects[$this->_object]['properties']['table'];	
			} else {
				if ( $this->_table == '' ) {
					$this->_table	=	'#__cck_store_form_'.( $type->parent ? $type->parent : $content_type );
				}
			}
		} else {
			$type		=	JCckDatabaseCache::loadObject( 'SELECT id, parent, permissions, relationships FROM #__cck_core_types WHERE name = "'.$this->_type.'"' );

			if ( !is_object( $type ) ) {
				$this->log( 'error', 'Content Type not found.' );

				return false;
			}
		}

		$settings					=	json_decode( $type->relationships, true );
		$this->_type_id				=	$type->id;
		$this->_type_parent			=	$type->parent;
		$this->_type_permissions	=	$type->permissions;

		if ( isset( $settings['properties'] ) ) {
			$this->_type_properties	=	$settings['properties'];
		}
		if ( isset( $settings['relationships'] ) ) {
			$this->_type_relationships	=	$settings['relationships'];
		} else {
			$this->_type_relationships	=	$settings;
		}

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

	// _setCallable
	protected function _setCallable( $name, $callable, $scope, $scope_target = '' )
	{
		if ( $scope == 'object' ) {
			self::$objects[($scope_target ? $scope_target : $this->_object)]['callables'][$name]	=	$callable;
		} elseif ( $scope == 'type' ) {
			self::$types[($scope_target ? $scope_target : $this->_type)]['callables'][$name]		=	$callable;
		} elseif ( $scope == 'global' ) {
			self::$callables[$name]		=	$callable;
		} else {
			$this->_callables[$name]	=	$callable;
		}

		self::$callables_map[$name]	=	$scope;
	}

	// _setMixin
	protected function _setMixin( $mixin, $scope, $scope_target = '' )
	{
		$methods	=	(new ReflectionClass( $mixin ) )->getMethods( ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED );

		foreach ( $methods as $method ) {
			$method->setAccessible( true );

			$this->_setCallable( $method->name, $method->invoke( $mixin ), $scope, $scope_target );
		}
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
										'callables'=>array(),
										'columns'=>array(),
										'properties'=>JCck::callFunc( 'plgCCK_Storage_Location'.$object, 'getStaticProperties', $properties )
									);

		if ( !( isset( self::$objects[$object]['properties']['context2'] ) && self::$objects[$object]['properties']['context2'] != '' ) ) {
			self::$objects[$object]['properties']['context2']	=	self::$objects[$object]['properties']['context'];
		}

		// Core Relationships

		// self::$objects[$object]['relations']	=	array( 'one'=>array(), 'many'=>array() );

		// if ( $columns['author_object'] ) {
		// 	$this->setRelation( 'author', $columns['author_object'], $columns['author'], $object );
		// }

		// if ( $columns['parent_object'] ) {
		// 	$this->setRelation( 'parent', $columns['parent_object'], $columns['parent'], $object );
		// }

		// if ( $columns['child_object'] ) {
		// 	$this->_setObjectMap( $columns['child_object'] );
		// }

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
											'callables'=>array(),
											'data_map'=>array()
										);

		// More Relationships
		if ( $this->_type_relationships ) {
			foreach ( $this->_type_relationships as $relationship ) {
				if ( isset( $relationship['mode'] ) && isset( $relationship['name'] ) ) {
					$this->setRelationship( $relationship['mode'], $relationship['name'], $relationship['params'] );
				}
			}
		}

		return true;
	}
	
	// _triggerEvent
	protected function _triggerEvent( $event, array $args = null )
	{
		// Safe call for now
		try {
			JFactory::getApplication()->triggerEvent( $event, $args );
		} catch ( Exception $e ) {
			// Do Nothing
		}		
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

// JCckContentUnknown
// class JCckContentUnknown
// {
// 	protected $_error	=	true;
// }
?>