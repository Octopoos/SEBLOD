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

// JCckDatabase
abstract class JCckDatabase
{
	// doQuery (deprecated)
	public static function doQuery( $query )
	{
		return self::execute( $query );
	}
	
	// escape
	public static function escape( $text, $extra = false )
	{
		return JFactory::getDbo()->escape( $text, $extra );
	}
	
	// execute
	public static function execute( $query )
	{
		$db		=	JFactory::getDbo();
		
		$db->setQuery( $query );
		if ( ! $db->execute() ) {
			return false;
		}
		
		return true;
	}
	
	// getTableCreate
	public static function getTableCreate( $tables )
	{
		$res	=	JFactory::getDbo()->getTableCreate( $tables );
		
		$res	=	str_replace( JFactory::getConfig()->get( 'dbprefix' ), '#__', $res );
		$res	=	str_replace( 'CREATE TABLE `#__', 'CREATE TABLE IF NOT EXISTS `#__', $res );
		
		return $res;
	}
	
	// getTableList
	public static function getTableList()
	{
		return JFactory::getDbo()->getTableList();
	}

	// loadAssocList
	public static function loadAssocList( $query, $key = null, $column = null )
	{
		$db		=	JFactory::getDbo();
	
		$db->setQuery( $query );
		$res	=	$db->loadAssocList( $key, $column );
		
		return $res;
	}
	
	// loadColumn
	public static function loadColumn( $query )
	{
		$db		=	JFactory::getDbo();
	
		$db->setQuery( $query );
		$res	=	$db->loadColumn();
		
		return $res;
	}
	
	// loadResult
	public static function loadResult( $query )
	{
		$db		=	JFactory::getDbo();
	
		$db->setQuery( $query );
		$res	=	$db->loadResult();
		
		return $res;
	}
	
	// loadResultArray (deprecated)
	public static function loadResultArray( $query )
	{
		return self::loadColumn( $query );
	}
	
	// loadObject
	public static function loadObject( $query )
	{
		$db		=	JFactory::getDbo();
	
		$db->setQuery( $query );
		$res	=	$db->loadObject();
		
		return $res;
	}
	
	// loadObjectList
	public static function loadObjectList( $query, $key = null )
	{
		$db		=	JFactory::getDbo();
	
		$db->setQuery( $query );
		$res	=	$db->loadObjectList( $key );
		
		return $res;
	}
	
	// loadObjectListArray
	public static function loadObjectListArray( $query, $akey, $key = null )
	{
		$db		=	JFactory::getDbo();
		
		$db->setQuery( $query );

		$list	=	$db->loadObjectList();
		$res	=	array();
		if ( count( $list ) ) {
			if ( $key ) {
				foreach ( $list as $row ) {
					$res[$row->$akey][$row->$key]	=	$row;
				}
			} else {
				foreach ( $list as $row ) {
					$res[$row->$akey][]				=	$row;
				}
			}
		}
		
		return $res;
	}
	
	// quote
	public static function quote( $text, $escape = true )
	{
		return JFactory::getDbo()->quote( $text, $escape );
	}

	// quoteName
	public static function quoteName( $name, $as = NULL )
	{
		return JFactory::getDbo()->quoteName( $name, $as );
	}
}
?>