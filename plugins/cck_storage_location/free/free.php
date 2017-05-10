<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: free.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Storage_LocationFree extends JCckPluginLocation
{
	protected static $type			=	'free';
	protected static $table			=	'';
	protected static $table_object	=	array();
	protected static $key			=	'id';
	
	protected static $access		=	'';
	protected static $author		=	'';
	protected static $author_object	=	'';
	protected static $bridge_object	=	'';
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
	protected static $contexts		=	array();
	protected static $error			=	false;
	protected static $ordering		=	array();
	protected static $ordering2		=	array();
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
		$storage	=	parent::g_onCCK_Storage_LocationPrepareContent( $table, ( $table == '#__cck_core' ? $config['id'] : $pk ) );
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
		$storage	=	parent::g_onCCK_Storage_LocationPrepareContent( $table, ( $table == '#__cck_core' ? $config['id'] : $pk ) );
	}

	// onCCK_Storage_LocationPrepareForm
	public function onCCK_Storage_LocationPrepareForm( &$field, &$storage, $pk = 0, &$config = array() )
	{
		if ( self::$type != $field->storage_location ) {
			return;
		}
		
		// Init
		$table	=	$field->storage_table;
		if ( $table == '#__cck_core' ) {
			// todo: use API
			$pk	=	JCckDatabase::loadResult( 'SELECT a.id FROM #__cck_core AS a'
											. ' LEFT JOIN #__cck_core_types AS b ON b.name = a.cck'
											. ' WHERE a.storage_location = b.storage_location AND a.pk = '.(int)$pk );
		}

		// Set
		$storage			=	self::_getTable( $pk, $table );
		$config['asset']	=	'';
		$config['asset_id']	=	0;
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
			if ( $table == '#__cck_core' ) {
				$keys						=	'ids';
				$storages[$table]			=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$table.' WHERE '.self::$key.' IN ('.$config[$keys].')', 'pk' );	//#
			} else {
				$keys						=	'ids';
				$storages['#__cck_core']	=	JCckDatabase::loadObjectList( 'SELECT author_id, pk FROM #__cck_core WHERE '.self::$key.' IN ('.$config[$keys].')', 'pk' );	//#
				$keys						=	'pks';
				$storages[$table]			=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$table.' WHERE '.self::$key.' IN ('.$config[$keys].')', self::$key ); //#				
			}
		}
		$config['author']	=	( isset( $storages['#__cck_core'][$config['pk']] ) ) ? (int)$storages['#__cck_core'][$config['pk']]->author_id : 0;
	}
	
	// onCCK_Storage_LocationPrepareList
	public static function onCCK_Storage_LocationPrepareList( &$params )
	{
	}
	
	// onCCK_Storage_LocationPrepareOrder
	public function onCCK_Storage_LocationPrepareOrder( $type, &$order, &$tables, &$config = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		$order	=	'';
	}

	// onCCK_Storage_LocationPrepareSearch
	public function onCCK_Storage_LocationPrepareSearch( $type, &$query, &$tables, &$t, &$config = array(), &$inherit = array(), $user )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		// Init
		if ( empty( $inherit['table'] ) ) {
			return;
		}
		
		$table	=	$inherit['table'];
		$keys	=	JCckDatabase::loadObject( 'SHOW KEYS FROM '.$table.' WHERE Key_name = "PRIMARY"' );
		
		// Prepare
		if ( ! isset( $tables[$table] ) ) {
			$tables[$table]		=	array( '_'=>'t'.$t++,
										   'fields'=>array(),
										   'join'=>1,
										   'location'=>self::$type
									);
		}
		$tables[$table]['key']	=	( isset( $keys->Column_name ) ) ? $keys->Column_name : 'id';
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Process
	
	// onCCK_Storage_LocationDelete
	public static function onCCK_Storage_LocationDelete( $pk, &$config = array() )
	{
		$app		=	JFactory::getApplication();
		$dispatcher	=	JEventDispatcher::getInstance();
		
		$item		=	JCckDatabase::loadObject( 'SELECT id, cck as type, pk, storage_table FROM #__cck_core WHERE cck = "'.$config['type'].'" AND pk = '.(int)$pk );		
		if ( !is_object( $item ) ) {
			return false;
		}
		if ( !$item->storage_table ) {
			return false;
		}
		$table		=	JCckTable::getInstance( $item->storage_table, 'id' );
		$table->load( $pk );
		
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
		// -- onContentBeforeDelete?
		if ( !$table->delete( $pk ) ) {
			return false;
		}

		// Delete Core
		if ( $item->id ) {
			$table	=	JCckTable::getInstance( '#__cck_core', 'id', $item->id );
			$table->delete();
		}

		// Delete More
		$base		=	str_replace( '#__', '', $item->storage_table );
		$tables		=	JCckDatabase::loadColumn( 'SHOW TABLES' );
		$prefix		= 	JFactory::getConfig()->get( 'dbprefix' );

		if ( in_array( $prefix.'cck_store_item_'.$base, $tables ) ) {
			$table	=	JCckTable::getInstance( '#__cck_store_item_'.$base, 'id', $pk );
			if ( $table->id ) {
				$table->delete();
			}
		}
		if ( in_array( $prefix.'cck_store_form_'.$item->type, $tables ) ) {
			$table	=	JCckTable::getInstance( '#__cck_store_form_'.$item->type, 'id', $pk );
			if ( $table->id ) {
				$table->delete();
			}
		}
		// -- onContentAfterDelete?
		
		return true;
	}
	
	// onCCK_Storage_LocationSearch
	public function onCCK_Storage_LocationSearch( $type, $tables, $fields, $fields_order, &$config, &$inherit, &$results )
	{
		if ( self::$type != $type ) {
			return;
		}

		if ( $config['doQuery'] === false && isset( $config['query'] ) && $config['query'] ) {
			if ( isset( $config['query_variables'] ) && count( $config['query_variables'] ) ) {
				foreach ( $config['query_variables'] as $var ) {
					if ( $var != '' ) {
						JCckDatabase::execute( $var );
					}
				}
			}
			$results			=	JCckDatabase::loadObjectList( $config['query'] );
			$inherit['query']	=	$config['query'];

			unset( $config['query'] );
		}
	}
	
	// onCCK_Storage_LocationStore
	public function onCCK_Storage_LocationStore( $type, $data, &$config = array(), $pk = 0 )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		if ( $data['_']->table == '#__cck_core' ) {
			// ...
		} else {
			if ( ! self::$pk ) {
				// Init
				$table	=	self::_getTable( $pk, $data['_']->table, $config );
				$isNew	=	( $pk > 0 ) ? false : true;
				self::_initTable( $table, $data, $config );
				
				// Check Error
				if ( self::$error === true ) {
					$config['error']	=	true;

					return false;
				}
				
				if ( $config['join'] && $config['pk'] > 0 ) {
					if ( ! $data[$config['join']] ) {
						$data[$config['join']]	=	$config['pk'];
					}
				}
				
				// Prepare
				if ( is_array( $data ) ) {
					$table->bind( $data );
				}
				$table->check();
				self::_completeTable( $table, $data, $config );
				
				// Store
				if ( $isNew === true && parent::g_isMax( JFactory::getUser()->id, 0, $config ) ) {
					$config['error']	=	true;

					return false;
				}
				if ( !$table->store() ) {
					JFactory::getApplication()->enqueueMessage( $table->getError(), 'error' );
					
					if ( $isNew ) {
						parent::g_onCCK_Storage_LocationRollback( $config['id'] );
					}
					$config['error']	=	true;

					return false;
				}
				
				self::$pk	=	$table->{self::$key};
				if ( ! $config['pk'] ) {
					$config['pk']	=	self::$pk;
				}
				
				if ( $config['join'] ) {
					self::_core( $data, $config );
				} else {
					if ( ! isset( $config['primary'] ) && self::$pk ) {
						self::_core( $data, $config );
					}
				}
			}
		}
		
		return self::$pk;
	}
	
	// _core
	protected function _core( $data, $config = array() )
	{
		$core					=	JCckTable::getInstance( '#__cck_core', 'id' );
		$core->load( $config['id'] );
		$core->cck				=	$config['type'];
		if ( ! $core->pk ) {
			$core->date_time	=	JFactory::getDate()->toSql();
		}
		$core->pk				=	self::$pk;
		$core->storage_location	=	self::$type;
		$core->storage_table	=	$data['_']->table;
		$core->author_id		=	( $config['author'] ) ? $config['author'] : JFactory::getUser()->id;
		$core->storeIt();
	}
	
	// _getTable
	protected static function _getTable( $pk = 0, $table = '', &$config = array() )
	{
		$table	=	JCckTable::getInstance( $table, 'id' );
		$pk		=	( $pk ) ? $pk : $config['pk'];		

		if ( $pk > 0 ) {
			$table->load( $pk, true );
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
	}
	
	// getRoute
	public static function getRoute( $item, $sef, $itemId, $config = array() )
	{
		return '';
	}
	
	// getRouteByStorage
	public static function getRouteByStorage( &$storage, $sef, $itemId, $config = array() )
	{
		return '';
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

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// checkIn
	public static function checkIn( $pk = 0 )
	{
		return true;
	}
	
	// getId
	public static function getId( $config )
	{
		return JCckDatabase::loadResult( 'SELECT id FROM #__cck_core WHERE storage_location="'.self::$type.'" AND storage_table="'.(string)$config['base']->table.'" AND pk='.(int)$config['pk'] );
	}
}
?>