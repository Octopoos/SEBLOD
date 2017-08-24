<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: article.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JLoader::register( 'JTableContent', JPATH_PLATFORM.'/joomla/database/table/content.php' );

// CCK_Article
class CCK_Article
{	
	// getRow
	public static function getRow( $id )
	{
		$row	=	'';
		
		if ( $id ) {
			$row	=	JTable::getInstance( 'Content' );
			$row->load( $id );
		}
		
		return $row;
	}
	
	// getRow_Value
	public static function getRow_Value( $id, $fieldname )
	{
		$res	=	'';
		
		$row	=	CCK_Article::getRow( $id );
		
		if ( ! $row ) {
			return false;
		}
		
		$res	=	@$row->$fieldname;
		
		return $res;
	}
	
	// getText
	public static function getText( $id )
	{
		$obj	=	JCckDatabase::loadObject( 'SELECT s.id, s.introtext, s.fulltext FROM #__content AS s WHERE s.id='.$id );
		$res	=	@$obj->introtext.@$obj->fulltext;
		
		return $res;
	}
	
	// getValue
	public static function getValue( $id, $fieldname )
	{
		$text	=	CCK_Article::getText( $id );
		
		$res	=	CCK_Content::getValue( $text, $fieldname );
		
		return $res;
	}
	
	// getValues
	public static function getValues( $id, $fieldnames = '' )
	{		
		$text	=	CCK_Article::getText( $id );
		
		$res	=	CCK_Content::getValues( $text, $fieldnames );
		
		return $res;
	}
	
	// setRow_Value
	public static function setRow_Value( $id, $fieldname,	$value )
	{
		$row	=	CCK_Article::getRow( $id );
		
		if ( ! $row ) {
			return false;
		}
		
		$row->$fieldname	=	$value;
		
		if ( ! $row->store() ) {
			return false;
		}
		
		return true;
	}
	
	// setValue
	public static function setValue( $id, $fieldname, $value, $old_value = '' )
	{
		$row	=	CCK_Article::getRow( $id );
		
		if ( ! $row ) {
			return false;
		}
		
		$row->introtext	=	CCK_Content::setValue( $row->introtext, $fieldname, $value, $old_value );
		$row->fulltext	=	CCK_Content::setValue( $row->fulltext, $fieldname, $value, $old_value );
	
		if ( ! $row->store() ) {
			return false;
		}
		
		return true;
	}
	
	// setValues
	public static function setValues( $id, $fieldnames, $values, $old_values = '' )
	{
		$row	=	CCK_Article::getRow( $id );
		
		if ( ! $row ) {
			return false;
		}
		
		$row->introtext	=	CCK_Content::setValues( $row->introtext, $fieldnames, $values, $old_values );
		$row->fulltext	=	CCK_Content::setValues( $row->fulltext, $fieldnames, $values, $old_values );
	
		if ( ! $row->store() ) {
			return false;
		}
		
		return true;
	}
}
?>