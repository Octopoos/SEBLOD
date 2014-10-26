<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: format.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckCryptFormat
abstract class JCckCryptFormat
{
	// decode
	public static function decrypt( $data )
	{
	}
	
	// encode
	public static function encrypt( $data )
	{
	}
}
?>