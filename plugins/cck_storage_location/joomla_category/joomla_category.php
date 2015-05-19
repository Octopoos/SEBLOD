<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: joomla_category.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

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
	protected static $created_at	=	'created_time';
	protected static $custom		=	'description';
	protected static $modified_at	=	'modified_time';
	protected static $parent		=	'parent_id';
	protected static $parent_object	=	'joomla_category';
	protected static $status		=	'published';
	protected static $to_route		=	'a.id as pk, a.title, a.alias';
	
	protected static $context		=	'com_categories.category';
	protected static $contexts		=	array( 'com_content.categories' );
	protected static $error			=	false;
	protected static $ordering		=	array( 'alpha'=>'title ASC', 'newest'=>'created_time DESC', 'oldest'=>'created_time ASC', 'ordering'=>'lft ASC', 'popular'=>'hits DESC' );
	protected static $pk			=	0;
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
	
	// onCCK_Storage_LocationPrepareDelete
	public function onCCK_Storage_LocationPrepareDelete( &$field, &$storage, $pk = 0, &$config = array() )
	{
		if ( self::$type != $field->storage_location ) {
			return;
		}
		
		// Init
		$table	=	$field->storage_table;
		
		// Set
		if ( $table == self::$table ) {
			$storage	=	self::_getTable( $pk );
		} else {
			$storage	=	parent::g_onCCK_Storage_LocationPrepareForm( $table, $pk );
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
		
		// Set
		if ( $table == self::$table ) {
			$storage			=	self::_getTable( $pk );
			$config['asset']	=	'com_categories';
			
			if ( $config['translate_id'] ) {
				$empty						=	array( self::$key, 'alias', 'created_time', 'created_user_id', 'hits', 'modified_time', 'modified_user_id', 'version' );
				$config['language']			=	JFactory::getApplication()->input->get( 'translate' );
				$config['translate']		=	$storage->language;
				$config['translated_id']	=	$config['translate_id'].':'.$storage->alias;
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
		$dispatcher	=	JDispatcher::getInstance();
		$table		=	self::_getTable( $pk );	
		
		if ( !$table ) {
			return false;
		}
		
		// Check
		$user 			=	JCck::getUser();
		$canDelete		=	$user->authorise( 'core.delete', 'com_cck.form.'.$config['type_id'] );
		$canDeleteOwn	=	$user->authorise( 'core.delete.own', 'com_cck.form.'.$config['type_id'] );
		if ( ( !$canDelete && !$canDeleteOwn ) ||
			 ( !$canDelete && $canDeleteOwn && $config['author'] != $user->get( 'id' ) ) ||
			 ( $canDelete && !$canDeleteOwn && $config['author'] == $user->get( 'id' ) ) ) {
			$app->enqueueMessage( JText::_( 'COM_CCK_ERROR_DELETE_NOT_PERMITTED' ), 'error' );
			return;
		}
		
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
	
	// onCCK_Storage_LocationStore
	public function onCCK_Storage_LocationStore( $type, $data, &$config = array(), $pk = 0 )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		if ( ! @$config['storages'][self::$table]['_']->pk ) {
			self::_core( $config['storages'][self::$table], $config, $pk );
			$config['storages'][self::$table]['_']->pk	=	self::$pk;
		}
		if ( $data['_']->table != self::$table ) {
			parent::g_onCCK_Storage_LocationStore( $data, self::$table, self::$pk, $config );
		}
		
		return self::$pk;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Protected
	
	// _core
	protected function _core( $data, &$config = array(), $pk = 0 )
	{
		if ( ! $config['id'] ) {
			$isNew	=	true;
			$config['id']	=	parent::g_onCCK_Storage_LocationPrepareStore();
		} else {
			$isNew	=	false;
		}
		
		// Init
		$table	=	self::_getTable( $pk );
		$isNew	=	( $pk > 0 ) ? false : true;
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
		$dispatcher	=	JDispatcher::getInstance();
		JPluginHelper::importPlugin( 'content' );
		$dispatcher->trigger( 'onContentBeforeSave', array( self::$context, &$table, $isNew ) );
		if ( $isNew === true && parent::g_isMax( $table->{self::$author}, $table->{self::$parent}, $config ) ) {
			return;
		}
		if ( !$table->store() ) {
			JFactory::getApplication()->enqueueMessage( $table->getError(), 'error' );
			if ( $isNew ) {
				parent::g_onCCK_Storage_LocationRollback( $config['id'] );
			}
			return false;
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
		$table	=	JTable::getInstance( 'category' );
		
		if ( $pk > 0 ) {
			$table->load( $pk );
			if ( $table->id ) {
				if ( $join ) { // todo:join
					$join					=	JCckDatabase::loadObject( 'SELECT a.title, a.alias FROM #__categories AS a WHERE a.id = '.$table->parent_id );	//@
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
					$table->tags->getTagIds( $table->id, 'com_content.category' );	// todo: dynamic context per extension
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
			if ( ( $user->get( 'id' ) > 0 && @$user->guest != 1 ) && !isset( $data[self::$author] ) && !$force ) {
				$data[self::$author]	=	$user->get( 'id' );
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
					if ( self::$sef[$config['doSEF']] == 'alias' ) {
						list( $tmp, $id )	=	explode( ':', $query['id'], 2 );
					} else {
						list( $id, $alias )	=	explode( ':', $query['id'], 2 );
					}
				} else {
					$id		=	$query['id'];
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
		$extension	=	$app->input->getString( 'extension', 'com_content' );
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
		
		return JRoute::_( $route );
	}
	
	// getRouteByStorage
	public static function getRouteByStorage( &$storage, $sef, $itemId, $config = array() )
	{
		if ( isset( $storage[self::$table]->_route ) ) {
			return JRoute::_( $storage[self::$table]->_route );
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
				$storage[self::$table]->_route	=	self::_getRoute( $sef, $itemId, $storage[self::$table]->slug, $path );
			}

			// Multilanguage Associations
			if ( JCckDevHelper::hasLanguageAssociations() ) {
				// TODO (mod_cck_lang...)
			}
		} else {
			require_once JPATH_SITE.'/components/com_content/helpers/route.php';
			$storage[self::$table]->_route	=	ContentHelperRoute::getCategoryRoute( $storage[self::$table]->id );
		}
		
		return JRoute::_( $storage[self::$table]->_route );
	}

	// parseRoute
	public static function parseRoute( &$vars, $segments, $n, $config )
	{
		$join			=	'';
		$where			=	'';
		
		$vars['option']	=	'com_content';
		$vars['view']	=	'categories';

		if ( $n == 2 ) {
			if ( $config['doSEF'][0] == '3' ) {
				$join				=	' LEFT JOIN #__cck_core AS b on b.'.$config['join_key'].' = a.id';
				$where				=	' AND b.cck = "'.(string)$segments[0].'"';
			} else {
				$join				=	' LEFT JOIN #__categories AS b on b.id = a.parent_id';
				if ( $config['doSEF'] == '1'  ) {
					$where			=	' AND b.id = '.(int)$segments[0];
					$vars['catid']	=	$segments[0];
				} else {
					$segments[0]	=	str_replace( ':', '-', $segments[0] );
					$where			=	' AND b.alias = "'.$segments[0].'"';
				}
			}
		} else {
			if ( $config['doSEF'][0] == '2' && isset( $config['doSEF'][1] ) && $config['doSEF'][1] == '4' ) {
				$active				=	JFactory::getApplication()->getMenu()->getActive();
				if ( isset( $active->query['search'] ) && $active->query['search'] ) {
					$cck			=	JCckDatabaseCache::loadResult( 'SELECT sef_route FROM #__cck_core_searchs WHERE name = "'.$active->query['search'].'"' );
					if ( $cck ) {
						$join		=	' LEFT JOIN #__cck_core AS b on b.'.$config['join_key'].' = a.id';
						$where		=	( strpos( $cck, ',' ) !== false ) ? ' AND b.cck IN ("'.str_replace( ',', '","', $cck ).'")' : ' AND b.cck = "'.$cck.'"';
					}
				}
			}
		}
		if ( self::$sef[$config['doSEF']] == 'full' ) {
			list( $id, $alias )		=	explode( ':', $segments[$n - 1], 2 );
			$vars['id']				=	$id;
		} else {
			if ( is_numeric( $segments[$n - 1] ) ) {
				$vars['id']			=	$segments[$n - 1];
			} else {
				$segments[$n - 1]	=	str_replace( ':', '-', $segments[$n - 1] );
				$query				=	'SELECT a.id FROM '.self::$table.' AS a'
									.	$join
									.	' WHERE a.alias = "'.$segments[$n - 1].'"'.$where;
				$vars['id']			=	(int)JCckDatabaseCache::loadResult( $query );
				if ( $vars['id'] == 0 ) {
					return JError::raiseError( 404, JText::_( 'JGLOBAL_CATEGORY_NOT_FOUND' ) );
				}
			}
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
		$option	=	( $option != '' ) ? 'option='.$option.'&' : '';
		$link	=	'index.php?'.$option.'view=categories'.$path;

		if ( $id ) {
			$link	.=	'&id='.$id; 
		}
		if ( $itemId ) {
			$link	.=	'&Itemid='.$itemId;
		}
		
		return $link;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
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
									'created_at'=>'',
									'context'=>'',
									'contexts'=>'',
									'custom'=>'',
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
		static $legacy	=	-1;
		if ( $legacy < 0 ) {
			$plg		=	JPluginHelper::getPlugin( 'cck_storage_location', 'joomla_category' );
			$plg_params	=	new JRegistry( $plg->params );
			$legacy		=	$plg_params->get( 'routing_context', 0 );
		}
		
		if ( count( $properties ) ) {
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