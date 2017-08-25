<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: joomla_category.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Utilities\ArrayHelper;

JLoader::register( 'JTableCategory', JPATH_PLATFORM.'/joomla/database/table/category.php' );

// Plugin
class plgCCK_Storage_LocationJoomla_Category extends JCckPluginLocation
{
	protected static $type			=	'joomla_category';
	protected static $table			=	'#__categories';
	protected static $table_object	=	array( 'Category', 'JTable' );
	protected static $key			=	'id';
	
	protected static $access		=	'access';
	protected static $author		=	'created_user_id';
	protected static $author_object	=	'joomla_user';
	protected static $bridge_object	=	'';
	protected static $child_object	=	'joomla_article';
	protected static $created_at	=	'created_time';
	protected static $custom		=	'description';
	protected static $modified_at	=	'modified_time';
	protected static $parent		=	'parent_id';
	protected static $parent_object	=	'joomla_category';
	protected static $status		=	'published';
	protected static $to_route		=	'a.id as pk, a.title, a.alias';
	
	protected static $context		=	'com_categories.category';
	protected static $context2		=	'com_content.category';
	protected static $contexts		=	array( 'com_content.categories', 'com_content.category' );
	protected static $error			=	false;
	protected static $events		=	array(
											'afterDelete'=>'onContentAfterDelete',
											'afterSave'=>'onContentAfterSave',
											'beforeDelete'=>'onContentBeforeDelete',
											'beforeSave'=>'onContentBeforeSave'
										);
	protected static $ordering		=	array( 'alpha'=>'title ASC', 'newest'=>'created_time DESC', 'oldest'=>'created_time ASC', 'ordering'=>'lft ASC', 'popular'=>'hits DESC' );
	protected static $ordering2		=	array();
	protected static $pk			=	0;
	protected static $routes		=	array(
											0=>'categories',
											1=>'categories',
											2=>'category'
										);
	protected static $sef			=	array( '1'=>'full',
											   '2'=>'full', '22'=>'id', '23'=>'alias', '24'=>'alias',
											   '3'=>'full', '32'=>'id', '33'=>'alias',
											   '4'=>'full', '42'=>'id', '43'=>'alias'
										);
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_Storage_LocationConstruct
	public function onCCK_Storage_LocationConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		if ( empty( $data['storage_table'] ) ) {
			$data['storage_table']	=	self::$table;
		}
		$data['core_table']		=	self::$table;
		$data['core_columns']	=	array( 'associations', 'tags' );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Storage_LocationPrepareContent
	public function onCCK_Storage_LocationPrepareContent( &$field, &$storage, $pk = 0, &$config = array(), &$row = null )
	{
		if ( self::$type != $field->storage_location ) {
			return;
		}
		
		// Init
		$table	=	$field->storage_table;
		
		// Set
		if ( $table == self::$table ) {
			$storage			=	self::_getTable( $pk, true );
			$storage->slug		=	( $storage->alias ) ? $storage->id.':'.$storage->alias : $storage->id;
			$config['author']	=	$storage->{self::$author};
		} else {
			$storage			=	parent::g_onCCK_Storage_LocationPrepareContent( $table, $pk );
			if ( ! isset( $config['storages'][self::$table] ) ) {
				$config['storages'][self::$table]		=	self::_getTable( $pk, true );
				$config['storages'][self::$table]->slug	=	( $config['storages'][self::$table]->alias ) ? $config['storages'][self::$table]->id.':'.$config['storages'][self::$table]->alias
																										 : $config['storages'][self::$table]->id;
				$config['author']						=	$config['storages'][self::$table]->{self::$author};
			}
		}
	}

