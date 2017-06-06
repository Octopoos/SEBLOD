<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: database.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckDatabaseCache
abstract class JCckDatabaseCache extends JCckDatabase
{
	// getTableColumns
	public static function getTableColumns( $table, $flip = false )
	{
		static $cache	=	array();

		if ( !isset( $cache[$table] ) ) {
			$cache[$table]	=	parent::getTableColumns( $table );
		}

		return $flip ? array_flip( $cache[$table] ) : $cache[$table];
	}

	// getTableList
	public static function getTableList( $flip = false )
	{
		static $cache	=	NULL;
		
		if ( !$cache ) {
			$cache	=	parent::getTableList();
		}

		return $flip ? array_flip( $cache ) : $cache;
	}

	// loadResult
	public static function loadObject( $query )
	{
		static $cache	=	array();
		$idx			=	md5( $query );

		if ( !isset( $cache[$idx] ) ) {
			$cache[$idx]	=	parent::loadObject( $query );
		}
		
		return $cache[$idx];
	}
	
	// loadObjectList
	public static function loadObjectList( $query, $key = null )
	{
		static $cache	=	array();
		$idx			=	md5( $query );
		
		if ( !isset( $cache[$idx] ) ) {
			$cache[$idx]	=	parent::loadObjectList( $query, $key );
		}
		
		return $cache[$idx];
	}

	// loadObjectListArray
	public static function loadObjectListArray( $query, $akey, $key = null )
	{
		static $cache	=	array();
		$idx			=	md5( $query );

		if ( !isset( $cache[$idx] ) ) {
			$cache[$idx]	=	parent::loadObjectListArray( $query, $akey, $key );
		}
		
		return $cache[$idx];
	}
	
	// loadResult
	public static function loadResult( $query )
	{
		static $cache	=	array();
		$idx			=	md5( $query );

		if ( !isset( $cache[$idx] ) ) {
			$cache[$idx]	=	parent::loadResult( $query );
		}
		
		return $cache[$idx];
	}
}
?>