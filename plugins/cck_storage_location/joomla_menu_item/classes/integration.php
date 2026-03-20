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

require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_menu_item/joomla_menu_item.php';

// Class
class plgCCK_Storage_LocationJoomla_Menu_Item_Integration extends plgCCK_Storage_LocationJoomla_Menu_Item
{
	// onCCK_Storage_LocationAfterDispatch
	public static function onCCK_Storage_LocationAfterDispatch( &$data, $uri = array() )
	{
	}

	// onCCK_Storage_LocationAfterRender
	public static function onCCK_Storage_LocationAfterRender( &$buffer, &$data, $uri = array() )
	{
	}
}
?>
