<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_user/joomla_user.php';

// Class
class plgCCK_Storage_LocationJoomla_User_Importer extends plgCCK_Storage_LocationJoomla_User
{
	protected static $columns_excluded	=	array( 'isRoot', 'password_clear', 'usertype', 'guest', 'aid', 'userHelper' );

	// getColumnsToImport
	public static function getColumnsToImport()
	{
		$table		=	self::_getTable();
		$columns	=	$table->getProperties();
		
		foreach ( self::$columns_excluded as $column ) {
			if ( array_key_exists( $column, $columns ) ) {
				unset( $columns[$column] );
			}
		}
		
		return array_keys( $columns );
	}

	// onCCK_Storage_LocationImport
	public static function onCCK_Storage_LocationImport( $data, &$config = array(), $pk = 0 )
	{
		// $config['force_password']	=	true;
		if ( !$config['pk'] ) {
			// Init
			if ( isset( $config['key'] ) && $config['key'] ) {
				if ( isset( $data[$config['key']] ) && $data[$config['key']] != '' ) {
					$pk		=	(int)JCckDatabase::loadResult( 'SELECT '.self::$key.' FROM '.self::$table.' WHERE '.$config['key'].' = "'.$data[$config['key']].'"' );
				}
				$pk		=	( $pk > 0 ) ? $pk : 0;
			} else {
				$pk		=	( isset( $data[self::$key] ) && (int)$data[self::$key] > 0 ) ? (int)$data[self::$key] : 0;
			}
			$table	=	self::_getTable_fromSite( $pk );
			$isNew	=	( $table->{self::$key} > 0 ) ? false : true;
			$iPk	=	0;
			
			if ( $isNew ) {
				if ( isset( $data[self::$key] ) ) {
					$iPk	=	$data[self::$key];
					unset( $data[self::$key] );
				}
				$config['log']	=	'created';
			} else {
				$config['id']	=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_core WHERE storage_location = "joomla_user" AND pk = '.(int)$pk );
				$config['log']	=	'updated';
			}
			if ( !$config['id'] ) {
				$config['id']	=	parent::g_onCCK_Storage_LocationPrepareStore();
			}
			self::_initTable( $table, $data, $config, true );
			
			// Prepare
			if ( $data['password'] ) {
				$data['password2']	=	$data['password'];
			}
			if ( !empty( $data ) ) {
				$table->bind( $data );
			}
			if ( isset( $config['params']['force_password'] ) && $config['params']['force_password'] ) {
				$table->password	=	$table->password_clear;
			}
			$parameters		=	JComponentHelper::getParams( 'com_users' );
			self::_completeTable( $table, $data, $config, $parameters );
			
			// Store
			if ( !$table->save() ) {
				$config['error']	=	true;
				$config['log']		=	'cancelled';
				$config['pk']		=	$pk;
				parent::g_onCCK_Storage_LocationRollback( $config['id'] );
				return false;
			}
			
			// Tweak
			//if ( $iPk > 0 ) {
			//	if ( JCckDatabase::execute( 'UPDATE '.self::$table.' SET '.self::$key.' = '.(int)$iPk.' WHERE '.self::$key.' = '.(int)$table->{self::$key} ) ) {
			//		$table->{self::$key}	=	$iPk;
			//		$config['auto_inc']		=	( $iPk > $config['auto_inc'] ) ? $iPk : $config['auto_inc'];
			//		if ( $table->asset_id ) {
			//			JCckDatabase::execute( 'UPDATE #__assets SET name = "com_content.article.'.$iPk.'" WHERE id = '.(int)$table->asset_id );
			//		}
			//	}
			//}
			
			if ( !$config['pk'] ) {
				$config['pk']	=	(int)$table->{self::$key};
			}
			$config['isNew']	=	(int)$isNew;
			$config['author']	=	$table->id;
		}
		
		parent::g_onCCK_Storage_LocationStore( $data, self::$table, $config['pk'], $config, $config['params'] );
		
		return true;
	}
		
	// onCCK_Storage_LocationAfterImport
	public static function onCCK_Storage_LocationAfterImports( $fields, &$config = array() )
	{
	}
}
?>