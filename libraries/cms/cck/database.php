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

// JCckDatabase
abstract class JCckDatabase
{
	// clean
	public static function clean( $text )
	{
		if ( is_numeric( $text ) ) {
			return (string)$text;
		} else {
			$len	=	strlen( $text );

			if ( $text[0] == "'" && $text[$len - 1] == "'" ) {
				$t	=	substr( $text, 1, - 1 );

				if ( is_numeric( $t ) ) {
					return "'".(string)$t."'";
				}
			}
		}

		return JCckDatabase::quote( uniqid() );
	}
	
	// convertUtf8mb4QueryToUtf8
	public static function convertUtf8mb4QueryToUtf8( $query )
	{
		if ( JCck::on( '3.5' ) ) {
			return JFactory::getDbo()->convertUtf8mb4QueryToUtf8( $query );
		}

		$beginningOfQuery	=	substr( $query, 0, 12 );
		$beginningOfQuery	=	strtoupper( $beginningOfQuery );

		if ( !in_array( $beginningOfQuery, array( 'ALTER TABLE ', 'CREATE TABLE' ) ) ) {
			return $query;
		}

		return str_replace( 'utf8mb4', 'utf8', $query );
	}

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
		$db			=	JFactory::getDbo();
		$utf8mb4	=	false;
		
		if ( JCck::on( '3.5' ) ) {
			$utf8mb4	=	$db->hasUTF8mb4Support();
		}
		if ( !$utf8mb4 ) {
			$query		=	self::convertUtf8mb4QueryToUtf8( $query );
		}

		$db->setQuery( $query );
		
		if ( ! $db->execute() ) {
			return false;
		}
		
		return true;
	}
	
	// getTableColumns
	public static function getTableColumns( $table, $flip = false )
	{
		return $flip ? array_flip( array_keys( JFactory::getDbo()->getTableColumns( $table ) ) ) : array_keys( JFactory::getDbo()->getTableColumns( $table ) );
	}

	// getTableFullColumns
	public static function getTableFullColumns( $table )
	{
		return JFactory::getDbo()->getTableColumns( $table, false );
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
	public static function getTableList( $flip = false )
	{
		return $flip ? array_flip( JFactory::getDbo()->getTableList() ) : JFactory::getDbo()->getTableList();
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