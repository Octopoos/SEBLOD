<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: payment.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommercePayment
abstract class JCckEcommercePayment
{
	// getGateway
	public static function getGateway()
	{
		$name	=	JCckDatabase::loadResult( 'SELECT type FROM #__cck_more_ecommerce_gateways WHERE published = 1 ORDER BY id DESC' );
		
		return $name; /* JFactory::getApplication()->input->post->get( 'seb_order_gateway' ); */
	}
}
?>