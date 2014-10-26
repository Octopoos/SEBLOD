<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: database.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckDatabaseCache
abstract class JCckDatabaseCache extends JCckDatabase
{
	// getTableList
	public static function getTableList()
	{
		static $cache	=	NULL;
		
		if ( !$cache ) {
			$cache	=	parent::getTableList();
		}

		return $cache;
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