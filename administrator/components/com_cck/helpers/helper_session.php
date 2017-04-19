<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: helper_version.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Helper
class Helper_Session
{
	// getExtensionShortName
	public static function getExtensionShortName( $extension )
	{
		return str_replace( 'com_cck_', '', $extension );
	}
	
	// loadExtensionLang
	public static function loadExtensionLang( $extension )
	{
		$lang	=	JFactory::getLanguage();
		
		$lang->load( $extension.'.sys', JPATH_ADMINISTRATOR, $lang->getTag(), true );
	}
}
?>