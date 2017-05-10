<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: custom.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JLoader::register( 'CCK_Content', JPATH_PLATFORM.'/cck/content/content.php' );

// Plugin
class plgCCK_StorageCustom extends JCckPluginStorage
{
	protected static $type	=	'custom';
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
			$storage->values[$P]	=	( isset( $storage->$P ) ) ? self::_initValues( $storage->$P ) : array( 0=>array(), 1=>array(), 2=>array(), 3=>array() );
		}
		
		// Set
		if ( ( $k = array_search( $field->storage_field2, $storage->values[$P][1] ) ) !== false ) {
			$value	=	$storage->values[$P][2][$k];
		}
	}

	// onCCK_StoragePrepareContent_Xi
	public function onCCK_StoragePrepareContent_Xi( &$field, &$value, &$storage, $x = '', $xi = 0 )
	{
		if ( self::$type != $field->storage ) {
			return;
		}
		parent::g_onCCK_StoragePrepareForm( $field, $config );
		
		// Init
		$P	=	$field->storage_field;
		
		// Prepare
		if ( ! isset( $storage->values[$P] ) ) {
			$storage->values[$P]	=	self::_initValues( $storage->$P );
		}
		
		// Set
		if ( ( $k = array_search( $field->storage_field2.'|'.($xi).'|'.$x, $storage->values[$P][1] ) ) !== false ) {
			$value	=	$storage->values[$P][2][$k];
		}
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
			$storage->values[$P]	=	( isset( $storage->$P ) ) ? self::_initValues( $storage->$P ) : array( 0=>array(), 1=>array(), 2=>array(), 3=>array() );
		}
		
		// Set
		if ( ( $k = array_search( $field->storage_field2, $storage->values[$P][1] ) ) !== false ) {
			$value	=	$storage->values[$P][2][$k];
		}
	}

	// onCCK_StoragePrepareDownload
	public function onCCK_StoragePrepareDownload( &$field, &$value, &$config = array() )
	{
		if ( self::$type != $field->storage ) {
			return;
		}

		// Init

		// Set
		if ( $config['collection'] != '' ) {
			$regex	=	CCK_Content::getRegex_Group( $config['fieldname'], $config['collection'], $config['xi'] );			
			preg_match( $regex, $field->value, $matches );
			$value	=	$matches[1];
		} else {
			if ( is_object( $field ) && $field->storage_field2 ) {
				$config['fieldname']	=	$field->storage_field2;
			}
			$regex	=	CCK_Content::getRegex_Field( $config['fieldname'] );
			preg_match( $regex, $field->value, $matches );
			$value	=	$matches[1];
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
			$storage->values[$P]	=	( isset( $storage->$P ) ) ? self::_initValues( $storage->$P ) : array( 0=>array(), 1=>array(), 2=>array(), 3=>array() );
			if ( $P == $config['custom'] ) {
				if ( ( $k = array_search( 'cck', $storage->values[$P][1] ) ) !== false ) {
					$config['id']	=	$storage->values[$P][2][$k];
				}
			}
		}
		
		// Set
		if ( ( $k = array_search( $field->storage_field2, $storage->values[$P][1] ) ) !== false ) {
			$value	=	$storage->values[$P][2][$k];
		}
	}
	
	// onCCK_StoragePrepareForm_Xi
	public function onCCK_StoragePrepareForm_Xi( &$field, &$value, &$storage, $x = '', $xi = 0 )
	{
		if ( self::$type != $field->storage ) {
			return;
		}
		parent::g_onCCK_StoragePrepareForm( $field, $config );
		
		// Init
		$P	=	$field->storage_field;
		
		// Prepare
		if ( ! isset( $storage->values[$P] ) ) {
			$storage->values[$P]	=	self::_initValues( $storage->$P );
		}
		
		// Set
		if ( ( $k = array_search( $field->storage_field2.'|'.($xi).'|'.$x, $storage->values[$P][1] ) ) !== false ) {
			$value	=	$storage->values[$P][2][$k];
		}
	}
	
	// onCCK_StoragePrepareSearch
	public static function onCCK_StoragePrepareSearch( &$field, $match, $value, $name, $name2, $target )
	{
		$sql	=	'';
		$TA		=	'(::'.$name.$name2.'::)';
		$TZ		=	'(::/'.$name.$name2.'::)';
		//if ( !empty( $field->match_target ) && $field->match_target != '~' ) {
		//	$target	=	self::_getTarget( $name, $field->match_target );
		//	$TA		=	'';
		//	$TZ		=	'';
		//}
		
		switch ( $match ) {
			case 'exact':
				$sql		=	( !$TA ) ? $target.' = "'.$TA.JCckDatabase::escape( $value ).$TZ.'"' : $target.' REGEXP "'.$TA.JCckDatabase::escape( $value ).$TZ.'"';
				break;
			case 'empty':
				$sql		=	$target.' REGEXP "'.$TA.$TZ.'"';
				break;
			case 'alpha':
				$sql		=	$target.' REGEXP "'.$TA.JCckDatabase::escape( $value ).'.*'.$TZ.'"';
				break;
			case 'zeta': /* Zeta is not the last letter of Greek alphabet but.. this won't be an issue here. */
				$sql		=	$target.' REGEXP "'.$TA.'.*'.JCckDatabase::escape( $value ).$TZ.'"';
				break;
			case 'any':
				$separator	=	( $field->match_value ) ? $field->match_value : ' ';
				$values		=	explode( $separator, $value );
				if ( count( $values ) ) {
					$fragments	=	array();
					foreach ( $values as $v ) {
						if ( strlen( $v ) > 0 ) {
							$fragments[]	=	$target.' REGEXP "'.$TA.'.*'.JCckDatabase::escape( $v ).'.*'.$TZ.'"';
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
					foreach ( $values as $v ) {
						if ( strlen( $v ) > 0 ) {
							$fragments[]	=	( !$TA ) ? $target.' = "'.$TA.JCckDatabase::escape( $v ).$TZ.'"' : $target.' REGEXP "'.$TA.JCckDatabase::escape( $v ).$TZ.'"';
						}
					}
					if ( count( $fragments ) ) {
						$sql	=	'((' . implode( ') OR (', $fragments ) . '))';
					}
				}
				break;
			case 'each':
			case 'each_exact':
				$separator	=	( $field->match_value ) ? $field->match_value : ' ';
				$values		=	explode( $separator, $value );
				if ( count( $values ) ) {
					$fragments	=	array();
					if ( $match == 'each_exact' ) {
						foreach ( $values as $v ) {
							if ( strlen( $v ) > 0 ) {
								$fragments[]	=	( ( !$TA ) ? $target.' = "'.$TA.JCckDatabase::escape( $v ).$TZ.'"' : $target.' REGEXP "'.$TA.JCckDatabase::escape( $v ).$TZ.'"' )
												.	$target.' REGEXP "'.$TA.JCckDatabase::escape( $v ).$separator.'.*'.$TZ.'"'
												.	$target.' REGEXP "'.$TA.'.*'.$separator.JCckDatabase::escape( $v ).$separator.'.*'.$TZ.'"'
												.	$target.' REGEXP "'.$TA.'.*'.$separator.JCckDatabase::escape( $v ).$TZ.'"';
							}
						}
					} else {
						foreach ( $values as $v ) {
							if ( strlen( $v ) > 0 ) {
								$fragments[]	=	$target.' REGEXP "'.$TA.'.*'.JCckDatabase::escape( $v ).'.*'.$TZ.'"';
							}
						}
					}
					if ( count( $fragments ) ) {
						$sql	=	'((' . implode( ') AND (', $fragments ) . '))';
					}
				}
				break;
			case 'date_past_only':
			case 'date_past':
			case 'date_future':
			case 'date_future_only':
				JFactory::getApplication()->enqueueMessage( JText::_( 'COM_CCK_DATE_AND_TIME_MATCH_ONLY_WITH_STANDARD' ), 'notice' );
				break;
			case 'nested_exact':
				$table		=	( $field->match_options ) ? $field->match_options->get( 'table', $field->storage_table ) : $field->storage_table;
				$values		=	JCckDevHelper::getBranch( $table, $value );
				if ( count( $values ) ) {
					$fragments	=	array();
					foreach ( $values as $v ) {
						if ( $v != '' ) {
							$fragments[]	=	( !$TA ) ? $target.' = "'.$TA.JCckDatabase::escape( $v ).$TZ.'"' : $target.' REGEXP "'.$TA.JCckDatabase::escape( $v ).$TZ.'"';
						}
					}
					if ( count( $fragments ) ) {
						$sql	=	'((' . implode( ') OR (', $fragments ) . '))';
					}
				}
				break;
			case 'num_higher':
				$range		=	'';
				$max		=	( $field->match_value ) ? $field->match_value : 99999;
				if ( $value <= $max ) {
					$range	=	CCK_List::generateRange( $value, $max );
				}
				$range		=	'[[:<:]]('.$range.')[[:>:]]';
				$sql 		=	$target.' REGEXP "'.$TA.$range.$TZ.'"';
				break;
			case 'num_higher_only':
				$range		=	'';
				$max		=	( $field->match_value ) ? $field->match_value : 99999;
				if ( $value <= $max ) {
					$range	=	CCK_List::generateRange( $value, $max );
				}
				$range		=	'[[:<:]]('.$range.')[[:>:]]';
				$sql 		=	$target.' REGEXP "'.$TA.$range.$TZ.'"';
				break;
			case 'num_lower':
				$range		=	'';
				$min		=	( $field->match_value ) ? $field->match_value : 0;
				if ( $value >= $min ) {
					$range	=	CCK_List::generateRange( $min, $value );
				}
				$range		=	'[[:<:]]('.$range.')[[:>:]]';
				$sql		=	$target.' REGEXP "'.$TA.$range.$TZ.'"';
				break;
			case 'num_lower_only':
				$range		=	'';
				$min		=	( $field->match_value ) ? $field->match_value : 0;
				if ( $value >= $min ) {
					$range	=	CCK_List::generateRange( $min, $value );
				}
				$range		=	'[[:<:]]('.$range.')[[:>:]]';
				$sql		=	$target.' REGEXP "'.$TA.$range.$TZ.'"';
				break;
			case 'not_alpha':
				$sql		=	$target.' NOT REGEXP "'.$TA.JCckDatabase::escape( $value ).'.*'.$TZ.'"';
				break;
			case 'not_any_exact':
				// todo
				break;
			case 'not_zeta': /* Zeta is not the last letter of Greek alphabet but.. this won't be an issue here. */
				$sql		=	$target.' NOT REGEXP "'.$TA.'.*'.JCckDatabase::escape( $value ).$TZ.'"';
				break;
			case 'not_empty':
				$sql		=	$target.' NOT REGEXP "'.$TA.$TZ.'"';
				break;
			case 'not_equal':
				$sql		=	$target.' NOT REGEXP "'.$TA.JCckDatabase::escape( $value ).$TZ.'"';
				break;
			case 'not_like':
				$sql		=	$target.' NOT REGEXP "'.$TA.'.*'.JCckDatabase::escape( $value ).'.*'.$TZ.'"';
				break;
			case 'not_null':
				$sql		=	$target.' NOT REGEXP "'.$TA.'0'.$TZ.'"';
				break;
			case 'is_null':
				// todo
				break;
			case 'is_not_null':
				// todo
				break;
			case 'radius_higher':
			case 'radius_lower':
				JFactory::getApplication()->enqueueMessage( JText::_( 'COM_CCK_RADIUS_MATCH_ONLY_WITH_STANDARD' ), 'notice' );
				break;
			case 'none':
				return;
				break;
			default:
				$sql		=	$target.' REGEXP "'.$TA.'.*'.JCckDatabase::escape( $value ).'.*'.$TZ.'"';
				break;
		}
		
		return $sql;
	}
	
	// onCCK_StoragePrepareStore
	public static function onCCK_StoragePrepareStore( &$field, $value, &$config = array() )
	{
		// Prepare
		$store	=	'<br />'.'::'.$field->storage_field2.'::'.$value.'::'.'/'.$field->storage_field2.'::';
		
		// Set
		parent::g_onCCK_StoragePrepareStore( $field, $store, $config );
	}
	
	// onCCK_StoragePrepareStore_X
	public static function onCCK_StoragePrepareStore_X( &$field, $value, $value2, &$config = array() )
	{
		// Prepare
		$store	=	'<br />::'.$field->storage_field2.'::'.$value.'::'.'/'.$field->storage_field2.'::'
				.	$value2;
		
		// Set
		parent::g_onCCK_StoragePrepareStore( $field, $store, $config );
	}
	
	// onCCK_StoragePrepareStore_Xi
	public static function onCCK_StoragePrepareStore_Xi()
	{
	}
	
	//onCCK_StoragePrepareImport
	public static function onCCK_StoragePrepareImport( $field, $value, &$config = array() )
	{
		// Prepare
		$store	=	'<br />'.'::'.$field->storage_field2.'::'.$value.'::'.'/'.$field->storage_field2.'::';
		
		// Set
		parent::g_onCCK_StoragePrepareStore( $field, $store, $config );
		
		return $store;   
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
		return '::'.$name.'::'.$value.'::'.'/'.$name.'::';
	}
	
	// _getTarget
	protected function _getTarget( $name, $target )
	{
		return '';
	}
	
	// _initValues
	protected function _initValues( $value )
	{
		preg_match_all( CCK_Content::getRegex(), $value, $values );
		
		return $values;
	}
	
	// replace
	public static function _replace( $name, $value, $value_old, $string, &$config = array() )
	{
		return str_replace( self::_format( $name, $value_old ), self::_format( $name, $value ), $string );
	}
}
?>