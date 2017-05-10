<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: payment.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommerceProduct
abstract class JCckEcommerceProduct
{
	// getAttributes
	public static function getAttributes()
	{
		return JCckDatabase::loadColumn( 'SELECT storage_field FROM #__cck_core_fields WHERE type = "cck_ecommerce_attribute" AND published = 1' );
	}

	// getDefinition (retrieve the primary definition of a Content Type)
	public static function getDefinition( $type )
	{
		static $definitions	=	array();
		
		if ( !count( $definitions ) ) {
			$items			=	JCckDatabase::loadObjectList( 'SELECT name, content_type FROM #__cck_more_ecommerce_product_definitions WHERE published = 1 AND type != "alternative"' );
			
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
			return (object)array(
							'attribute'=>0,
							'attributes'=>array(),
							'name'=>'',
							'quantity'=>'',
							'request_stock_field'=>'',
							'request_weight_field'=>''
						   );
		}
		
		return JCckEcommerce::getProductDefinition( $definitions[$type]->name );
	}

	// getDefinitions (retrieve all primary definitions)
	public static function getDefinitions()
	{
		static $definitions	=	array();

		if ( !count( $definitions ) ) {
			$items			=	JCckDatabase::loadObjectList( 'SELECT content_type, name, type, quantity, request_payment_field, request_payment_field_live, request_payment_field_live_options, request_stock_field, request_tax_field, request_tax_field_live, request_tax_field_live_options, request_weight_field, attribute, attributes FROM #__cck_more_ecommerce_product_definitions WHERE published = 1 AND type != "alternative"' );
			
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
					$dispatcher		=	JEventDispatcher::getInstance();
					$keys			=	array( 'payment', 'tax' );
					
					foreach ( $definitions as $name=>$product_def ) {
						if ( is_string( $product_def->attributes ) ) {
							if ( $product_def->attributes == '' ) {
								$product_def->attributes	=	array();
							} else {
								$product_def->attributes	=	explode( '||', $product_def->attributes );
								$product_def->attributes	=	array_flip( $product_def->attributes );
							}
						}
						foreach( $keys as $key ) {
							if ( $product_def->{'request_'.$key.'_field_live'} != '' ) {
								$field			=	(object)array(
													'live'=>$product_def->{'request_'.$key.'_field_live'},
													'live_options'=>$product_def->{'request_'.$key.'_field_live_options'},
												);
								$suffix			=	'';
								
								$dispatcher->trigger( 'onCCK_Field_LivePrepareForm', array( &$field, &$suffix, &$config ) );

								if ( $suffix != '' ) {
									if ( $product_def->{'request_'.$key.'_field'} != '' ) {
										$definitions[$name]->{'request_'.$key.'_field'}	.=	'_'.$suffix;
									} else {
										$definitions[$name]->{'request_'.$key.'_field'}	=	$suffix;
									}
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