	// onCCK_Storage_LocationPrepareForm
	public function onCCK_Storage_LocationPrepareForm( &$field, &$storage, $pk = 0, &$config = array() )
	{
		if ( self::$type != $field->storage_location ) {
			return;
		}
		
		// Init
		$table	=	$field->storage_table;
		if ( isset( $config['primary'] ) && $config['primary'] != self::$type ) {
			$pk	=	$config['pkb'];
		}

		// Set
		if ( $table == self::$table ) {
			$storage			=	self::_getTable( $pk );
			$config['asset']	=	'com_categories';
			
			if ( $config['copyfrom_id'] ) {
				$empty						=	array( self::$key, 'alias', 'created_time', 'created_user_id', 'hits', 'modified_time', 'modified_user_id', 'version' );
				$config['language']			=	JFactory::getApplication()->input->get( 'translate' );
				$config['translate']		=	$storage->language;
				$config['copiedfrom_id']	=	$config['copyfrom_id'].':'.$storage->alias;
				foreach ( $empty as $k ) {
					$storage->$k	=	'';
				}
			} else {
				$config['asset_id']	=	(int)$storage->asset_id;
				$config['author']	=	$storage->{self::$author};
				$config['custom']	=	( ! $config['custom'] ) ? self::$custom : $config['custom'];
				$config['language']	=	$storage->language;
			}
		} else {
			$storage			=	parent::g_onCCK_Storage_LocationPrepareForm( $table, $pk );
		}
	}
	
	// onCCK_Storage_LocationPrepareItems
	public function onCCK_Storage_LocationPrepareItems( &$field, &$storages, $pks, &$config = array(), $load = false )
	{
		if ( self::$type != $field->storage_location ) {
			return;
		}
		
		// Init
		$table	=	$field->storage_table;
		
		// Prepare
		if ( $load ) {
			if ( $table == self::$table ) {
				$storages[$table]	=	JCckDatabase::loadObjectList( 'SELECT a.*, b.title AS parent_title, b.alias AS parent_alias'
																	. ' FROM '.$table.' AS a LEFT JOIN #__categories AS b ON b.id = a.parent_id'
																	. ' WHERE a.'.self::$key.' IN ('.$config['pks'].')', self::$key );
				foreach ( $storages[self::$table] as $s ) {
					$s->slug		=	( $s->alias ) ? $s->id.':'.$s->alias : $s->id;
				}
			} else {
				$storages[$table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$table.' WHERE id IN ('.$config['pks'].')', 'id' );
				if ( !isset( $storages[self::$table] ) ) {
					$storages['_']			=	self::$table;
					$storages[self::$table]	=	JCckDatabase::loadObjectList( 'SELECT a.*, b.title AS parent_title, b.alias AS parent_alias'
																			. ' FROM '.self::$table.' AS a LEFT JOIN #__categories AS b ON b.id = a.parent_id'
																			. ' WHERE a.'.self::$key.' IN ('.$config['pks'].')', self::$key );
					foreach ( $storages[self::$table] as $s ) {
						$s->slug	=	( $s->alias ) ? $s->id.':'.$s->alias : $s->id;
					}
				}
			}
		}
		$config['author']	=	(int)$storages[self::$table][$config['pk']]->{self::$author};
	}
	
	// onCCK_Storage_LocationPrepareList
	public static function onCCK_Storage_LocationPrepareList( &$params )
	{
		require_once JPATH_SITE.'/components/com_content/helpers/category.php';
		require_once JPATH_SITE.'/components/com_content/helpers/route.php';
		require_once JPATH_SITE.'/components/com_content/router.php';
		
		JPluginHelper::importPlugin( 'content' );
		$params	=	JComponentHelper::getParams( 'com_content' );
	}
	
	// onCCK_Storage_LocationPrepareOrder
	public function onCCK_Storage_LocationPrepareOrder( $type, &$order, &$tables, &$config = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		$order	=	( isset( self::$ordering[$order] ) ) ? $tables[self::$table]['_'] .'.'. self::$ordering[$order] : '';
	}
	
	// onCCK_Storage_LocationPrepareSearch
	public function onCCK_Storage_LocationPrepareSearch( $type, &$query, &$tables, &$t, &$config = array(), &$inherit = array(), $user )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		// Prepare
		if ( ! isset( $tables[self::$table] ) ) {
			$tables[self::$table]	=	array( '_'=>'t'.$t++,
											   'fields'=>array(),
											   'join'=>1,
											   'location'=>self::$type
										);
		}
		
