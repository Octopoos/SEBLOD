<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: payment.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

// JCckEcommercePayment
abstract class JCckEcommercePayment
{
	// getGateway
	public static function getGateway( $name = '' )
	{
		$user	=	Factory::getUser();
		$access	=	implode( ',', $user->getAuthorisedViewLevels() );
		$and	=	'';

		if ( $name != '' ) {
			$and	=	' AND type = '.Factory::getDbo()->quote( $name );
		}
		$name	=	JCckDatabase::loadResult( 'SELECT type'
											. ' FROM #__cck_more_ecommerce_gateways'
											. ' WHERE published = 1 AND access IN ('.$access.')'
											. $and
											. ' ORDER BY id DESC' );
		
		return $name;
	}

	// getGateways
	public static function getGateways()
	{
		$user	=	Factory::getUser();
		$access	=	implode( ',', $user->getAuthorisedViewLevels() );

		$items	=	JCckDatabase::loadObjectList( 'SELECT title, type'
											. ' FROM #__cck_more_ecommerce_gateways'
											. ' WHERE published = 1 AND access IN ('.$access.')'
											. ' ORDER BY title ASC' );
		
		return $items;
	}

	// getListenUrl
	public static function getListenUrl( $gateway )
	{
		$url	=	Uri::root();
		$url	.=	( $url[strlen($url)-1] == '/' ? '' : '/' );

		if ( !Factory::getApplication()->get( 'sef_rewrite' ) ) {
			$url	.=	'index.php/';
		} 

		$url	.=	'listen/'.$gateway->token;

		return $url;
	}
}
?>