<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: joomla_user_group.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JLoader::register( 'JTableUsergroup', JPATH_PLATFORM.'/joomla/database/table/usergroup.php' );

// Plugin
class plgCCK_Storage_LocationJoomla_User_Group extends JCckPluginLocation
{
	protected static $type			=	'joomla_user_group';
	protected static $table			=	'#__usergroups';
	protected static $table_object	=	array( 'Usergroup', 'JTable' );
	protected static $key			=	'id';
	
	protected static $access		=	'';
	protected static $author		=	'';
	protected static $author_object	=	'';
	protected static $bridge_object	=	'joomla_article';
	protected static $child_object	=	'';
	protected static $created_at	=	'';
	protected static $custom		=	'';
	protected static $modified_at	=	'';
	protected static $parent		=	'';
	protected static $parent_object	=	'';
	protected static $status		=	'';
	protected static $to_route		=	'';
	
	protected static $context		=	'';
	protected static $context2		=	'';
	protected static $contexts		=	array( 'com_content.article' );
	protected static $error			=	false;
	protected static $events		=	array(
											'afterDelete'=>'onUserAfterDeleteGroup',
											'afterSave'=>'onUserAfterSaveGroup',
											'beforeDelete'=>'onUserBeforeDeleteGroup',
											'beforeSave'=>'onUserBeforeSaveGroup'
										);
	protected static $ordering		=	array( 'alpha'=>'title ASC' );
	protected static $ordering2		=	array( 'newest'=>'created DESC', 'oldest'=>'created ASC', 'ordering'=>'ordering ASC', 'popular'=>'hits DESC' );
	protected static $pk			=	0;
	protected static $routes		=	array();
	protected static $sef			=	array();
	
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
		$data['core_columns']	=	array( 'tags' );
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
			$storage	=	self::_getTable( $pk );
		} else {
			$storage	=	parent::g_onCCK_Storage_LocationPrepareContent( $table, $pk );
			if ( ! isset( $config['storages'][self::$table] ) ) {
				$config['storages'][self::$table]	=	self::_getTable( $pk );
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
		if ( !isset( $config['primary'] ) ) {
			$config['primary']	=	self::$type;
			$config['pkb']		=	JCckDatabase::loadResult( 'SELECT pkb FROM #__cck_core WHERE storage_location="'.self::$type.'" AND pk='.(int)$config['pk'] ); // todo: move+improve
		}
		
		// Set
		if ( $table == self::$table ) {
			$storage			=	self::_getTable( $pk );
			$config['asset']	=	'';
			$config['asset_id']	=	0;
			$config['author']	=	parent::g_getBridgeAuthor( 'joomla_article', $pk, self::$type );
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
				$storages[$table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$table.' WHERE '.self::$key.' IN ('.$config['pks'].')', self::$key );
			} else {
				$storages[$table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$table.' WHERE id IN ('.$config['pks'].')', 'id' );
				if ( !isset( $storages[self::$table] ) ) {
					$storages['_']			=	self::$table;
					$storages[self::$table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.self::$table.' WHERE '.self::$key.' IN ('.$config['pks'].')', self::$key );
				}
			}
		}
		//$config['author']	=	''; //todo
	}
	
	// onCCK_Storage_LocationPrepareList
	public static function onCCK_Storage_LocationPrepareList( &$params )
	{
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
		
		if ( !$this->params->get( 'bridge', 0 ) ) {
			$order	=	'alpha';
		}
		if ( $order == 'alpha' ) {
			$order	=	( isset( self::$ordering[$order] ) ) ? $tables[self::$table]['_'] .'.'. self::$ordering[$order] : '';
		} else {
			$order	=	( isset( self::$ordering2[$order] ) ) ? $tables['#__content']['_'] .'.'. self::$ordering2[$order] : '';
		}
	}
	
	// onCCK_Storage_LocationPrepareSearch
	public function onCCK_Storage_LocationPrepareSearch( $type, &$query, &$tables, &$t, &$config = array(), &$inherit = array(), $user )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		// Init
		$db		=	JFactory::getDbo();
		$now	=	substr( JFactory::getDate()->toSql(), 0, -3 );
		$null	=	$db->getNullDate();
		
		// Prepare
		if ( !$this->params->get( 'bridge', 0 ) ) {
			if ( ! isset( $tables[self::$table] ) ) {
				$tables[self::$table]	=	array( '_'=>'t'.$t++,
												   'fields'=>array(),
												   'join'=>1,
												   'location'=>self::$type
											);
			}
		} else {
			$bridge	=	'#__content';
			if ( ! isset( $tables[$bridge] ) ) {
				$tables[$bridge]	=	array( '_'=>'t'.$t++,
											   'fields'=>array(),
											   'join'=>1,
											   'key'=>'id',
											   'location'=>'joomla_article'
										);
			}
			if ( ! isset( $tables[self::$table] ) ) {
				$tables[self::$table]	=	array( '_'=>'t'.$t++,
												   'fields'=>array(),
												   'join'=>1,
												   'location'=>self::$type
											);
			}
			$t_pk				=	$tables[self::$table]['_'];
			$t_pkb				=	$tables[$bridge]['_'];
			$inherit['bridge']	=	$bridge;
			
			// Set
			if ( ! isset( $tables[$bridge]['fields']['state'] ) ) {
				$query->where( $t_pkb.'.state = 1' );
			}
			if ( ! isset( $tables[$bridge]['fields']['access'] ) ) {
				$access	=	implode( ',', $user->getAuthorisedViewLevels() );
				$query->where( $t_pkb.'.access IN ('.$access.')' );
			}
			if ( ! isset( $tables[$bridge]['fields']['publish_up'] ) ) {
				$query->where( '( '.$t_pkb.'.publish_up = '.$db->quote( $null ).' OR '.$t_pkb.'.publish_up <= '.$db->quote( $now ).' )' );
			}
			if ( ! isset( $tables[$bridge]['fields']['publish_down'] ) ) {
				$query->where( '( '.$t_pkb.'.publish_down = '.$db->quote( $null ).' OR '.$t_pkb.'.publish_down >= '.$db->quote( $now ).' )' );
			}
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
		JPluginHelper::importPlugin( 'user' );

		$result	=	$dispatcher->trigger( 'onUserBeforeDeleteGroup', array( $table->getProperties() ) );
		if ( in_array( false, $result, true ) ) {
			return false;
		}
		if ( !$table->delete( $pk ) ) {
			return false;
		}
		$dispatcher->trigger( 'onUserAfterDeleteGroup', array( $table->getProperties(), true, $table->getError() ) );
		
		return true;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Protected
	
	// _core
	protected function _core( $data, &$config = array(), $pk = 0 )
	{
		if ( ! $config['id'] ) {
			$config['id']	=	parent::g_onCCK_Storage_LocationPrepareStore();
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
		if ( is_array( $data ) ) {
			$table->bind( $data );
		}
		$table->check();
		self::_completeTable( $table, $data, $config );
		
		// Store
		$dispatcher	=	JEventDispatcher::getInstance();
		JPluginHelper::importPlugin( 'user' );
		$dispatcher->trigger( 'onUserBeforeSaveGroup', array( self::$context, &$table, $isNew ) );
		if ( !$table->store() ) {
			JFactory::getApplication()->enqueueMessage( $table->getError(), 'error' );

			if ( $isNew ) {
				parent::g_onCCK_Storage_LocationRollback( $config['id'] );
			}
			$config['error']	=	true;
			
			return false;
		}
		$dispatcher->trigger( 'onUserAfterSaveGroup', array( self::$context, &$table, $isNew ) );
		
		self::$pk	=	$table->{self::$key};
		if ( !$config['pk'] ) {
			$config['pk']	=	self::$pk;
		}
		
		$config['author']	=	JFactory::getUser()->id;
		parent::g_onCCK_Storage_LocationStore( $data, self::$table, self::$pk, $config );
	}
	
	// _getTable
	protected static function _getTable( $pk = 0 )
	{
		$table	=	JTable::getInstance( 'Usergroup' );
		
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
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // SEF
	
	// buildRoute
	public static function buildRoute( &$query, &$segments, $config, $menuItem = NULL )
	{
		require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_article/joomla_article.php';
		plgCCK_Storage_LocationJoomla_Article::buildRoute( $query, $segments, $config, $menuItem );
	}
	
	// getRoute	//todo: make a parent::getBridgeRoute..
	public static function getRoute( $item, $sef, $itemId, $config = array() )
	{
		if ( is_numeric( $item ) ) {
			$core	=	JCckDatabase::loadObject( 'SELECT cck, pkb FROM #__cck_core WHERE storage_location = "'.self::$type.'" AND pk = '.(int)$item );
			if ( !is_object( $core ) ) {
				return '';
			}
			$pk				=	$core->pkb;
			$config['type']	=	$core->cck;
		} else {
			$pk		=	( isset( $item->pk ) ) ? $item->pk : $item->id;
			$pk		=	JCckDatabase::loadResult( 'SELECT pkb FROM #__cck_core WHERE storage_location = "'.self::$type.'" AND pk = '.(int)$pk );
			if ( !$pk ) {
				return '';
			}
		}
		
		require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_article/joomla_article.php';
		return plgCCK_Storage_LocationJoomla_Article::getRoute( $pk, $sef, $itemId, $config );
	}
	
	// getRouteByStorage //todo: make a parent::getBridgeRoute.. + optimize ($storage->)
	public static function getRouteByStorage( &$storage, $sef, $itemId, $config = array() )
	{
		if ( isset( $storage[self::$table]->_route ) ) {
			return JRoute::_( $storage[self::$table]->_route, false );
		}
		
		$bridge			=	JCckDatabase::loadObject( 'SELECT a.id, a.title, a.alias, a.catid, b.title AS category_title, b.alias AS category_alias'
													. ' FROM #__content AS a LEFT JOIN #__categories AS b ON b.id = a.catid'
													. ' WHERE a.id='.(int)$config['pkb'] );
		if ( !is_object( $bridge ) ) {
			$storage[self::$table]->_route	=	'';
			return $storage[self::$table]->_route;
		}
		$bridge->slug	=	( $bridge->alias ) ? $bridge->id.':'.$bridge->alias : $bridge->id;
		$path			=	$bridge->catid;

		if ( $sef ) {
			if ( $sef == '0' || $sef == '1' ) {
				$path	=	'&catid='.$bridge->catid;
			} elseif ( $sef[0] == '4' ) {
				$path	=	'&catid='.( isset( $bridge->category_alias ) ? $bridge->category_alias : $bridge->catid );
			} elseif ( $sef[0] == '3' ) {
				$path	=	'&typeid='.$config['type'];
			} else {
				$path	=	'';
			}
			$storage[self::$table]->_route	=	plgCCK_Storage_LocationJoomla_Article::_getRoute( $sef, $itemId, $bridge->slug, $path );
		} else {
			require_once JPATH_SITE.'/components/com_content/helpers/route.php';
			$storage[self::$table]->_route	=	ContentHelperRoute::getArticleRoute( $bridge->slug, $bridge->catid );
		}
		
		return JRoute::_( $storage[self::$table]->_route, false );
	}
	
	// parseRoute
	public static function parseRoute( &$vars, $segments, $n, $config )
	{
		$config['join_key']	=	'pkb';
		require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_article/joomla_article.php';
		plgCCK_Storage_LocationJoomla_Article::parseRoute( $vars, $segments, $n, $config );
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
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff

	// checkIn
	public static function checkIn( $pk = 0 )
	{
		return true;
	}
	
	// getId
	public static function getId( $config )
	{
		return JCckDatabase::loadResult( 'SELECT id FROM #__cck_core WHERE storage_location="'.self::$type.'" AND pk='.(int)$config['pk'] );
	}
}
?>