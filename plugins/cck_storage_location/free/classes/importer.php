<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

require_once JPATH_SITE.'/plugins/cck_storage_location/free/free.php';

// Class
class plgCCK_Storage_LocationFree_Importer extends plgCCK_Storage_LocationFree
{
	protected static $columns_excluded	=	array(); /* TODO#SEBLOD: */

	// getColumnsToImport
	public static function getColumnsToImport( $table_name )
	{
		$table		=	self::_getTable( 0, $table_name );
		$columns	=	$table->getProperties();

		return array_keys( $columns );
	}

	// onCCK_Storage_LocationImport
	public static function onCCK_Storage_LocationImport( $data, &$config = array(), $pk = 0 )
	{
		if ( !$config['pk'] ) {
			// Init
			if ( !$pk ) {
				if ( isset( $config['key'] ) && $config['key'] ) {
					if ( isset( $data[$config['key']] ) && $data[$config['key']] != '' ) {
						$pk		=	(int)JCckDatabase::loadResult( 'SELECT '.self::$key.' FROM '.$config['table'].' WHERE '.$config['key'].' = "'.$data[$config['key']].'"' );
					}
					$pk		=	( $pk > 0 ) ? $pk : 0;
				} else {
					$pk		=	( isset( $data[self::$key] ) && (int)$data[self::$key] > 0 ) ? (int)$data[self::$key] : 0;
				}
			}
			$table	=	self::_getTable( $pk, $config['table'] );
			$isNew	=	( $table->{self::$key} > 0 ) ? false : true;
			$iPk	=	0;
			
			if ( $isNew ) {
				if ( isset( $data[self::$key] ) ) {
					$iPk	=	$data[self::$key];
					unset( $data[self::$key] );
				}
				$config['log']	=	'created';
			} else {
				$config['id']	=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_core WHERE storage_table = "'.$config['table'].'" AND pk = '.(int)$pk );
				$config['log']	=	'updated';
			}
			if ( !$config['id'] ) {
				$config['id']	=	parent::g_onCCK_Storage_LocationPrepareStore();
			}

			self::_initTable( $table, $data, $config, true );
			
			// Prepare
			if ( !empty( $data ) ) {
				$table->bind( $data );
			}
			$table->check();

			self::_completeTable( $table, $data, $config );
			
			// Store
			JPluginHelper::importPlugin( 'content' );
			$dispatcher	=	JEventDispatcher::getInstance();
			$dispatcher->trigger( 'onContentBeforeSave', array( self::$context, &$table, $isNew ) );
			if ( !$table->store() ) {
				$config['error']	=	true;
				$config['log']		=	'cancelled';
				$config['pk']		=	$pk;
				parent::g_onCCK_Storage_LocationRollback( $config['id'] );
				return false;
			}
			$dispatcher->trigger( 'onContentAfterSave', array( self::$context, &$table, $isNew ) );
			
			// Tweak
			if ( $iPk > 0 ) {
				if ( JCckDatabase::execute( 'UPDATE '.$config['table'].' SET '.self::$key.' = '.(int)$iPk.' WHERE '.self::$key.' = '.(int)$table->{self::$key} ) ) {
					$table->{self::$key}	=	$iPk;
					$config['auto_inc']		=	( $iPk > $config['auto_inc'] ) ? $iPk : $config['auto_inc'];
				}
			}
			
			if ( !$config['pk'] ) {
				$config['pk']	=	(int)$table->{self::$key};
			}
			$config['isNew']	=	(int)$isNew;

			if ( $config['join'] ) {
				self::_core( $data, $config );
			} else {
				if ( ! isset( $config['primary'] ) && $config['pk'] ) {
					self::_core( $data, $config );
				}
			}
		}
		
		return true;
	}
	
	// onCCK_Storage_LocationAfterImport
	public static function onCCK_Storage_LocationAfterImports( $fields, &$config = array() )
	{
	}
}
?>