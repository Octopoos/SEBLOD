<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: article.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckContentArticle
abstract class JCckContentArticle
{
	public static $_items	=	array();
	
	// getRow
	public static function getRow( $id, $content_type = '' )
	{
		if ( !$id ) {
			return NULL;
		}
		
		$index	=	$id.'_'.$content_type;
		if ( isset( self::$_items[$index] ) ) {
			return self::$_items[$index];
		}
		
		$row	=	JTable::getInstance( 'content' );
		$row->load( $id );
		
		if ( !$content_type ) {
			$content_type	=	JCckDatabase::loadResult( 'SELECT cck FROM #__cck_core WHERE storage_location = "joomla_article" AND pk = '.(int)$row->id );
		}
		if ( $content_type ) {
			$tables		=	JCckDatabaseCache::getTableList( true );
			$hasMore	=	isset( $tables[JFactory::getConfig()->get( 'dbprefix' ).'cck_store_form_'.$content_type] );

			if ( $hasMore ) {
				$fields	=	JCckDatabase::loadObject( 'SELECT * FROM #__cck_store_form_'.$content_type.' WHERE id = '.(int)$row->id );

				if ( count( $fields ) ) {
					foreach ( $fields as $k=>$v ) {
						$row->$k	=	$v;
					}
				}
			}
		}
		
		self::$_items[$index]	=	$row;
		
		return $row;
	}
}
?>