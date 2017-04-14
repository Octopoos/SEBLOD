<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: storage.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class JCckPluginStorage extends JPlugin
{
	protected static $construction	=	'cck_storage';	
	
	// g_onCCK_StoragePrepareContent
	public static function g_onCCK_StoragePrepareContent( &$field, &$config = array() )
	{
		if ( ! $field->storage_field2 ) {
			$field->storage_field2	=	$field->name;
		} elseif ( $field->storage_field2 == 'clear' ) {
			$field->storage_field2	=	'';
		} elseif ( strpos( $field->storage_field2, '|' ) !== false ) {
			$levels	=	explode( '|', $field->storage_field2 );
			for ( $i = 0, $n = count( $levels ); $i < $n; $i++ ) {
				$field->{'storage_field'.($i + 2)}	=	$levels[$i];
			}
		}
	}
	
	// g_onCCK_StoragePrepareForm
	public static function g_onCCK_StoragePrepareForm( &$field, &$config = array() )
	{
		if ( ! $field->storage_field2 ) {
			$field->storage_field2	=	$field->name;
		} elseif ( $field->storage_field2 == 'clear' ) {
			$field->storage_field2	=	'';
		} elseif ( strpos( $field->storage_field2, '|' ) !== false ) {
			$levels	=	explode( '|', $field->storage_field2 );
			for ( $i = 0, $n = count( $levels ); $i < $n; $i++ ) {
				$field->{'storage_field'.($i + 2)}	=	$levels[$i];
			}
		}
	}
	
	// g_onCCK_StoragePrepareStore
	public static function g_onCCK_StoragePrepareStore( &$field, $store, &$config = array() )
	{
		$Pl	=	$field->storage_location;
		$Pt	=	$field->storage_table;
		$Pf	=	$field->storage_field;
		
		if ( ! isset( $config['storages'][$Pt] ) ) {
			$config['storages'][$Pt]		=	array();
			// -
			$s				=	new stdClass;
			$s->location	=	$Pl;
			$s->table		=	$Pt;
			$s->state		=	false;
			// -
			$config['storages'][$Pt]['_']	=	$s;
		}
		if ( is_array( $store ) ) {
			@$config['storages'][$Pt][$Pf]	=	$store;
		} else {
			@$config['storages'][$Pt][$Pf]	.=	trim( $store );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	public static function g_addProcess( $event, $type, &$config, $params, $priority = 3 )
	{
		if ( $event && $type ) {
			$process						=	new stdClass;
			$process->group					=	self::$construction;
			$process->type					=	$type;
			$process->params				=	$params;
			$process->priority				=	$priority;
			$config['process'][$event][]	=	$process;
		}
	}
}
?>