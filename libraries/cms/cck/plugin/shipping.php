<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: shipping.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class JCckPluginShipping extends JPlugin
{
	protected static $construction	=	'cck_ecommerce_shipping';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// g_getPath
	public static function g_getPath( $type = '' )
	{
		return JUri::root( true ).'/plugins/'.self::$construction.'/'.$type;
	}
}
?>