		// Set
		$t_pk	=	$tables[self::$table]['_'];
		if ( ! isset( $tables[self::$table]['fields']['published'] ) ) {
			$query->where( $t_pk.'.published = 1' );
		}
		if ( ! isset( $tables[self::$table]['fields']['access'] ) ) {
			$access	=	implode( ',', $user->getAuthorisedViewLevels() );
			$query->where( $t_pk.'.access IN ('.$access.')' );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// onCCK_Storage_LocationDelete
	public static function onCCK_Storage_LocationDelete( $pk, &$config = array() )
	{
		$app		=	JFactory::getApplication();
		$dispatcher	=	JEventDispatcher::getInstance();
		$table		=	self::_getTable( $pk );	
		
		if ( !$table ) {
			return false;
		}
		
		// Check
		$user 			=	JCck::getUser();
		$canDelete		=	$user->authorise( 'core.delete', 'com_cck.form.'.$config['type_id'] );
		$canDeleteOwn	=	$user->authorise( 'core.delete.own', 'com_cck.form.'.$config['type_id'] );
		if ( ( !$canDelete && !$canDeleteOwn ) ||
			 ( !$canDelete && $canDeleteOwn && $config['author'] != $user->id ) ||
			 ( $canDelete && !$canDeleteOwn && $config['author'] == $user->id ) ) {
			$app->enqueueMessage( JText::_( 'COM_CCK_ERROR_DELETE_NOT_PERMITTED' ), 'error' );
			return;
		}
		if ( $table->extension == '' ) {
			$table->extension	=	'com_content';
		}
		JFactory::getApplication()->input->set( 'extension', $table->extension );
		
		// Process
		$result	=	$dispatcher->trigger( 'onContentBeforeDelete', array( self::$context, $table ) );
		if ( in_array( false, $result, true ) ) {
			return false;
		}
		if ( !$table->delete( $pk ) ) {
			return false;
		}
		$dispatcher->trigger( 'onContentAfterDelete', array( self::$context, $table ) );
		
		return true;
	}
	
	// onCCK_Storage_LocationSaveOrder
	public static function onCCK_Storage_LocationSaveOrder( $ids = array(), $lft = array() )
	{
		$table	=	self::_getTable();

		if ( !$table->saveorder( $ids, $lft ) ) {
			return false;
		}

		return true;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Protected
	
	// _core
	protected function _core( $data, &$config = array(), $pk = 0 )
	{
		if ( ! $config['id'] ) {
			$isNew			=	true;
			$config['id']	=	parent::g_onCCK_Storage_LocationPrepareStore();
		} else {
			$isNew			=	false;
		}
		
		// Init
		$table		=	self::_getTable( $pk );
		$isNew		=	( $pk > 0 ) ? false : true;
		if ( isset( $table->tags ) ) {
			$tags	=	$table->tags;
			unset( $table->tags );
		} else {
			$tags	=	null;
		}
		if ( isset( $data['tags'] ) ) {
			if ( !empty( $data['tags'] ) && $data['tags'][0] != '' ) {
				$table->newTags	=	$data['tags'];
			}
			unset( $data['tags'] );
		}
		self::_initTable( $table, $data, $config );
		
		// Check Error
		if ( self::$error === true ) {
			$config['error']	=	true;

			return false;
		}
		
		// Prepare
		if ( is_array( $data ) ) {
			if ( $config['task'] == 'save2copy' ) {
				$empty		=	array( self::$key, 'alias', 'created_time', 'created_user_id', 'hits', 'modified_time', 'modified_user_id', 'version' );
				foreach ( $empty as $k ) {
					$data[$k]	=	'';
				}
			}
			$table->bind( $data );
		}
		if ( $isNew && !isset( $data['rules'] ) ) {
			$data['rules']	=	array( 'core.create'=>array(), 'core.delete'=>array(), 'core.edit'=>array(), 'core.edit.state'=>array(), 'core.edit.own'=>array() );
		}
		if ( isset( $data['rules'] ) && $data['rules'] ) {
			if ( !is_array( $data['rules'] ) ) {
				$data['rules']	=	json_decode( $data['rules'] );
			}
			$rules	=	new JAccessRules( JCckDevHelper::getRules( $data['rules'] ) );
			$table->setRules( $rules );
		}
		$table->check();
		self::_completeTable( $table, $data, $config );
		
		// Store
		JPluginHelper::importPlugin( 'content' );
		$dispatcher	=	JEventDispatcher::getInstance();
		$dispatcher->trigger( 'onContentBeforeSave', array( self::$context, &$table, $isNew ) );
		if ( $isNew === true && parent::g_isMax( $table->{self::$author}, $table->{self::$parent}, $config ) ) {
			$config['error']	=	true;

			return false;
		}
		if ( !$table->store() ) {
			$error		=	true;

			if ( $isNew ) {
				$i		=	2;
				$alias	=	$table->alias.'-'.$i;
				$test	=	JTable::getInstance( 'Category' );
				
				while ( $test->load( array( 'alias'=>$alias, 'parent_id'=>$table->parent_id ) ) ) {
					$alias		=	$table->alias.'-'.$i++;
				}
				$table->alias	=	$alias;

				if ( $table->store() ) {
					$error		=	false;
				}
			}
			if ( $error ) {
				JFactory::getApplication()->enqueueMessage( $table->getError(), 'error' );

				if ( $isNew ) {
					parent::g_onCCK_Storage_LocationRollback( $config['id'] );
				}
				$config['error']	=	true;

				return false;
			}
		}
		
		// Checkin
		parent::g_checkIn( $table );
		
		self::$pk			=	$table->{self::$key};
		if ( !$config['pk'] ) {
			$config['pk']	=	self::$pk;
		}
		
		$config['author']	=	$table->{self::$author};
		$config['parent']	=	$table->{self::$parent};
		
		parent::g_onCCK_Storage_LocationStore( $data, self::$table, self::$pk, $config );
		$dispatcher->trigger( 'onContentAfterSave', array( self::$context, &$table, $isNew ) );

		// Associations
		if ( JCckDevHelper::hasLanguageAssociations() ) {
			self::_setAssociations( $table, $data, $isNew, $config );
		}
	}
	
	// _getTable
	protected static function _getTable( $pk = 0, $join = false )
	{
		$table	=	JTable::getInstance( 'Category' );
		
		if ( $pk > 0 ) {
			$table->load( $pk );
			if ( $table->id ) {
				if ( $join ) { // todo:join
					$join						=	JCckDatabaseCache::loadObject( 'SELECT a.title, a.alias FROM #__categories AS a WHERE a.id = '.$table->parent_id );	//@
					if ( is_object( $join ) && isset( $join->title ) ) {
						$table->parent_title	=	$join->title;
						$table->parent_alias	=	$join->alias;
					} else {
						$table->parent_title	=	'';
						$table->parent_alias	=	'';
					}
				}
				if ( JCck::on( '3.1' ) ) {
					$table->tags	=	new JHelperTags;

					// if ( (int)JCckDatabaseCache::loadResult( 'SELECT COUNT(id) FROM #__tags' ) > 1 ) {
					$table->tags->getTagIds( $table->id, 'com_content.category' );	// todo: dynamic context per extension
					// }
				}
			}
		}
		
		return $table;
	}
	
	// _initTable
	protected function _initTable( &$table, &$data, &$config, $force = false )
	{
		$user	=	JFactory::getUser();
		
		if ( ! $table->{self::$key} ) {
			parent::g_initTable( $table, ( ( isset( $config['params'] ) ) ? $config['params'] : $this->params->toArray() ), $force );
			$table->{self::$author}		=	$table->{self::$author} ? $table->{self::$author} : JCck::getConfig_Param( 'integration_user_default_author', 42 );
			if ( isset( $config['params'] ) ) {
				$table->access				=	( isset( $config['params']['base_default-access'] ) ) ? $config['params']['base_default-access'] : 1;
				if ( ! isset( $data['parent_id'] ) ) {
					$data['parent_id']		=	( isset( $config['params']['base_default-parent_id'] ) ) ? $config['params']['base_default-parent_id'] : 1;
				}
			} else {
				$table->access				=	$this->params->get( 'base_default-access', 1 );
				if ( ! isset( $data['parent_id'] ) ) {
					$data['parent_id']		=	$this->params->get( 'base_default-parent_id', 1 );
				}
			}
			if ( ( $user->id > 0 && @$user->guest != 1 ) && !isset( $data[self::$author] ) && !$force ) {
				$data[self::$author]	=	$user->id;
			}
		} else {
			$data[self::$key]	=	$table->{self::$key};
		}
		if ( $table->parent_id != $data['parent_id'] || $data['id'] == 0 ) {
			$table->setLocation( $data['parent_id'], 'last-child' );
		}
		$table->{self::$custom}	=	'';
	}
	
	// _completeTable
	protected function _completeTable( &$table, &$data, &$config )
	{
		if ( ! $table->{self::$key} ) {
			$table->modified_user_id	=	0;
			$table->extension			=	( ! $table->extension ) ? 'com_content' : $table->extension;
		}
		$table->path	=	( $table->parent_id > 1 ) ? self::_getTable( $table->parent_id )->path.'/' : '';
		$table->path	.=	$table->alias;
		if ( empty( $table->language ) ) {
			$table->language	=	'*';
		}
		
		parent::g_completeTable( $table, self::$custom, $config );
	}

	// _setAssociations
	protected function _setAssociations( $table, $data, $isNew, $config )
	{
		if ( !( isset( $data['associations'] ) && is_array( $data['associations'] ) ) ) {
			return;
		}
		$app	=	JFactory::getApplication();
		$db		=	JFactory::getDbo();

		$associations	=	$data['associations'];
		foreach ( $associations as $tag=>$id ) {
			if ( empty( $id ) ) {
				unset( $associations[$tag] );
			}
		}

		// Detecting all associations
		$all_language	=	$table->language == '*';

		if ( $all_language && !empty( $associations ) ) {
			JError::raiseNotice( 403, JText::_( 'COM_CATEGORIES_ERROR_ALL_LANGUAGE_ASSOCIATED' ) );
		}
		$associations[$table->language]	=	$table->{self::$key};

		// Deleting old association for these items
		$query	=	$db->getQuery( true )
				->delete( '#__associations' )
				->where( 'context=' . $db->quote( 'com_categories.item' ) )
				->where( 'id IN (' . implode(',', $associations ) . ')' );
		$db->setQuery( $query );
		$db->execute();

		if ( $error = $db->getErrorMsg() ) {
			$app->enqueueMessage( $error, 'error' );
			return false;
		}

		if ( !$all_language && count( $associations ) ) {
			// Adding new association for these items
			$key	=	md5( json_encode( $associations ) );
			$query->clear()->insert( '#__associations' );
			foreach ( $associations as $tag=>$id ) {
				$query->values( $id . ',' . $db->quote( 'com_categories.item' ) . ',' . $db->quote( $key ) );
			}
			$db->setQuery( $query );
			$db->execute();

			if ( $error = $db->getErrorMsg() ) {
				$app->enqueueMessage( $error, 'error' );
				return false;
			}
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // SEF

	// buildRoute
	public static function buildRoute( &$query, &$segments, $config, $menuItem = NULL )
	{
		if ( isset( $query['typeid'] ) ) {
			$segments[]	=	$query['typeid'];
			unset( $query['typeid'] );
		} elseif ( isset( $query['catid'] ) ) {
			//if ( $config['doSEF'] == '1' ) {
				$catid	=	$query['catid'];
			//} else {
			//	list( $tmp, $catid )	=	explode( ':', $query['catid'], 2 );
			//}
			$segments[]	=	$catid;
			unset( $query['catid'] );
		}
		
		if ( isset( $query['id'] ) ) {
			if ( self::$sef[$config['doSEF']] == 'full' ) {
				$id		=	$query['id'];
			} else {
				if ( strpos( $query['id'], ':' ) !== false ) {
					$idArray	=	explode( ':', $query['id'], 2 );

					if ( self::$sef[$config['doSEF']] == 'alias' ) {
						$id		=	(string)$idArray[1];
					} else {
						$id		=	(int)$idArray[0];
					}
				} else {
					$id			=	$query['id'];
				}
			}
			$segments[]	=	$id;
			unset( $query['id'] );
		}
	}
	
	// hasAssociations
	public static function hasAssociations()
	{
		static $assoc	=	null;

		if ( !is_null( $assoc ) ) {
			return $assoc;
		}

		$app		=	JFactory::getApplication();
		$assoc		=	JCckDevHelper::hasLanguageAssociations();
		$extension	=	$app->input->get( 'extension', 'com_content' );
		$component	=	str_replace('com_', '', $extension );

		if (!$assoc || !$extension || !$component ) {
			$assoc = false;
		} else {
			$name	=	$component.'HelperAssociation';
			JLoader::register( $name, JPATH_SITE.'/components/'.$extension.'/helpers/association.php' );

			$assoc	=	class_exists( $name ) && !empty( $name::$category_association );
		}

		return $assoc;
	}

	// getRoute
	public static function getRoute( $item, $sef, $itemId, $config = array() )
	{
		$route		=	'';
		if ( is_numeric( $item ) ) {
			$item	=	self::_getTable( (int)$item, true );
			if ( empty( $item ) ) {
				return '';
			}
		}
		$pk			=	( isset( $item->pk ) ) ? $item->pk : $item->id;
		$item->slug	=	( $item->alias ) ? $pk.':'.$item->alias : $pk;
		
		if ( $sef ) {
			if ( $sef == '0' || $sef == '1' ) {
				$path	=	'&catid='.$item->catid;
			} elseif ( $sef[0] == '4' ) {
				$path	=	'&catid='.( isset( $item->parent_alias ) ? $item->parent_alias : $item->parent_id );
			} elseif ( $sef[0] == '3' ) {
				$path	=	( $config['type'] ) ? '&typeid='.$config['type'] : '';
			} else {
				$path	=	'';
			}
			$route		=	self::_getRoute( $sef, $itemId, $item->slug, $path );
		} else {
			require_once JPATH_SITE.'/components/com_content/helpers/route.php';
			$route		=	ContentHelperRoute::getCategoryRoute( $item->id );
		}
		
		return JRoute::_( $route, false );
	}
	
	// getRouteByStorage
	public static function getRouteByStorage( &$storage, $sef, $itemId, $config = array(), $lang_tag = '' )
	{
		$idx	=	md5( $sef.'|'.$itemId.'|'.$lang_tag );

		if ( isset( $storage[self::$table]->_route[$idx] ) ) {
			return JRoute::_( $storage[self::$table]->_route[$idx], false );
		}
		
		if ( $sef ) {
			if ( $sef == '0' || $sef == '1' ) {
				$path	=	'&catid='.$storage[self::$table]->catid;
			} elseif ( $sef[0] == '4' ) {
				$path	=	'&catid='.( isset( $storage[self::$table]->parent_alias ) ? $storage[self::$table]->parent_alias : $storage[self::$table]->parent_id );
			} elseif ( $sef[0] == '3' ) {
				$path	=	'&typeid='.$config['type'];
			} else {
				$path	=	'';
			}
			if ( is_object( $storage[self::$table] ) ) {
				if ( !isset( $storage[self::$table]->_route ) ) {
					$storage[self::$table]->_route		=	array();
				}
				$storage[self::$table]->_route[$idx]	=	self::_getRoute( $sef, $itemId, $storage[self::$table]->slug, $path );
			}

			// Multilanguage Associations
			if ( JCckDevHelper::hasLanguageAssociations() ) {
				// TODO (mod_cck_lang...)
			}
		} else {
			require_once JPATH_SITE.'/components/com_content/helpers/route.php';

			if ( !isset( $storage[self::$table]->_route ) ) {
				$storage[self::$table]->_route		=	array();
			}
			$storage[self::$table]->_route[$idx]	=	ContentHelperRoute::getCategoryRoute( $storage[self::$table]->id );
		}
		
		return JRoute::_( $storage[self::$table]->_route[$idx], false );
	}

	// parseRoute
	public static function parseRoute( &$vars, $segments, $n, $config )
	{
		$active			=	JFactory::getApplication()->getMenu()->getActive();
		$id				=	0;
		$join			=	'';
		$where			=	'';
		
		$vars['option']	=	'com_content';
		$vars['view']	=	self::$routes[(int)self::getStaticParams()->get( 'routing_context', 0 )];

		// Prepare the query
		if ( $n == 2 ) {
			if ( $config['doSEF'][0] == '3' ) {
				$join				=	' LEFT JOIN #__cck_core AS b on b.'.$config['join_key'].' = a.id';
				$where				=	' AND b.cck = '.JCckDatabase::quote( (string)$segments[0] );
			} else {
				$join				=	' LEFT JOIN #__categories AS b on b.id = a.parent_id';
				if ( $config['doSEF'] == '1'  ) {
					$where			=	' AND b.id = '.(int)$segments[0];
					$vars['catid']	=	$segments[0];
				} else {
					$segments[0]	=	str_replace( ':', '-', $segments[0] );
					$where			=	' AND b.alias = '.JCckDatabase::quote( $segments[0] );
				}
			}
		}

		// Retrieve Content Type(s)
		if ( isset( $active->query['search'] ) && $active->query['search'] ) {
			$cck			=	JCckDatabaseCache::loadResult( 'SELECT sef_route FROM #__cck_core_searchs WHERE name = '.JCckDatabase::quote( $active->query['search'] ) );
			
			if ( $cck != '' ) {
				$join		=	' LEFT JOIN #__cck_core AS b on b.'.$config['join_key'].' = a.id';
				$where		=	( strpos( $cck, ',' ) !== false ) ? ' AND b.cck IN ("'.str_replace( ',', '","', $cck ).'")' : ' AND b.cck = "'.$cck.'"';
			}
		}

		// Identity the PK
		if ( self::$sef[$config['doSEF']] == 'full' ) {
			$idArray				=	explode( ':', $segments[$n - 1], 2 );
			$id						=	(int)$idArray[0];

			if ( $where != '' ) {
				$where				=	' WHERE a.id = '.JCckDatabase::clean( $id ).$where;
			}
		} else {
			if ( is_numeric( $segments[$n - 1] ) ) {
				$id					=	$segments[$n - 1];

				if ( $where != '' ) {
					$where			=	' WHERE a.id = '.JCckDatabase::clean( $id ).$where;
				}
			} else {
				$segments[$n - 1]	=	str_replace( ':', '-', $segments[$n - 1] );
				$where				=	' WHERE a.alias = '.JCckDatabase::quote( $segments[$n - 1] ).$where;
			}
		}
		if ( $where != '' ) {
			$vars['id']	=	(int)JCckDatabaseCache::loadResult( 'SELECT a.id FROM '.self::$table.' AS a'.$join.$where );
		} else {
			$vars['id']	=	$id;
		}
		if ( $vars['id'] == 0 ) {
			throw new Exception( JText::_( 'JGLOBAL_CATEGORY_NOT_FOUND' ), 404 );
		}
	}
	
	// setRoutes
	public static function setRoutes( $items, $sef, $itemId )
	{
		if ( count( $items ) ) {
			foreach ( $items as $item ) {
				$item->link	=	self::getRoute( $item, $sef, $itemId );
			}
		}
	}
	
	// _getRoute
	public static function _getRoute( $sef, $itemId, $id, $path = '', $option = '' )
	{
		static $isAdmin	=	-1;
		static $itemIds	=	array();

		if ( $isAdmin == -1 ) {
			$isAdmin	=	JFactory::getApplication()->isClient( 'administrator' );
		}

		if ( $itemId && !$isAdmin ) {
			$mode	=	@$sef[0];
			$index	=	$itemId.'_'.$mode;
			
			if ( !isset( $itemIds[$index] ) ) {
				$menu				=	JFactory::getApplication()->getMenu();
				$item				=	$menu->getItem( $itemId );

				if ( !is_object( $item ) ) {
					$itemIds[$index]	=	'/';
				} else {
					$app		=	JFactory::getApplication();
					$isChild	=	false;

					if ( $item->query['view'] == self::$routes[(int)self::getStaticParams()->get( 'routing_context', 0 )] ) {
						$item2	=	$menu->getItem( $item->parent_id );

						if ( is_object( $item2 ) && @$item2->query['option'] == 'com_cck' && @$item2->query['view'] == 'list' ) {
							$isChild	=	true;
							$itemId		=	$item2->id;
						}
					}
					if ( !$isChild ) {
						$itemIds[$index]	=	'option='.$item->query['option'].'&view='.$item->query['view'];
					}
				}
			}
			if ( isset( $itemIds[$index] ) ) {
				// Check Query
				if ( $itemIds[$index] == '/' ) {
					return ''; /* No Link */
				} elseif ( $itemIds[$index] == 'option=com_content&view='.self::$routes[(int)self::getStaticParams()->get( 'routing_context', 0 )] ) {
					return 'index.php?Itemid='.$itemId; /* Direct Link */
				}
			}
		}

		$option	=	( $option != '' ) ? 'option='.$option.'&' : '';
		$link	=	'index.php?'.$option.'view='.self::$routes[(int)self::getStaticParams()->get( 'routing_context', 0 )].$path;

		if ( $id ) {
			$link	.=	'&id='.$id; 
		}
		if ( $itemId ) {
			$link	.=	'&Itemid='.$itemId;
		}
		
		return $link;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// access
	public static function access( $pk, $checkAccess = true )
	{
		$states	=	self::getStaticParams()->get( 'allowed_status', '1,2' );
		$states	=	explode( ',', $states );
		$states	=	ArrayHelper::toInteger( $states );
		$states	=	( count( $states ) > 1 ) ? 'IN ('.implode( ',', $states ).')' : '= '.(int)$states[0];
		$query	=	'SELECT '.self::$key
				.	' FROM '.self::$table
				.	' WHERE '.self::$key.' = '.(int)$pk
				.	' AND '.self::$status.' '.$states
				;

		if ( $checkAccess ) {
			$query	.=	' AND '.self::$access.' IN ('.implode( ',', JFactory::getUser()->getAuthorisedViewLevels() ).')';
		}
		
		return (int)JCckDatabaseCache::loadResult( $query );
	}
	
	// authorise
	public static function authorise( $rule, $pk )
	{
		return JFactory::getUser()->authorise( $rule, 'com_content.category.'.$pk );
	}
	
	// checkIn
	public static function checkIn( $pk = 0 )
	{
		if ( !$pk ) {
			return false;
		}
		
		$table	=	self::_getTable( $pk );
		
		return parent::g_checkIn( $table );
	}
	
	// getId
	public static function getId( $config )
	{
		return JCckDatabase::loadResult( 'SELECT id FROM #__cck_core WHERE storage_location="'.self::$type.'" AND pk='.(int)$config['pk'] );
	}
	
	// getStaticProperties
	public static function getStaticProperties( $properties )
	{
		static $autorized	=	array(
									'access'=>'',
									'author'=>'',
									'author_object'=>'',
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
			$legacy	=	(int)self::getStaticParams()->get( 'routing_context', 0 );

			foreach ( $properties as $i=>$p ) {
				if ( isset( $autorized[$p] ) ) {
					if ( $p == 'contexts' && $legacy == 0 ) {
						$properties[$p]	=	array();
					} else {
						$properties[$p]	=	self::${$p};
					}
				}
				unset( $properties[$i] );
			}
		}
		
		return $properties;
	}
}
?>