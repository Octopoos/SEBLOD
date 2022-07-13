<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cckactiondropdown.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JLoader::register( 'JHtmlActionsDropdown', JPATH_SITE.'/libraries/cms/html/actionsdropdown.php' );

// JHtmlCckActionsDropdown
abstract class JHtmlCckActionsDropdown extends JHtmlActionsDropdown
{
	// addCustomLinkItem
	public static function addCustomLinkItem( $label, $icon = '', $id = '', $link = '', $class = '', $attr = '' )
	{
		$attr	=	$attr ? ' '.$attr : '';
		$class	=	$class ? ' class="'.$class.'"' : '';

		static::$dropDownList[] = '<li>'
			. '<a href = "'.$link.'"'.$class.$attr.'>'
			. ($icon ? '<span class="icon-' . $icon . '"></span>' : '')
			. $label
			. '</a>'
			. '</li>';
	}

	public static function render($item = '')
	{
		$html	= array();

		if ( JCck::on( '4.0 ') ) {
			$html[] = '<button data-bs-toggle="dropdown" class="dropdown-toggle btn btn-sm btn-outline-secondary">';
			$html[] = '<span class="icon-ellipsis-h"></span>';
		} else {
			$html[] = '<button data-toggle="dropdown" class="dropdown-toggle btn btn-micro">';
			$html[] = '<span class="caret"></span>';
		}

		$html[] = '</button>';
		$html[] = '<ul class="dropdown-menu">';
		$html[] = implode( '', static::$dropDownList );
		$html[] = '</ul>';

		static::$dropDownList = null;

		return implode( '', $html );
	}
}
?>