<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Storage_Location%class% extends JCckPluginLocation
{
	protected static $type			=	'%name%';
	protected static $table			=	'#__'; // TODO
	protected static $table_object	=	array( '', '' ); // TODO
	protected static $key			=	'id'; // TODO
	
	protected static $access		=	'access'; // TODO
	protected static $author		=	''; // TODO
	protected static $author_object	=	''; // TODO
	protected static $created_at	=	''; // TODO
	protected static $custom		=	'description'; // TODO
	protected static $modified_at	=	''; // TODO
	protected static $parent		=	''; // TODO
	protected static $parent_object	=	''; // TODO
	protected static $status		=	'state'; // TODO
	protected static $to_route		=	'a.id as pk, a.title, a.alias'; // TODO
	
	protected static $context		=	''; //TODO
	protected static $contexts		=	array(); //TODO
	protected static $error			=	false;
	protected static $ordering		=	array( 'alpha'=>'title ASC' ); // TODO
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
			// TODO
		} else {
			$storage	=	parent::g_onCCK_Storage_LocationPrepareContent( $table, $pk );
			if ( ! isset( $config['storages'][self::$table] ) ) {
				// TODO
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
			// TODO
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
		$config['author']	=	$storages[self::$table][$config['pk']]->{self::$author};
	}

	// onCCK_Storage_LocationPrepareList
	public static function onCCK_Storage_LocationPrepareList( &$params )
	{
		// TODO
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
		// TODO
		
		// Set
		// TODO
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// onCCK_Storage_LocationDelete
	public static function onCCK_Storage_LocationDelete( $pk, &$config = array() )
	{
		// TODO
		
		return false;
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
		self::_initTable( $table, $data, $config );
		
		// Check Error
		if ( self::$error === true ) {
			return false;
		}
		
		// Prepare
		$table->bind( $data );
		$table->check();
		self::_completeTable( $table, $data, $config );
		
		// Store
		$dispatcher	=	JEventDispatcher::getInstance();
		$table->store();
		
		// Checkin
		// parent::g_checkIn( $table );
		self::$pk	=	$table->{self::$key};
		if ( !$config['pk'] ) {
			$config['pk']	=	self::$pk;
		}
		
		$config['author']	=	$table->{self::$author}; // TODO
		$config['parent']	=	$table->{self::$parent}; // TODO
		
		parent::g_onCCK_Storage_LocationStore( $data, self::$table, self::$pk, $config );
	}
	
	// _getTable
	protected static function _getTable( $pk = 0 )
	{
		// TODO
		$table	=	JTable::getInstance( /* TODO */ );
		
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
			// TODO
		}
		// TODO
		//$table->{self::$custom}	=	'';
	}
	
	// _completeTable
	protected function _completeTable( &$table, &$data, &$config )
	{
		if ( ! $table->{self::$key} ) {
			// TODO
		}
		// TODO
		
		parent::g_completeTable( $table, self::$custom, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // SEF

	// buildRoute
	public static function buildRoute( &$query, &$segments, $config, $menuItem = NULL )
	{
	}
	
	// getRoute
	public static function getRoute( $item, $sef, $itemId, $config = array() )
	{
		$route		=	'';
		// TODO
		
		return JRoute::_( $route );
	}
	
	// getRouteByStorage
	public static function getRouteByStorage( &$storage, $sef, $itemId, $config = array() )
	{
		if ( isset( $storage[self::$table]->_route ) ) {
			return JRoute::_( $storage[self::$table]->_route );
		}
		
		if ( $sef ) {
			$storage[self::$table]->_route	=	''; // TODO
		} else {
			$storage[self::$table]->_route	=	''; // TODO
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
		$link	=	'';
		
		// TODO
		
		return $link;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
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
}
?>