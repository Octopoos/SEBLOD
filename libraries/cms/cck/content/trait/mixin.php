<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: mixin.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( version_compare( PHP_VERSION, '5.4', '<=' ) ) {
	return;
}

// JCckContentTrait
trait JCckContentTraitMixin
{
	// _
	public function _()
	{
		static $properties	=	array();

		return function( $key, $value = '' ) use ( &$properties ) {
			if ( isset( $value ) && $value !== '' ) {
				$properties[$key]	=	$value;
			}

			return $properties[$key];
		};
	}
}
?>