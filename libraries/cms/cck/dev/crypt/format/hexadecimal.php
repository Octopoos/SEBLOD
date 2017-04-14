<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: hexadecimal.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( !function_exists( 'hex2bin' ) ) {
	// hex2bin PHP >= 5.4.0
	function hex2bin( $string ) {
	  $hexstr	=	str_replace( ' ', '', $string );
	  $hexstr	=	str_replace( '\x', '', $string );
	  $retstr	=	pack( 'H*', $string );
	  
	  return $retstr;
	}
}

// JCckCryptFormatHexadecimal
class JCckCryptFormatHexadecimal extends JCckCryptFormat
{
	// decode
	public static function decrypt( $string )
	{
		return hex2bin( $string );
	}
	
	// encode
	public static function encrypt( $string )
	{
		return bin2hex( $string );
	}
}
?>