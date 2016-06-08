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

// JCckEcommerceProduct
abstract class JCckEcommerceProduct
{
	// getDefinition
	public static function getDefinition( $type )
	{
		static $definitions	=	array();
		
		if ( !count( $definitions ) ) {
			$items			=	JCckDatabase::loadObjectList( 'SELECT name, content_type FROM #__cck_more_ecommerce_product_definitions WHERE published = 1' );
			
			if ( count( $items ) ) {
				foreach ( $items as $item ) {
					if ( !$item->content_type ) {
						continue;
					}
					$types		=	explode( '||', $item->content_type );

					if ( count( $types ) ) {
						foreach ( $types as $k=>$v ) {
							if ( $v != '' ) {
								$definitions[$v]	=	$item;
							}
						}
					}
				}
			}
		}
		if ( !( isset( $definitions[$type] ) && $definitions[$type] ) ) {
			return (object)array( 'quantity'=>'' );
		}
		
		return JCckEcommerce::getProductDefinition( $definitions[$type]->name );
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