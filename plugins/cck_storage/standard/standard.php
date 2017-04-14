<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: standard.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
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
	
	// onCCK_StoragePrepareContent_Xi
	public function onCCK_StoragePrepareContent_Xi( &$field, &$value, &$storage, $x = '', $xi = 0 )
	{
		if ( self::$type != $field->storage ) {
			return;
		}
		self::onCCK_StoragePrepareForm( $field, $value, $storage );
	}

	// onCCK_StoragePrepareDelete
	public function onCCK_StoragePrepareDelete( &$field, &$value, &$storage, $config = array() )
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

	// onCCK_StoragePrepareDownload
	public function onCCK_StoragePrepareDownload( &$field, &$value, &$config = array() )
	{
		if ( self::$type != $field->storage ) {
			return;
		}
		
		// Set
		$value	=	$field->value;
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
	public static function onCCK_StoragePrepareSearch( &$field, $match, $value, $name, $name2, $target, $fields = array(), &$config = array() )
	{
		$sql	=	'';
		
		switch ( $match ) {
			case 'exact':
				$var_type	=	( $field->match_options ) ? $field->match_options->get( 'var_type', 1 ) : 1;
				if ( !$var_type ) {
					$sql	=	$target.' = '.JCckDatabase::clean( $value );
				} else {
					$sql	=	$target.' = '.JCckDatabase::quote( $value );
				}
				break;
			case 'empty':
				$sql	=	$target.' = ""';
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
					$var_mode	=	( $field->match_options ) ? $field->match_options->get( 'var_mode', '0' ) : '0';
					$var_type	=	( $field->match_options ) ? $field->match_options->get( 'var_type', 1 ) : 1;
					if ( $var_mode == '1' ) {
						foreach ( $values as $v ) {
							if ( strlen( $v ) > 0 ) {
								$fragments[] 	=	$target.' = '.JCckDatabase::quote( $v )
												.	' OR '.$target.' LIKE '.JCckDatabase::quote( JCckDatabase::escape( $v, true ).$separator.'%', false )
												.	' OR '.$target.' LIKE '.JCckDatabase::quote( '%'.$separator.JCckDatabase::escape( $v, true ).$separator.'%', false )
												.	' OR '.$target.' LIKE '.JCckDatabase::quote( '%'.$separator.JCckDatabase::escape( $v, true ), false );
							}
						}
						if ( count( $fragments ) ) {
							$sql	=	'((' . implode( ') OR (', $fragments ) . '))';
						}
					} else {
						if ( !$var_type ) {
							foreach ( $values as $v ) {
								if ( strlen( $v ) > 0 ) {
									$fragments[] 	=	JCckDatabase::clean( $v );
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
				}
				break;
			case 'each':
			case 'each_exact':
				$separator	=	( $field->match_value ) ? $field->match_value : ' ';
				$values		=	explode( $separator, $value );
				$count		=	count( $values );
				if ( $count ) {
					$fragments	=	array();
					$var_count	=	( $field->match_options ) ? $field->match_options->get( 'var_count', '' ) : '';
					if ( $match == 'each_exact' ) {
						foreach ( $values as $v ) {
							if ( strlen( $v ) > 0 ) {
								$fragment		=	'';

								if ( $count == 1 ) {
									$fragment 	.=	$target.' = '.JCckDatabase::quote( $v ).' OR ';
								}
								$fragment		.=	$target.' LIKE '.JCckDatabase::quote( JCckDatabase::escape( $v, true ).$separator.'%', false )
												.	' OR '.$target.' LIKE '.JCckDatabase::quote( '%'.$separator.JCckDatabase::escape( $v, true ).$separator.'%', false )
												.	' OR '.$target.' LIKE '.JCckDatabase::quote( '%'.$separator.JCckDatabase::escape( $v, true ), false );
								$fragments[] 	=	$fragment;
							}
						}
					} else {
						foreach ( $values as $v ) {
							if ( strlen( $v ) > 0 ) {
								$fragments[] 	=	$target.' LIKE '.JCckDatabase::quote( '%'.JCckDatabase::escape( $v, true ).'%', false );
							}
						}
					}
					if ( count( $fragments ) ) {
						$sql	=	'((' . implode( ') AND (', $fragments ) . '))';
					}
					if ( $var_count != '' ) {
						if ( (int)$var_count == 0 || (int)$var_count == 1 ) {
							$idx	=	'diff_'.$field->name;
							$offset	=	( $field->match_options && (int)$var_count == 1 ) ? $field->match_options->get( 'var_count_offset', '' ) : '';

							if ( !isset( $config['query_parts'] ) ) {
								$config['query_parts']	=	array();
							}
							if ( !isset( $config['query_parts']['select'] ) ) {
								$config['query_parts']['select']	=	array();
							}
							if ( !isset( $config['query_parts']['having'] ) ) {
								$config['query_parts']['having']	=	array();
							}
							$config['query_parts']['select'][]		=	'LENGTH('.$target.') - LENGTH(REPLACE('.$target.',"'.$separator.'","")) AS '.$idx;
							$config['query_parts']['having'][]		=	$idx.' = '.( $count - 1 + (int)$offset );
						}
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
				if ( $sql == '' ) {
					$sql	=	$target.' IN (0)';
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
					$var_type	=	( $field->match_options ) ? $field->match_options->get( 'var_type', 1 ) : 1;
					if ( !$var_type ) {
						foreach ( $values as $v ) {
							if ( strlen( $v ) > 0 ) {
								$fragments[] 	=	JCckDatabase::clean( $v );
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
				$var_type	=	( $field->match_options ) ? $field->match_options->get( 'var_type', 1 ) : 1;
				if ( !$var_type ) {
					$sql	=	$target.' != '.JCckDatabase::clean( $value );
				} else {
					$sql	=	$target.' != '.JCckDatabase::quote( $value );
				}
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
			case 'radius_higher':
			case 'radius_lower':
				$t			=	explode( '.', $target );
				$f_lat		=	$field->match_options->get( 'fieldname1', '' );
				$f_lng		=	$field->match_options->get( 'fieldname2', '' );
				$f_rad		=	$field->match_options->get( 'fieldname3', '' );
				$lat		=	( isset( $fields[$f_lat] ) ) ? $fields[$f_lat]->value : '';
				$lng		=	( isset( $fields[$f_lng] ) ) ? $fields[$f_lng]->value : '';
				$s_lat		=	( isset( $fields[$f_lat]->storage_field ) && $fields[$f_lat]->storage_field ) ? $fields[$f_lat]->storage_field : $f_lat;
				$s_lng		=	( isset( $fields[$f_lng]->storage_field ) && $fields[$f_lng]->storage_field ) ? $fields[$f_lng]->storage_field : $f_lng;
				if ( $lat != '' && $lng != '' ) {
					$alias		=	'distance';
					$mod		=	( $field->match_options->get( 'var_unit', '1' ) ) ? '' : '*1.609344';
					$radius		=	( isset( $fields[$f_rad] ) ) ? $fields[$f_rad]->value : '';
					$sign		=	( $match == 'radius_higher' ) ? '>' : '<';
					$config['query_parts']['select'][]	=	'(((acos(sin(('.(float)$lat.'*pi()/180)) * sin(('.$t[0].'.'.$s_lat.'*pi()/180))+cos(('.(float)$lat.'*pi()/180)) * cos(('.$t[0].'.'.$s_lat.'*pi()/180)) * cos((('.(float)$lng.'- '.$t[0].'.'.$s_lng.')*pi()/180))))*180/pi())*60*1.1515'.$mod.') AS '.$alias;						
					if ( (int)$radius > 0 ) {
						$config['query_parts']['having'][]	=	$alias.' '.$sign.' '.$radius;
						$sql		=	'()'; // todo
					} else {
						$lat		=	number_format( $lat, 8 );
						$lng		=	number_format( $lng, 8 );
						$sql		=	'('.$t[0].'.'.$s_lat.' = '.JCckDatabase::quote( $lat ).' AND '.$t[0].'.'.$s_lng.' = '.JCckDatabase::quote( $lng ).')';
					}
				} else {
					$sql			=	'()'; // todo
				}
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