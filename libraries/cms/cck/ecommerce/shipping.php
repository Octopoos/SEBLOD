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

// JCckEcommerceShipping
abstract class JCckEcommerceShipping
{
	// getMethod
	public static function getMethod()
	{
		$name	=	JCckDatabase::loadResult( 'SELECT type FROM #__cck_more_ecommerce_shipping_methods WHERE published = 1 ORDER BY id DESC' );

		return $name; /* JFactory::getApplication()->input->post->get( 'seb_order_method' ); */
	}
}
?>