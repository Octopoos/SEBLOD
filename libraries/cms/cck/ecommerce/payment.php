<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: payment.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommercePayment
abstract class JCckEcommercePayment
{
	// getGateway
	public static function getGateway()
	{
		$user	=	JFactory::getUser();
		$access	=	implode( ',', $user->getAuthorisedViewLevels() );

		$name	=	JCckDatabase::loadResult( 'SELECT type'
											. ' FROM #__cck_more_ecommerce_gateways'
											. ' WHERE published = 1 AND access IN ('.$access.')'
											. ' ORDER BY id DESC' );
		
		return $name;
	}

	// getListenUrl
	public static function getListenUrl( $gateway )
	{
		$url	=	JUri::root();
		$url	.=	( $url[strlen($url)-1] == '/' ? '' : '/' );

		if ( !JFactory::getApplication()->get( 'sef_rewrite' ) ) {
			$url	.=	'index.php/';
		} 

		$url	.=	'listen/'.$gateway->token;

		return $url;
	}
}
?>