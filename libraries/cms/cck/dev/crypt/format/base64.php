<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: base64.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckCryptFormatBase64
class JCckCryptFormatBase64 extends JCckCryptFormat
{
	// decode
	public static function decrypt( $string )
	{
		return base64_decode( $string );
	}
	
	// encode
	public static function encrypt( $string )
	{
		return base64_encode( $string );
	}
}
?>