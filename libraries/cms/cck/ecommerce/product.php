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

	// getDefinitions
	public static function getDefinitions()
	{
		static $definitions	=	array();

		if ( !count( $definitions ) ) {
			$items			=	JCckDatabase::loadObjectList( 'SELECT content_type, name, type, quantity, request_payment_field, request_payment_field_live, request_payment_field_live_options FROM #__cck_more_ecommerce_product_definitions WHERE published = 1' );
			
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
				if ( count( $definitions ) ) {
					JPluginHelper::importPlugin( 'cck_field_live' );

					$config			=	array();
					$dispatcher		=	JDispatcher::getInstance();

					foreach ( $definitions as $name=>$product_def ) {
						if ( $product_def->request_payment_field_live != '' ) {
							$field			=	(object)array(
												'live'=>$product_def->request_payment_field_live,
												'live_options'=>$product_def->request_payment_field_live_options,
											);
							$suffix			=	'';
						
							$dispatcher->trigger( 'onCCK_Field_LivePrepareForm', array( &$field, &$suffix, &$config ) );

							if ( $suffix != '' ) {
								if ( $product_def->request_payment_field != '' ) {
									$definitions[$name]->request_payment_field	.=	'_'.$suffix;
								} else {
									$definitions[$name]->request_payment_field	=	$suffix;
								}
							}
						}
					}
				}
			}
		}

		return $definitions;
	}
}
?>