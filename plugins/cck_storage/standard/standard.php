<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: standard.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_StorageStandard extends JCckPluginStorage
{
	protected static $type	=	'standard';
	protected static $list	=	array();
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_StoragePrepareContent
	public function onCCK_StoragePrepareContent( &$field, &$value, &$storage )
	{
		if ( self::$type != $field->storage ) {
			return;
		}
		
		// Init
		$P		=	$field->storage_field;
		
		// Set
		if ( isset( $storage->$P ) ) {
			$value	=	$storage->$P;
		}
	}
	
	// onCCK_StoragePrepareForm
	public function onCCK_StoragePrepareForm( &$field, &$value, &$storage, $config = array() )
	{
		if ( self::$type != $field->storage ) {
			return;
		}
		
		// Init
		$P		=	$field->storage_field;
		
		// Set
		if ( isset( $storage->$P ) ) {
			$value	=	$storage->$P;
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
		$sql	=	'';
		
		switch ( $match ) {
			case 'exact':
				$sql	=	$target.' = '.JCckDatabase::quote( $value );
				break;
			case 'alpha':
				$sql	=	$target.' LIKE '.JCckDatabase::quote( JCckDatabase::escape( $value, true ).'%', false );
				break;
			case 'zeta': /* Zeta is not the last letter of Greek alphabet but.. this won't be an issue here. */
				$sql	=	$target.' LIKE '.JCckDatabase::quote( '%'.JCckDatabase::escape( $value, true ), false );
				break;
			case 'any':
				$separator	=	( $field->match_value ) ? $field->match_value : ' ';
				$values		=	explode( $separator, $value );
				if ( count( $values ) ) {
					$fragments	=	array();
					foreach ( $values as $v ) {
						if ( strlen( $v ) > 0 ) {
							$fragments[] 	=	$target.' LIKE '.JCckDatabase::quote( '%'.JCckDatabase::escape( $v, true ).'%', false );
						}
					}
					if ( count( $fragments ) ) {
						$sql	=	'((' . implode( ') OR (', $fragments ) . '))';
					}
				}
				break;
			case 'any_exact':
				$separator	=	( $field->match_value ) ? $field->match_value : ' ';
				$values		=	explode( $separator, $value );
				if ( count( $values ) ) {
					$fragments	=	array();
					$var_type	=	( $field->match_options ) ? $field->match_options->get( 'var_type', 1 ) : 1;
					if ( !$var_type ) {
						foreach ( $values as $v ) {
							if ( strlen( $v ) > 0 ) {
								$fragments[] 	=	$v;
							}
						}
					} else {
						foreach ( $values as $v ) {
							if ( strlen( $v ) > 0 ) {
								$fragments[] 	=	JCckDatabase::quote( $v );
							}
						}
					}
					if ( count( $fragments ) ) {
						$sql	=	$target.' IN ('.implode( ',', $fragments ).')';
					}
				}
				break;
			case 'each':
				$separator	=	( $field->match_value ) ? $field->match_value : ' ';
				$values		=	explode( $separator, $value );
				if ( count( $values ) ) {
					$fragments	=	array();
					foreach ( $values as $v ) {
						if ( strlen( $v ) > 0 ) {
							$fragments[] 	=	$target.' LIKE '.JCckDatabase::quote( '%'.JCckDatabase::escape( $v, true ).'%', false );
						}
					}
					if ( count( $fragments ) ) {
						$sql	=	'((' . implode( ') AND (', $fragments ) . '))';
					}
				}
				break;
			case 'date_past_only':
				$sql	=	$target.' < '.JCckDatabase::quote( $value );
				break;
			case 'date_past':
				$sql	=	$target.' <= '.JCckDatabase::quote( $value );
				break;
			case 'date_future':
				$sql	=	$target.' >= '.JCckDatabase::quote( $value );
				break;
			case 'date_future_only':
				$sql	=	$target.' > '.JCckDatabase::quote( $value );
				break;
			case 'nested_exact':
				$table		=	( $field->match_options ) ? $field->match_options->get( 'table', $field->storage_table ) : $field->storage_table;
				$column		=	'id';
				$values		=	JCckDevHelper::getBranch( $table, $value );
				if ( $column != 'id' ) {
					if ( count( $values ) ) {
						$fragments	=	array();
						foreach ( $values as $v ) {
							if ( $v != '' ) {
								$fragments[] 	=	JCckDatabase::quote( $v );
							}
						}
						if ( count( $fragments ) ) {
							$sql	=	$target.' IN (' . implode( ',', $fragments ) . ')';
						}
					}
				} else {
					if ( count( $values ) ) {
						$sql	=	$target.' IN (' . implode( ',', $values ) . ')';
					}
				}
				break;
			case 'num_higher':
				$sql	=	$target.' >= '.JCckDatabase::quote( $value );
				break;
			case 'num_higher_only':
				$sql	=	$target.' > '.JCckDatabase::quote( $value );
				break;
			case 'num_lower':
				$sql	=	$target.' <= '.JCckDatabase::quote( $value );
				break;
			case 'num_lower_only':
				$sql	=	$target.' < '.JCckDatabase::quote( $value );
				break;
			case 'not_alpha':
				$sql	=	$target.' NOT LIKE '.JCckDatabase::quote( JCckDatabase::escape( $value, true ).'%', false );
				break;
			case 'not_any_exact':
				$separator	=	( $field->match_value ) ? $field->match_value : ' ';
				$values		=	explode( $separator, $value );
				if ( count( $values ) ) {
					$fragments	=	array();
					foreach ( $values as $v ) {
						if ( strlen( $v ) > 0 ) {
							$fragments[] 	=	JCckDatabase::quote( $v );
						}
					}
					if ( count( $fragments ) ) {
						$sql	=	$target.' NOT IN (' . implode( ',', $fragments ) . ')';
					}
				}
				break;
			case 'not_zeta': /* Zeta is not the last letter of Greek alphabet but.. this won't be an issue here. */
				$sql	=	$target.' NOT LIKE '.JCckDatabase::quote( '%'.JCckDatabase::escape( $value, true ), false );
				break;
			case 'not_empty':
				$sql	=	$target.' != ""';
				break;
			case 'not_equal':
				$sql	=	$target.' != '.JCckDatabase::quote( $value );
				break;
			case 'not_like':
				$sql	=	$target.' NOT LIKE '.JCckDatabase::quote( '%'.JCckDatabase::escape( $value, true ).'%', false );
				break;
			case 'not_null':
				$sql	=	$target.' != "0"';
				break;
			case 'is_null':
				$sql	=	$target.' IS NULL';
				break;
			case 'is_not_null':
				$sql	=	$target.' IS NOT NULL';
				break;
			case 'none':
				return;
				break;
			default:
				$sql	=	$target.' LIKE '.JCckDatabase::quote( '%'.JCckDatabase::escape( $value, true ).'%', false );
				break;
		}
		
		return $sql;
	}
	
	// onCCK_StoragePrepareStore
	public static function onCCK_StoragePrepareStore( &$field, $value, &$config = array() )
	{
		// Prepare
		if ( is_array( $value ) ) {
			$store	=	$value;
		} else {
			$store	=	' '.$value;
		}
		
		parent::g_onCCK_StoragePrepareStore( $field, $store, $config );
	}
	
	// onCCK_StoragePrepareStore_X
	public static function onCCK_StoragePrepareStore_X( &$field, $value, $value2, &$config = array() )
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
	}
	
	// onCCK_StorageAfterStore
	public static function onCCK_StorageAfterStore( $process, &$fields, &$storages )
	{
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// _format
	public static function _format( $name, $value, &$config = array() )
	{
		return $value;
	}
	
	// _replace
	public static function _replace( $name, $value, $value_old, $string, &$config = array() )
	{
		return $value;
	}
}
?>