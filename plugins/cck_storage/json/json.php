<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: json.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_StorageJson extends JCckPluginStorage
{
	protected static $type	=	'json';
	protected static $list	=	array();
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_StoragePrepareContent
	public function onCCK_StoragePrepareContent( &$field, &$value, &$storage )
	{
		if ( self::$type != $field->storage ) {
			return;
		}
		parent::g_onCCK_StoragePrepareContent( $field, $config );
		
		// Init
		$P	=	$field->storage_field;
		
		// Prepare
		if ( ! isset( $storage->values[$P] ) ) {
			$storage->values[$P]	=	( isset( $storage->$P ) ) ? self::_initValues( $storage->$P ) : array();
		}
		
		// Set
		if ( !$field->storage_field2 ) {
			$value	=	$storage->values[$P];
		} else {
			if ( isset( $storage->values[$P][$field->storage_field2] ) ) {
				$value	=	$storage->values[$P][$field->storage_field2];
				if ( is_array( $value ) && isset( $field->storage_field3 ) ) {
					$value	=	$value[$field->storage_field3];
				}
			}
		}
	}

	// onCCK_StoragePrepareContent_Xi
	public function onCCK_StoragePrepareContent_Xi( &$field, &$value, &$storage, $x = '', $xi = 0 )
	{
		if ( self::$type != $field->storage ) {
			return;
		}
		self::onCCK_StoragePrepareForm( $field, $value, $storage );
	}

	// onCCK_StoragePrepareDelete
	public function onCCK_StoragePrepareDelete( &$field, &$value, &$storage )
	{
		if ( self::$type != $field->storage ) {
			return;
		}
		parent::g_onCCK_StoragePrepareContent( $field, $config );
		
		// Init
		$P	=	$field->storage_field;
		
		// Prepare
		if ( ! isset( $storage->values[$P] ) ) {
			$storage->values[$P]	=	( isset( $storage->$P ) ) ? self::_initValues( $storage->$P ) : array();
		}
		
		// Set
		if ( isset( $storage->values[$P][$field->storage_field2] ) ) {
			$value	=	$storage->values[$P][$field->storage_field2];
			if ( is_array( $value ) && isset( $field->storage_field3 ) ) {
				$value	=	$value[$field->storage_field3];
			}
		}
	}

	// onCCK_StoragePrepareDownload
	public function onCCK_StoragePrepareDownload( &$field, &$value, &$config = array() )
	{
		if ( self::$type != $field->storage ) {
			return;
		}
		
		// Prepare
		if ( $config['collection'] != '' ) {
			$matches	=	json_decode( $field->value, true );
			$P			=	$field->storage_field; /* Guess: it should be the $field->name? */
			
			if ( $field->storage_field2 != '' && $field->storage_field2 != $P ) {
				$P		=	$field->storage_field2;
			}
			$value		=	$matches[$P][$config['xi']];
		} else {
			$matches	=	json_decode( $field->value, true );
			$P			=	$field->storage_field2;

			if ( $P == '' ) {
				$P		=	$field->name;
			}
			if ( isset( $matches[$P] ) ) {
				$value	=	$matches[$P];
				if ( is_array( $value ) && isset( $field->storage_field3 ) ) {
					$value	=	$value[$field->storage_field3];
				}
			}
		}
	}
	
	// onCCK_StoragePrepareForm
	public function onCCK_StoragePrepareForm( &$field, &$value, &$storage, $config = array() )
	{
		if ( self::$type != $field->storage ) {
			return;
		}
		parent::g_onCCK_StoragePrepareForm( $field, $config );
		
		// Init
		$P	=	$field->storage_field;
		
		// Prepare
		if ( ! isset( $storage->values[$P] ) ) {
			$storage->values[$P]	=	( isset( $storage->$P ) ) ? self::_initValues( $storage->$P ) : array();
		}
		
		// Set
		if ( !$field->storage_field2 ) {
			$value	=	$storage->values[$P];
		} else {
			if ( isset( $storage->values[$P][$field->storage_field2] ) ) {
				$value	=	$storage->values[$P][$field->storage_field2];
				if ( is_array( $value ) && isset( $field->storage_field3 ) ) {
					$value	=	@$value[$field->storage_field3];
				}
			}
		}
	}
	
	// onCCK_StoragePrepareForm_Xi
	public function onCCK_StoragePrepareForm_Xi( &$field, &$value, &$storage, $x = '', $xi = 0 )
	{
		if ( self::$type != $field->storage ) {
			return;
		}
		self::onCCK_StoragePrepareForm( $field, $value, $storage );
	}
	
	// onCCK_StoragePrepareSearch
	public static function onCCK_StoragePrepareSearch( &$field, $match, $value, $name, $name2, $target )
	{		
		return;
	}
	
	// onCCK_StoragePrepareStore
	public static function onCCK_StoragePrepareStore( &$field, $value, &$config = array() )
	{
		// Prepare
		if ( strpos( $field->storage_field2, '|' ) !== false ) {
			$levels	=	explode( '|', $field->storage_field2 );
			for ( $i = 0, $n = count( $levels ); $i < $n; $i++ ) {
				$field->{'storage_field'.($i + 2)}	=	$levels[$i];
			}
			$value	=	array( $field->storage_field3=>$value );
			$value	=	json_encode( $value );
		} elseif ( is_array( $value ) ) {
			$value	=	json_encode( $value );
		} else {
			if ( $value != '' ) {
				$value	=	addcslashes( $value, "\"\n\r\\" );
			}
			$value	=	'"'.$value.'"';
		}
		$store	=	'"'.$field->storage_field2.'":'.$value.',';
		
		// Add Process
		if ( ! isset( self::$list[$field->storage_field] ) ) {
			parent::g_addProcess( 'beforeStore', self::$type, $config, array( 's_table' => $field->storage_table, 's_field' => $field->storage_field ) );
			self::$list[$field->storage_field]	=	1;
		}
		
		// Set
		parent::g_onCCK_StoragePrepareStore( $field, $store, $config );
	}
	
	// onCCK_StoragePrepareStore_X
	public static function onCCK_StoragePrepareStore_X()
	{
	}
	
	// onCCK_StoragePrepareStore_Xi
	public static function onCCK_StoragePrepareStore_Xi()
	{
	}
	
	//onCCK_StoragePrepareImport
	public static function onCCK_StoragePrepareImport( $field, $value, &$config = array() )
	{
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// onCCK_StorageBeforeStore
	public static function onCCK_StorageBeforeStore( $process, &$fields, &$storages )
	{
		// Set
		$idx1	=	$process['s_table'];
		$idx2	=	$process['s_field'];
		if ( $idx1 && $idx2 ) {
			if ( is_array( $storages[$idx1][$idx2] ) ) {
				$storages[$idx1][$idx2]	=	json_encode( $storages[$idx1][$idx2] );
			} else {
				$storages[$idx1][$idx2]	=	'{' . substr( $storages[$idx1][$idx2], 0, -1 ) . '}';
			}
		}
	}
	
	// onCCK_StorageAfterStore
	public static function onCCK_StorageAfterStore( $process, &$fields, &$storages )
	{
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// _format
	public static function _format( $name, $value, &$config = array() )
	{
		return '"'.$name.'":"'.$value.'",';
	}
	
	// _initValues
	protected function _initValues( $value )
	{
		$values	=	JCckDev::fromJSON( $value );
		
		return $values;
	}
	
	// replace
	public static function _replace( $name, $value, $value_old, $string, &$config = array() )
	{
		return str_replace( self::_format( $name, JCckDatabase::escape( $value_old ) ), self::_format( $name, JCckDatabase::escape( $value ) ), $string );
	}
}
?>