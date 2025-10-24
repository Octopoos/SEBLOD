<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JLoader::register( 'MenusTableMenu', JPATH_ADMINISTRATOR . '/components/com_menus/tables/menu.php' );

// Plugin
class plgCCK_Storage_LocationJoomla_Menu_Item extends JCckPluginLocation
{
	protected static $type			=	'joomla_menu_item';
	protected static $type_alias	=	'MenuItem';
	protected static $table			=	'#__menu';
	protected static $table_object	=	array( 'Menu', 'JTable' );
	protected static $key			=	'id';
	protected static $key_field		=	'nav_item_pk';
	
	protected static $access		=	'access';
	protected static $author		=	'';
	protected static $author_object	=	'';
	protected static $bridge_object	=	'';
	protected static $child_object	=	'';
	protected static $created_at	=	'';
	protected static $custom		=	'';
	protected static $modified_at	=	'';
	protected static $parent		=	'parent_id';
	protected static $parent_object	=	'joomla_menu_item';
	protected static $status		=	'published';
	protected static $to_route		=	'a.id as pk, a.title, a.alias, a.language';
	
	protected static $context		=	'com_menus.item'; /* used for Delete/Save events */
	protected static $context2		=	'';
	protected static $contexts		=	array(); /* used for Content/Intro views */
	protected static $error			=	false;
	protected static $events		=	array(
											'afterDelete'=>'onContentAfterDelete',
											'afterSave'=>'onContentAfterSave',
											'beforeDelete'=>'onContentBeforeDelete',
											'beforeSave'=>'onContentBeforeSave'
										);
	protected static $ordering		=	array( 'alpha'=>'title ASC', 'ordering'=>'lft ASC' );
	protected static $ordering2		=	array();
	protected static $pk			=	0;
	protected static $routes		=	array();
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
			$storage			=	self::_getTable( $pk );
			$storage->slug		=	( isset( $storage->alias ) && $storage->alias ) ? $storage->id.':'.$storage->alias : $storage->id;
		} else {
			$storage	=	parent::g_onCCK_Storage_LocationPrepareContent( $table, $pk );
			if ( ! isset( $config['storages'][self::$table] ) ) {
				$config['storages'][self::$table]->slug	=	( $config['storages'][self::$table]->alias ) ? $config['storages'][self::$table]->id.':'.$config['storages'][self::$table]->alias
																										 : $config['storages'][self::$table]->id;
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
		
		// Set
		if ( $table == self::$table ) {
			$storage	=	self::_getTable( $pk );
		} else {
			$storage	=	parent::g_onCCK_Storage_LocationPrepareForm( $table, $pk );
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
				$storages[$table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$table.' WHERE '.self::$key.' IN ('.$config['pks'].')', self::$key );
			} else {
				$storages[$table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$table.' WHERE id IN ('.$config['pks'].')', 'id' );
				if ( !isset( $storages[self::$table] ) ) {
					$storages['_']			=	self::$table;
					$storages[self::$table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.self::$table.' WHERE '.self::$key.' IN ('.$config['pks'].')', self::$key );
				}
			}
		}
		//$config['author']	=	$storages[self::$table][$config['pk']]->{self::$author};
	}
	
	// onCCK_Storage_LocationPrepareList
	public static function onCCK_Storage_LocationPrepareList( &$params )
	{
		/* TODO#SEBLOD: */
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
	public function onCCK_Storage_LocationPrepareSearch( $type, &$query, &$tables, &$t, &$config, &$inherit, $user )
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
		$table	=	self::_getTable( $pk );
		$isNew	=	( $pk > 0 ) ? false : true;
		self::_initTable( $table, $data, $config );
		
		// Check Error
		if ( self::$error === true ) {
			$config['error']	=	true;
			
			return false;
		}
		
		// Prepare
		if ( !$isNew ) {
			if ( $table->parent_id == $data['parent_id'] ) {
				//
			} else {
				$table->setLocation( $data['parent_id'], 'last-child' );
			}
		} else {
			$table->setLocation( $data['parent_id'], 'last-child' );
		}
		$storeTable 	= 	'#__cck_store_item_menu';

		if ( !( isset( $data['type'] ) && $data['type'] ) ) {
			$data['type']	=	'component';
		}

		if ( isset( $config['storages'][$storeTable]['item_type'] ) ) {
			$menuItemType	=	$config['storages'][$storeTable]['item_type'];

			if ( strpos( $menuItemType, '.' ) === false ) {
				$data['type']	=	$menuItemType;
			}

			switch ( $data['type'] ) {
				case 'component':
					$component		=	'';
					$item_request	=	json_decode( $config['storages'][$storeTable]['item_request'] );

					unset( $data['link'] );

					switch ( $menuItemType ) {
						case 'com_content.article':
							$component		=	'com_content';
							$table->link	= 	sprintf(
													'index.php?option=com_content&view=article&id=%s',
													$item_request->id
												);
							break;
						case 'com_content.category':
							$component		=	'com_content';
							$table->link	= 	sprintf(
													'index.php?option=com_content&view=category&id=%s',
													$item_request->id
												);
							break;
						case 'com_cck.api':
							$component		=	'com_cck_webservices';
							$table->link	=	'index.php?option=com_cck_webservices&view=api';
							break;
						case 'com_cck.api-docs':
							$component		=	'com_cck_webservices';
							$table->link	=	'index.php?option=com_cck_webservices&view=api_docs';
							break;
						case 'com_cck.custom':
							$component		=	'com_cck_toolbox';
							$table->link	=	'index.php?option=com_cck_toolbox&view=processing';
							break;
						case 'com_cck.form':
							$component		=	'com_cck';
							$table->link	= 	sprintf(
													'index.php?option=com_cck&view=form&layout=edit&type=%s',
													$item_request->type
												);
							break;
						case 'com_cck.list':
							$component		=	'com_cck';
							$table->link 	= 	sprintf(
													'index.php?option=com_cck&view=list&search=%s',
													$item_request->search
												);

							if ( isset( $item_request->task ) && !$item_request->task ) {
								$table->link	.=	'&task=no';
							} else {
								$table->link	.=	'&task=search';
							}
							break;
						case 'com_users.logout':
							$component	=	'com_users';
							break;
						default:
							break;
					}
					$table->component_id 	= 	(int)JTable::getInstance( 'Extension' )->find( array( 'name'=>$component, 'type'=>'component' ) );
					break;
				case 'alias':
					$table->component_id	=	0;
					break;
				case 'heading':
					$table->component_id	=	0;
					break;
				case 'separator':
					$table->component_id	=	0;
					break;
				case 'url':
					$table->component_id	=	0;
					$table->path			=	$table->alias;
					break;
				default:
					break;
			}
		}
		
		if ( !$isNew ) {
			if ( isset( $data['params'] ) && $data['params'] != '' ) {
				if ( $table->params != '' ) {
					$new_params	=	json_decode( $data['params'] );
					$params		=	json_decode( $table->params );

					foreach ( $new_params as $k=>$v ) {
						$params->$k	=	$v;
					}

					$table->params	=	json_encode( $params );

					unset( $data['params'] );
				}
			}
		}
		$table->bind( $data );
		
		if ( !$table->check() ) {
			JFactory::getApplication()->enqueueMessage( $table->getError(), 'error' );
		}

		self::_completeTable( $table, $data, $config );

		// Store
		$dispatcher	=	JEventDispatcher::getInstance();
		$dispatcher->trigger( 'onContentBeforeSave', array( self::$context, &$table, $isNew ) );
        if ( !$table->store() ) {
			JFactory::getApplication()->enqueueMessage( $table->getError(), 'error' );

			if ( $isNew ) {
				parent::g_onCCK_Storage_LocationRollback( $config['id'] );
			}
			$config['error']	=	true;

			return false;
		}
		
		// Rebuild the tree path.
		if ( !$table->rebuildPath( $table->id ) ) {
			JFactory::getApplication()->enqueueMessage( $table->getError(), 'error' );
		}
		
		// Checkin
		// parent::g_checkIn( $table );
		self::$pk	=	$table->{self::$key};
		if ( !$config['pk'] ) {
			$config['pk']	=	self::$pk;
		}
		
		$config['parent']	=	$table->{self::$parent};
		
		parent::g_onCCK_Storage_LocationStore( $data, self::$table, self::$pk, $config );
		$dispatcher->trigger( 'onContentAfterSave', array( self::$context, &$table, $isNew ) );
	}
	
	// _getTable
	protected static function _getTable( $pk = 0 )
	{
		if ( Jcck::on( '5' ) ) {
			$table	=	new \Joomla\Component\Menus\Administrator\Table\MenuTypeTable( JFactory::getDbo() );
		} else {
			$table	=	JTable::getInstance( 'Menu', 'MenusTable' );
		}
		
		if ( $pk > 0 ) {
			$table->load( $pk );
		}
		
		return $table;
	}
	
	// _initTable
	protected function _initTable( &$table, &$data, &$config, $force = false )
	{
		if ( ! $table->{self::$key} ) {
			parent::g_initTable( $table, ( ( isset( $config['params'] ) ) ? $config['params'] : $this->params->toArray() ), $force );
		}
	}
	
	// _completeTable
	protected function _completeTable( &$table, &$data, &$config )
	{
		if ( ! $table->{self::$key} ) {
		}
		
		parent::g_completeTable( $table, self::$custom, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // SEF

	// buildRoute
	public static function buildRoute( &$query, &$segments, $config, $menuItem = null )
	{
	}
	
	// getRoute
	public static function getRoute( $item, $sef, $itemId, $config = array() )
	{
		$route		=	'';
		
		return JRoute::_( $route );
	}
	
	// getRouteByStorage
	public static function getRouteByStorage( &$storage, $sef, $itemId, $config = array() )
	{
		if ( isset( $storage[self::$table]->_route ) ) {
			return JRoute::_( $storage[self::$table]->_route );
		}
		
		if ( $sef ) {
			$storage[self::$table]->_route	=	'';
		} else {
			$storage[self::$table]->_route	=	'';
		}
		
		return JRoute::_( $storage[self::$table]->_route );
	}
	
	// parseRoute
	public static function parseRoute( &$vars, $segments, $n, $config )
	{
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
	public static function _getRoute( $itemId, $id, $option = '' )
	{
		return '';
	}
}
?>