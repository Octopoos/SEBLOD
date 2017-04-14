<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: rule.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommerceRule
abstract class JCckEcommerceRule
{
	// apply
	public static function apply( $type, &$total, $items, $params = array(), $totals = array() )
	{
		$user		=	JCck::getUser();
		$my_groups	=	$user->groups; /* $user->getAuthorisedGroups(); */
		$my_zones	=	JCckEcommerce::getUserZones();
		
		$currency	=	JCckEcommerce::getCurrency();
		$res		=	0;
		$results	=	array( 'items'=>array() );
		$cost		=	'';
		$rules		=	JCckEcommerce::getShippingRules( $type, $my_zones );
		$total2		=	(int)$total;
		
		if ( count( $rules ) ) {
			foreach ( $rules as $r ) {
				if ( isset( $r->target_type ) && $r->target_type ) {
					$continue					=	true;
					$product_def				=	JCckEcommerce::getProductDefinition( $r->target_type );
					$content_types				=	explode( '||', $product_def->content_type );
					
					if ( count( $content_types ) ) {
						foreach ( $content_types as $content_type ) {
							if ( isset( $params['content_types'][$content_type] ) ) {
								$continue		=	false;
								break;
							}
						}
					}
					if ( $continue ) {
						continue;
					}
				}
				if ( isset( $r->target_products ) && $r->target_products ) {
					if ( $r->target_products == 2 ) {
						$continue	=	true;
						$products	=	self::getTargets( $r->id );

						if ( count( $products ) ) {
							foreach ( $products as $product ) {
								if ( isset( $items[$product] ) ) {
									$continue	=	false;
									break;
								}
							}
						}
						if ( $continue ) {
							continue;
						}
					} elseif ( $r->target_products == -2 ) {
						$continue	=	false;
						$products	=	self::getTargets( $r->id );

						if ( count( $products ) ) {
							foreach ( $products as $product ) {
								if ( isset( $items[$product] ) ) {
									$continue	=	true;
									break;
								}
							}
						}
						if ( $continue ) {
							continue;
						}
					}
				}

				if ( $r->mode ) {
					if ( !isset( $totals[$type][$r->target_type] ) ) {
						continue;
					} else {
						$min	=	(float)number_format( (float)$r->min, 2 );
						$max	=	(float)number_format( (float)$r->max, 2 );
						$value	=	(float)number_format( (float)$totals[$type][$r->target_type], 2 );

						if ( $value < $min ) {
							continue;
						}
						if ( $max && ( $value > $max ) ) {
							continue;
						}
					}
				} else {
					$min	=	(float)number_format( (float)$r->min, 2 );
					$max	=	(float)number_format( (float)$r->max, 2 );
					$value	=	(float)number_format( (float)$totals[$type]['_'], 2 );
										
					if ( $value < $min ) {
						continue;
					}
					if ( $max && ( $value > $max ) ) {
						continue;
					}
				}

				// Apply
				switch ( $r->cost ) {
					case 'free':
						$cost						=	0;
						$res						+=	$cost;
						$text						=	JText::_( 'COM_CCK_FREE_SHIPPING' );
						$total						+=	$cost;
						$results['items'][$r->id]	=	array(
															'cost'=>$r->cost,
															'cost_amount'=>'',
															'text'=>$text,
															'title'=>$r->title,
															'type'=>$r->type
														);
						break;
					case 'plus':
						$cost						=	$r->cost_amount;
						$res						+=	$cost;
						$total						+=	$cost;
						$results['items'][$r->id]	=	array(
															'cost'=>$r->cost,
															'cost_amount'=>(string)$cost,
															'text'=>'',
															'title'=>$r->title,
															'type'=>$r->type
														);
						break;
					default:
						break;
				}
			}
		}

		if ( count( $results['items'] ) ) {
			$results['total']	=	(float)$res;

			return (object)$results;
		}

		return null;
	}

	// getTargets
	public static function getTargets( $id )
	{
		static $cache	=	array();

		if ( !isset( $cache[$id] ) ) {
			$cache[$id]	=	JCckDatabase::loadColumn( 'SELECT product_id FROM #__cck_more_ecommerce_shipping_rule_product WHERE rule_id = '.(int)$id );
		}

		return $cache[$id];
	}
}
?>