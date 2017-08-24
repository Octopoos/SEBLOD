<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: category.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JLoader::register( 'JTableCategory', JPATH_PLATFORM.'/joomla/database/table/category.php' );

// CCK_Category
class CCK_Category
{
	// getRow
	public static function getRow( $id )
	{
		$row	=	'';
		
		if ( $id ) {
			$row	=	JTable::getInstance( 'Category' );
			$row->load( $id );
		}
		
		return $row;
	}
}
?>