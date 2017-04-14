<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: promotion.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommerceCurrency
abstract class JCckEcommerceCurrency
{
	// format
	public static function format( $amount, $format = true )
	{
		if ( $format ) {
			$amount	=	number_format( $amount, 2, JText::_( 'DECIMALS_SEPARATOR' ), JText::_( 'THOUSANDS_SEPARATOR' ) );
		}
		$currency	=	JCckEcommerce::getCurrency();
		
		return $currency->lft.$amount.$currency->rgt;
	}
}
?>