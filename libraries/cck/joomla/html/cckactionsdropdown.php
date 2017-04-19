<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cckactiondropdown.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JLoader::register( 'JHtmlActionsDropdown', JPATH_SITE.'/libraries/cms/html/actionsdropdown.php' );

// JHtmlCckActionsDropdown
abstract class JHtmlCckActionsDropdown extends JHtmlActionsDropdown
{
	// addCustomLinkItem
	public static function addCustomLinkItem( $label, $icon = '', $id = '', $link = '' )
	{
		static::$dropDownList[] = '<li>'
			. '<a href = "'.$link.'">'
			. ($icon ? '<span class="icon-' . $icon . '"></span> ' : '')
			. $label
			. '</a>'
			. '</li>';
	}
}
?>