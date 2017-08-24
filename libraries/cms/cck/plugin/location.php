<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: location.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class JCckPluginLocation extends JPlugin
{
	protected static $construction	=	'cck_storage_location';

	// __construct
	public function __construct( &$subject, $config = array() )
	{
		parent::__construct( $subject, $config );
		
		JLoader::register( 'JCckContent'.static::$type, JPATH_SITE.'/plugins/cck_storage_location/'.static::$type.'/classes/content.php' );
	}
	
	// access
	public static function access( $pk, $checkAccess = true )
	{
		return true;
	}

	// authorise
	public static function authorise( $rule, $pk )
	{
		return true;
	}

	// getStaticParams
	public static function getStaticParams()
	{
		static $params	=	null;
		
		if ( !is_object( $params ) ) {
			$plg		=	JPluginHelper::getPlugin( 'cck_storage_location', static::$type );
			$params		=	new JRegistry( $plg->params );
		}
		
		return $params;
	}

	// getStaticProperties
	public static function getStaticProperties( $properties )
	{
		static $autorized	=	array(
									'access'=>'',
									'author'=>'',
									'author_object'=>'',
									'bridge_object'=>'',
									'child_object'=>'',
									'created_at'=>'',
									'context'=>'',
									'contexts'=>'',
									'custom'=>'',
									'events'=>'',
									'key'=>'',
									'modified_at'=>'',
									'ordering'=>'',
									'parent'=>'',
									'parent_object'=>'',
									'routes'=>'',
									'status'=>'',
									'table'=>'',
									'table_object'=>'',
									'to_route'=>''
								);
		
		if ( count( $properties ) ) {
			foreach ( $properties as $i=>$p ) {
				if ( isset( $autorized[$p] ) ) {
					$properties[$p]	=	static::${$p};
				}
				unset( $properties[$i] );
			}
		}
		
		return $properties;
	}

	// onCCK_Storage_LocationPrepareDelete
	public function onCCK_Storage_LocationPrepareDelete( &$field, &$storage, $pk = 0, &$config = array() )
	{
		if ( static::$type != $field->storage_location ) {
			return;
		}
		
		// Init
		$table	=	$field->storage_table;
		
		// Set
		if ( $table == static::$table ) {
			$storage	=	static::_getTable( $pk );
		} else {
			$storage	=	static::g_onCCK_Storage_LocationPrepareForm( $table, $pk );
		}
	}

	// onCCK_Storage_LocationSaveOrder
	public static function onCCK_Storage_LocationSaveOrder( $pks = array(), $order = array() )
	{
		$table			=	static::_getTable();
		$tableClassName	=	get_class( $table );
		$contentType	=	new JUcmType;
		$type			=	$contentType->getTypeByTable( $tableClassName );
		$tagsObserver	=	$table->getObserverOfClass( 'JTableObserverTags' );
		$conditions		=	array();
		
		if ( empty( $pks ) ) {
			return;
		}

		foreach ( $pks as $i=>$pk ) {
			$table->load( (int)$pk );
			/*
			if ( !$this->canEditState( $table ) ) {
				unset( $pks[$i] );
			} else*/if ( $table->ordering != $order[$i] ) {
				$table->ordering	=	$order[$i];

				if ( $type ) {
					if (!empty( $tagsObserver ) && !empty( $type ) ) {
						$table->tagsHelper				=	new JHelperTags;
						$table->tagsHelper->typeAlias	=	$type->type_alias;
						$table->tagsHelper->tags		=	explode( ',', $table->tagsHelper->getTagIds( $pk, $type->type_alias ) );
					}
				}
				if ( !$table->store() ) {
					JFactory::getApplication()->enqueueMessage( $table->getError(), 'error' );

					return false;
				}
				
				// Remember to reorder within position and client_id
				$condition	=	static::_getReorderConditions( $table );
				$found		=	false;
				
				foreach ( $conditions as $cond ) {
					if ( $cond[1] == $condition ) {
						$found	=	true;
						break;
					}
				}
				if ( !$found ) {
					$key			=	$table->getKeyName();
					$conditions[]	=	array( $table->$key, $condition );
				}
			}
		}

		// Execute reorder for each condition
		foreach ( $conditions as $cond ) {
			$table->load( $cond[0] );
			$table->reorder( $cond[1] );
		}

		return true;
	}

	// onCCK_Storage_LocationStore
	public function onCCK_Storage_LocationStore( $type, $data, &$config = array(), $pk = 0 )
	{
		if ( static::$type != $type ) {
			return;
		}
		
		if ( isset( $config['primary'] ) && $config['primary'] != static::$type ) {
			return;
		}
		if ( ! @$config['storages'][static::$table]['_']->pk ) {
			if ( isset( $config['storages'][static::$table] )
			  && $config['storages'][static::$table]['_']->table == static::$table && isset( $config['storages'][static::$table][static::$key] ) ) {
				unset( $config['storages'][static::$table][static::$key] );
			}
			static::_core( $config['storages'][static::$table], $config, $pk );
			$config['storages'][static::$table]['_']->pk	=	static::$pk;
		}
		if ( $data['_']->table != static::$table ) {
			static::g_onCCK_Storage_LocationStore( $data, static::$table, static::$pk, $config );
		}
		
		return static::$pk;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// g_onCCK_Storage_LocationPrepareContent
	public function g_onCCK_Storage_LocationPrepareContent( $table, $pk )
	{
		$instance	=	JCckTable::getInstance( $table, 'id' );
		
		if ( $pk > 0 ) {
			$instance->load( $pk );
		}

		return $instance;
	}

	// g_onCCK_Storage_LocationPrepareForm
	public function g_onCCK_Storage_LocationPrepareForm( $table, $pk )
	{
		$instance	=	JCckTable::getInstance( $table, 'id' );
		
		if ( $pk > 0 ) {
			$instance->load( $pk );
		}

		return $instance;
	}

	// g_onCCK_Storage_LocationPrepareStore
	public static function g_onCCK_Storage_LocationPrepareStore( &$config = array() )
	{
		$core		=	JCckTable::getInstance( '#__cck_core', 'id' );
		$core->cck	=	' ';
		$core->storeIt();
		
		return $core->id;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// g_onCCK_Storage_LocationRollback
	public static function g_onCCK_Storage_LocationRollback( $pk )
	{
		JCckDatabase::execute( 'DELETE FROM #__cck_core WHERE id = '.(int)$pk );
	}

	// g_onCCK_Storage_LocationStore
	public static function g_onCCK_Storage_LocationStore( $location, $default, $pk, &$config )
	{		
		if ( ! $pk ) {
			return;
		}
		
		static $already		=	0;
		$config['author']	=	( (int)$config['author'] > 0 ) ? $config['author'] : JCck::getConfig_Param( 'integration_user_default_author', 42 );
		$config['parent']	=	( isset( $config['parent'] ) && (int)$config['parent'] > 0 ) ? $config['parent'] : 0;
		$table				=	$location['_']->table;

		// Core
		if ( !$already ) {
			if ( static::$bridge_object != '' ) {
				$params		=	static::getStaticParams()->toArray();
			}
			if ( isset( $params['bridge'] ) && $params['bridge'] ) {
				if ( !isset( $params['bridge_default_title'] ) ) {
					$params['bridge_default_title']			=	'';
				}
				if ( !isset( $params['bridge_default_title_mode'] ) ) {
					$params['bridge_default_title_mode']	=	0;
				}
				if ( $params['bridge'] == 1 ) {
					self::g_doBridge( 'joomla_article', $pk, $location, $config, $params );
				} elseif ( $params['bridge'] == 2 ) {
					self::g_doBridge( 'joomla_category', $pk, $location, $config, $params );
				}
			} else {
				$core					=	JCckTable::getInstance( '#__cck_core', 'id' );
				$core->load( $config['id'] );
				$core->cck				=	$config['type'];
				if ( ! $core->pk ) {
					$core->date_time	=	JFactory::getDate()->toSql();
				}
				$core->pk				=	$pk;
				$core->storage_location	=	( isset( $location['_']->location ) ) ? $location['_']->location : JCckDatabase::loadResult( 'SELECT storage_location FROM #__cck_core_types WHERE name = "'.$config['type'].'"' );
				$core->author_id		=	$config['author'];
				$core->parent_id		=	$config['parent'];
				if ( isset( $config['storages']['#__cck_core']['store_id'] ) ) {
					$core->store_id		=	$config['storages']['#__cck_core']['store_id'];
				}
				$core->storeIt();
			}
			if ( !( isset( $config['component'] ) && $config['component'] == 'com_cck_importer' ) ) {
				$already	=	1;
			}
		}

		// More
		if ( $table && $table != $default && $table != 'none' ) {
			$more	=	JCckTable::getInstance( $table, 'id' );
			$more->load( $pk, true );
			if ( isset( $more->cck ) ) {
				$more->cck	=	$config['type'];
			}
			$more->bind( $config['storages'][$table] );
			$more->check();
			$more->store();	
		}
		
		if ( ! isset( $config['primary'] ) ) {
			$config['primary']	=	$location['_']->location;
		}
	}
	
	// g_onCCK_Storage_LocationUpdate
	public function g_onCCK_Storage_LocationUpdate( $pk, $table, $field, $search, $replace, &$config = array() )
	{
		if ( ! $pk ) {
			return;
		}
		$update			=	JCckTable::getInstance( $table, 'id', $pk );
		$update->$field	=	str_replace( $search, $replace, $update->$field );
		$update->store();	
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// g_checkIn
	public static function g_checkIn( $table )
	{
		$app	=	JFactory::getApplication();
		$user	=	JFactory::getUser();
		
		if ( $table->checked_out > 0 ) {
			if ( $table->checked_out != $user->id && !$user->authorise( 'core.admin', 'com_checkin' ) ) {
				$app->enqueueMessage( JText::_( 'JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH' ), 'error' );
				return false;
			}
			
			if ( !$table->checkin() ) {
				$app->enqueueMessage( $table->getError(), 'error' );
				return false;
			}
		}
		
		// releaseEditId
		
		return true;
	}
	
	// g_isMax
	public function g_isMax( $author_id, $parent_id, $config = array() )
	{
		$app	=	JFactory::getApplication();
		$user	=	JFactory::getUser();
		$typeId	=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_core_types WHERE name ="'.$config['type'].'"' );
		
		jimport('cck.joomla.access.access');
		$max_parent_author	=	(int)CCKAccess::check( $user->id, 'core.create.max.parent.author', 'com_cck.form.'.$typeId );
		$max_parent			=	(int)CCKAccess::check( $user->id, 'core.create.max.parent', 'com_cck.form.'.$typeId );
		$max_author			=	(int)CCKAccess::check( $user->id, 'core.create.max.author', 'com_cck.form.'.$typeId );
		
		if ( $max_parent_author > 0 ) {
			$count	=	JCckDatabase::loadResult( 'SELECT COUNT(id) FROM #__cck_core WHERE cck="'.$config['type'].'" AND parent_id = '.$parent_id.' AND author_id = '.$author_id );
			if ( $count >= $max_parent_author ) {
				JCckDatabase::execute( 'DELETE FROM #__cck_core WHERE id = '.(int)$config['id'] );
				$app->enqueueMessage( JText::_( 'COM_CCK_ERROR_MAX_PARENT_AUTHOR' ), 'error' );
				$config['error']	=	true;
				return 1;
			}
		}
		if ( $max_parent > 0 ) {
			$count	=	JCckDatabase::loadResult( 'SELECT COUNT(id) FROM #__cck_core WHERE cck="'.$config['type'].'" AND parent_id = '.$parent_id );
			if ( $count >= $max_parent ) {
				JCckDatabase::execute( 'DELETE FROM #__cck_core WHERE id = '.(int)$config['id'] );
				$app->enqueueMessage( JText::_( 'COM_CCK_ERROR_MAX_PARENT' ), 'error' );
				$config['error']	=	true;
				return 1;
			}
		}
		if ( $max_author > 0 ) {
			$count	=	JCckDatabase::loadResult( 'SELECT COUNT(id) FROM #__cck_core WHERE cck="'.$config['type'].'" AND author_id = '.$author_id );
			if ( $count >= $max_author ) {
				JCckDatabase::execute( 'DELETE FROM #__cck_core WHERE id = '.(int)$config['id'] );
				$app->enqueueMessage( JText::_( 'COM_CCK_ERROR_MAX_AUTHOR' ), 'error' );
				$config['error']	=	true;
				return 1;
			}
		}
		
		return 0;
	}
	
	// g_doBridge
	public function g_doBridge( $type, $pk, $location, &$config, $params )
	{
		// Todo: move to plug-in
		if ( $type == 'joomla_category' ) {
			$core	=	JCckTable::getInstance( '#__cck_core', 'id' );
			$core->load( $config['id'] );
			
			JLoader::register( 'JTableCategory', JPATH_PLATFORM.'/joomla/database/table/category.php' );
			$bridge		=	JTable::getInstance( 'Category' );
			$dispatcher	=	JEventDispatcher::getInstance();
			
			if ( $core->pkb > 0 ) {
				$bridge->load( $core->pkb );
				$bridge->description	=	'';
				$isNew					=	false;
			} else {
				$bridge->access			=	'';
				$bridge->published		=	'';
				self::g_initTable( $bridge, $params, false, 'bridge_' );
				if ( ! isset( $config['storages']['#__categories']['parent_id'] ) ) {
					$config['storages']['#__categories']['parent_id']	=	$params['bridge_default-parent_id'];
				}
				$isNew					=	true;
			}
			$bridge->created_user_id	=	$config['author'];

			if ( $bridge->parent_id != $config['storages']['#__categories']['parent_id'] || $config['storages']['#__categories']['id'] == 0 ) {
				$bridge->setLocation( $config['storages']['#__categories']['parent_id'], 'last-child' );
			}
			if ( isset( $config['storages']['#__categories'] ) ) {
				$bridge->bind( $config['storages']['#__categories'] );
			}
			if ( $params['bridge_default_title_mode'] && $params['bridge_default_title'] != '' ) {
				$title	=	$params['bridge_default_title'];
				$title	=	str_replace( '[pk]', $pk, $title );

				if ( strpos( $params['bridge_default_title'], '#' ) !== false ) {
					$matches	=	array();
					preg_match_all( '#\#([a-zA-Z0-9_]*)\##U', $params['bridge_default_title'], $matches );
					if ( count( $matches[1] ) ) {
						$fieldnames	=	'"'.implode( '","', $matches[1] ).'"';
						$fields		=	JCckDatabase::loadObjectList( 'SELECT name, storage, storage_table, storage_field FROM #__cck_core_fields WHERE name IN ('.$fieldnames.') AND storage_field2 = ""', 'name' );
						foreach ( $matches[1] as $match ) {
							$value	=	'';
							if ( isset( $fields[$match] ) ) {
								if ( isset( $config['storages'][$fields[$match]->storage_table][$fields[$match]->storage_field] ) ) {
									$value	=	$config['storages'][$fields[$match]->storage_table][$fields[$match]->storage_field];
								}
							}
							$title	=	str_replace( '#'.$match.'#', $value, $title );
						}
						$bridge->title	=	trim( $title );
					}
				} else {
					$bridge->title		=	trim( $title );
				}
			}
			if ( ! $bridge->title ) {
				$bridge->title	=	ucwords( str_replace( '_', ' ', $location['_']->location ) ).' - '.$pk;
			}
			if ( ! $bridge->parent_id ) {
				$bridge->parent_id	=	2;
			}
			$bridge->description	=	'::cck::'.$config['id'].'::/cck::'.$bridge->description;
			
			if ( !$core->pkb ) {
				// setLocation, etc..				
			}
			$bridge->check();
			$bridge->extension		=	'com_content';
			if ( $bridge->parent_id > 1 ) {
				$bridgeParent		=	JTable::getInstance( 'Category' );
				$bridgeParent->load( $bridge->parent_id );
				$bridge->path		=	$bridgeParent->path.'/';
			} else {
				$bridge->path		=	'';
			}
			$bridge->path			.=	$bridge->alias;
			if ( empty( $bridge->language ) ) {
				$bridge->language	=	'*';
			}
			JPluginHelper::importPlugin( 'content' );
			$dispatcher->trigger( 'onContentBeforeSave', array( 'com_categories.category', &$bridge, $isNew ) );
			if ( !$bridge->store() ) {
				if ( $isNew ) {
					$test	=	JTable::getInstance( 'Category' );
					for ( $i = 2; $i < 69; $i++ ) {
						$alias	=	$bridge->alias.'-'.$i;
						if ( !$test->load( array( 'alias'=>$alias, 'parent_id'=>$bridge->parent_id, 'extension'=>$bridge->extension ) ) ) {
							$bridge->alias	=	$alias;
							$bridge->store();
							break;
						}
					}
				}
			}
			
			$config['author']		=	( $config['author'] == 0 ) ? $bridge->created_user_id : $config['author'];
			$config['parent_id']	=	( $config['parent_id'] == 0 ) ? $bridge->parent_id : $config['parent_id'];
			
			$core->pkb	=	( $bridge->id > 0 ) ? $bridge->id : 0;
			$core->cck	=	$config['type'];
			if ( ! $core->pk ) {
				$core->author_id	=	$config['author'];
				$core->date_time	=	JFactory::getDate()->toSql();
			}
			$core->pk	=	$pk;
			$core->storage_location	=	$location['_']->location;
			$core->author_id		=	$config['author'];
			$core->parent_id		=	$config['parent'];
			$core->storeIt();
			
			$dispatcher->trigger( 'onContentAfterSave', array( 'com_categories.category', &$bridge, $isNew ) );
		} else {
			if ( !isset( $params['bridge_ordering'] ) ) {
				$params['bridge_ordering']	=	1;
			} else {
				$params['bridge_ordering']	=	(int)$params['bridge_ordering'];
			}
			$core	=	JCckTable::getInstance( '#__cck_core', 'id' );
			$core->load( $config['id'] );
			
			JLoader::register( 'JTableContent', JPATH_PLATFORM.'/joomla/database/table/content.php' );
			$bridge		=	JTable::getInstance( 'Content' );
			$dispatcher	=	JEventDispatcher::getInstance();
			
			if ( $core->pkb > 0 ) {
				$bridge->load( $core->pkb );
				$bridge->introtext	=	'';
				$isNew				=	false;
			} else {
				$bridge->access		=	'';
				$bridge->state		=	'';
				self::g_initTable( $bridge, $params, false, 'bridge_' );
				$isNew				=	true;
			}
			$bridge->created_by		=	$config['author'];

			if ( isset( $config['storages']['#__content'] ) ) {
				$bridge->bind( $config['storages']['#__content'] );
			}
			if ( $params['bridge_default_title_mode'] && $params['bridge_default_title'] != '' ) {
				$title	=	$params['bridge_default_title'];
				$title	=	str_replace( '[pk]', $pk, $title );

				if ( strpos( $params['bridge_default_title'], '#' ) !== false ) {
					$matches	=	array();

					preg_match_all( '#\#([a-zA-Z0-9_]*)\##U', $params['bridge_default_title'], $matches );

					if ( count( $matches[1] ) ) {
						$fieldnames	=	'"'.implode( '","', $matches[1] ).'"';
						$fields		=	JCckDatabase::loadObjectList( 'SELECT name, storage, storage_table, storage_field FROM #__cck_core_fields WHERE name IN ('.$fieldnames.') AND storage_field2 = ""', 'name' );
						foreach ( $matches[1] as $match ) {
							$value	=	'';
							if ( isset( $fields[$match] ) ) {
								if ( isset( $config['storages'][$fields[$match]->storage_table][$fields[$match]->storage_field] ) ) {
									$value	=	$config['storages'][$fields[$match]->storage_table][$fields[$match]->storage_field];
								}
							}
							$title	=	str_replace( '#'.$match.'#', $value, $title );
						}
						$bridge->title	=	trim( $title );
					}
				} else {
					$bridge->title		=	trim( $title );
				}
			}
			if ( ! $bridge->title ) {
				$bridge->title	=	ucwords( str_replace( '_', ' ', $location['_']->location ) ).' - '.$pk;
			}
			if ( ! $bridge->catid ) {
				$bridge->catid	=	2;
			}
			$bridge->introtext	=	'::cck::'.$config['id'].'::/cck::'.$bridge->introtext;
			$bridge->version++;
			
			if ( $bridge->state == 1 && intval( $bridge->publish_up ) == 0 ) {
				$bridge->publish_up	=	substr( JFactory::getDate()->toSql(), 0, -3 );
			}
			if ( !$core->pkb ) {
				if ( $params['bridge_ordering'] ) {
					$max				=	JCckDatabase::loadResult( 'SELECT MAX(ordering) FROM #__content WHERE catid = '.(int)$bridge->catid );
					$bridge->ordering	=	(int)$max + 1;
				} else {
					$bridge->reorder( 'catid = '.(int)$bridge->catid.' AND state >= 0' );
				}
			}
			$bridge->check();

			if ( empty( $bridge->language ) ) {
				$bridge->language	=	'*';
			}
			
			JPluginHelper::importPlugin( 'content' );
			$dispatcher->trigger( 'onContentBeforeSave', array( 'com_content.article', &$bridge, $isNew ) );
			if ( !$bridge->store() ) {
				if ( $isNew ) {
					$test	=	JTable::getInstance( 'Content' );
					for ( $i = 2; $i < 69; $i++ ) {
						$alias	=	$bridge->alias.'-'.$i;
						if ( !$test->load( array( 'alias'=>$alias, 'catid'=>$bridge->catid ) ) ) {
							$bridge->alias	=	$alias;
							$bridge->store();
							break;
						}
					}
				}
			}
			
			$config['author']		=	( $config['author'] == 0 ) ? $bridge->created_by : $config['author'];
			$config['parent_id']	=	( $config['parent_id'] == 0 ) ? $bridge->catid : $config['parent_id'];
			
			$core->pkb	=	( $bridge->id > 0 ) ? $bridge->id : 0;
			$core->cck	=	$config['type'];
			if ( ! $core->pk ) {
				$core->author_id	=	$config['author'];
				$core->date_time	=	JFactory::getDate()->toSql();
			}
			$core->pk	=	$pk;
			$core->storage_location	=	$location['_']->location;
			$core->author_id		=	$config['author'];
			$core->parent_id		=	$config['parent'];
			$core->storeIt();
			
			$dispatcher->trigger( 'onContentAfterSave', array( 'com_content.article', &$bridge, $isNew ) );
		}
	}
	
	// g_getBridgeAuthor
	public function g_getBridgeAuthor( $type, $pk, $location )
	{
		// Todo: move to plug-in
		if ( $type == 'joomla_category' ) {
			$author_id	=	JCckDatabase::loadResult( 'SELECT b.created_user_id FROM #__cck_core AS a LEFT JOIN #__categories AS b ON b.id = a.pkb WHERE a.storage_location = "'.$location.'" AND a.pk = '.$pk );
		} else {
			$author_id	=	JCckDatabase::loadResult( 'SELECT b.created_by FROM #__cck_core AS a LEFT JOIN #__content AS b ON b.id = a.pkb WHERE a.storage_location = "'.$location.'" AND a.pk = '.$pk );
		}
		if ( !$author_id ) {
			$author_id	=	JCckDatabase::loadResult( 'SELECT a.author_id FROM #__cck_core AS a WHERE a.storage_location = "'.$location.'" AND a.pk = '.$pk ); // todo: a recuperer
		}

		return $author_id;
	}
	
	// g_initTable
	public function g_initTable( &$table, $params = array(), $force = false, $prefix = 'base_' )
	{
		if ( count( $params ) ) {
			if ( $force === true ) {
				foreach ( $params as $k => $v ) {
					if ( ( $pos = strpos( $k, $prefix.'default-' ) ) !== false ) {
						$length		=	strlen( $prefix ) + 8;
						$k			=	substr( $k, $length );
						$table->$k	=	$v;
					}
				}
			} else {				
				foreach ( $params as $k => $v ) {
					if ( ( $pos = strpos( $k, $prefix.'default-' ) ) !== false ) {
						$length	=	strlen( $prefix ) + 8;
						$k		=	substr( $k, $length );
						if ( $table->$k == '' || !isset( $table->$k ) ) {
							$table->$k	=	$v;
						}
					}
				}
			}
		}
	}
	
	// g_completeTable
	public function g_completeTable( &$table, $custom, $config = array() )
	{
		if ( $custom ) {
			$table->$custom	=	'::cck::'.$config['id'].'::/cck::'.$table->$custom;
		}
	}
}
?>