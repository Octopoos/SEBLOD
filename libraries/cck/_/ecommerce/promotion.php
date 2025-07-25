<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: promotion.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommercePromotion
abstract class JCckEcommercePromotion
{
	// apply
	public static function apply( $type, &$total, $items, $params = array() )
	{
		$user		=	JCck::getUser();
		$my_groups	=	$user->groups; /* $user->getAuthorisedGroups(); */

		$currency	=	JCckEcommerce::getCurrency();
		$promotions	=	JCckEcommerce::getPromotions( $type );
		$res		=	0;
		$results	=	array( 'items'=>array() );
		$text		=	'';
		
		if ( count( $promotions ) ) {
			foreach ( $promotions as $p ) {
				if ( (int)$p->user_id && (int)$p->user_id != $user->id ) {
					continue;
				}
				if ( isset( $params['target'] ) && $params['target'] ) {
					if ( $params['target'] == 'order2' && $p->target == 0 ) {
						// OK
					} elseif ( $params['target'] == 'order' && $p->target == -1 ) {
						// OK
					} elseif ( ( $params['target'] == 'product' && $p->target == 1 )
						    || ( $params['target'] == 'product2' && $p->target == 2 ) ) {
						if ( $p->target_products == 0 ) {
							// OK

							if ( isset( $p->target_type ) && $p->target_type ) {
								$continue					=	true;
								$product_def				=	JCckEcommerce::getProductDefinition( $p->target_type );
								$content_types				=	explode( '||', $product_def->content_type );
								$item_type					=	'';
								
								if ( isset( $params['target_id'] ) && $params['target_id'] && isset( $items[$params['target_id']] ) ) {
									if ( is_object( $items[$params['target_id']] ) ) {
										$item_type	=	$items[$params['target_id']]->type;
									} else {
										$object		=	current( $items[$params['target_id']] );
										$item_type	=	$object->type;
									}
								}
								if ( $item_type ) {
									if ( count( $content_types ) ) {
										foreach ( $content_types as $content_type ) {
											if ( $content_type == $item_type ) {
												$continue		=	false;
												break;
											}
										}
									}
								}
								if ( $continue ) {
									continue;
								}
							}
						} elseif ( $p->target_products == 2 ) {
							$products	=	self::getTargets( $p->id );

							if ( !isset( $products[$params['target_id']] ) ) {
								continue;
							}
						} elseif ( $p->target_products == -2 ) {
							$products	=	self::getTargets( $p->id );
							
							if ( isset( $products[$params['target_id']] ) ) {
								continue;
							}
						} else {
							continue;
						}
					} else {
						continue;
					}
				}
				if ( $p->usage_limit ) {
					$query	=	'SELECT COUNT(a.id)'
							.	' FROM #__cck_more_ecommerce_promotion_usage AS a'
							.	' LEFT JOIN #__cck_more_ecommerce_orders AS b ON b.id = a.order_id'
							.	' WHERE a.promotion_id = '.(int)$p->id
							.	' AND b.user_id = '.(int)$user->id
							.	' AND b.state > 0';
					$usage	=	(int)JCckDatabase::loadResult( $query );

					if ( $usage > 0 ) {
						continue;
					}
				}
				if ( $p->target_attributes != '' ) {
					$attribute	=	false;

					if ( isset( $params['target_id'] ) && $params['target_id'] ) {
						if ( isset( $items[$params['target_id']] ) ) {
							$attributes	=	json_decode( $p->target_attributes );

							if ( is_object( $attributes ) ) {			
								$target	=	$attributes->trigger;

								if ( $target ) {
									if ( !is_array( $items[$params['target_id']] ) ) {
										$item_list  =   array( '_'=>$items[$params['target_id']] );
									} else {
										$item_list	=	$items[$params['target_id']];
									}
									if ( count( $item_list ) ) {
										foreach ( $item_list as $item ) {
											if ( isset( $item->$target ) ) {
												if ( $attributes->match == 'isFilled' ) {
													if ( $item->$target != '' ) {
														$attribute	=	true;
														break;
													}
												} elseif ( $attributes->match == 'isEmpty' ) {
													if ( $item->$target == '' ) {
														$attribute	=	true;
														break;
													}
												} elseif ( $attributes->match == 'isEqual' ) {
													if ( $item->$target == $attributes->values ) {
														$attribute	=	true;
														break;
													}
												}
											}
										}
									}
								}
							}
						}
					}
					if ( !$attribute ) {
						continue;
					}
				}
				if ( $p->type == 'coupon' ) {
					if ( $p->code && ( $p->code != @$params['code'] ) ) {
						continue;
					}
				}
				$groups		=	explode( ',', $p->groups );
				
				if ( count( array_intersect( $my_groups, $groups ) ) > 0 ) {
					switch ( $p->discount ) {
						case 'free':
							$promotion					=	0;
							$res						=	$promotion;
							$text						=	JText::_( 'COM_CCK_FREE' );
							$total						=	$promotion;
							$results['items'][$p->id]	=	array(
																'code'=>@(string)$params['code'],
																'id'=>$p->id,
																'promotion'=>$p->discount,
																'promotion_amount'=>'',
																'target'=>@$params['target'],
																'text'=>$text,
																'title'=>$p->title,
																'type'=>$p->type
															);
							break;
						case 'minus':
							$promotion					=	$p->discount_amount * -1;
							$res						+=	$promotion;
							$text						=	'- '.JCckEcommerceCurrency::format( $p->discount_amount );
							$total						+=	$promotion;
							$total						=	( $total < 0 ) ? 0 : $total;
							$results['items'][$p->id]	=	array(
																'code'=>@(string)$params['code'],
																'id'=>$p->id,
																'promotion'=>$p->discount,
																'promotion_amount'=>(string)$promotion,
																'target'=>@$params['target'],
																'text'=>$text,
																'title'=>$p->title,
																'type'=>$p->type
															);
							break;
						case 'percentage':
							$promotion					=	$total * $p->discount_amount / 100;
							$res						=	$promotion;
							$text						=	'- '.$p->discount_amount.' %';
							$total						=	$total - $promotion;
							$results['items'][$p->id]	=	array(
																'code'=>@(string)$params['code'],
																'id'=>$p->id,
																'promotion'=>$p->discount,
																'promotion_amount'=>(string)$promotion,
																'target'=>@$params['target'],
																'text'=>$text,
																'title'=>$p->title,
																'type'=>$p->type
															);
							break;
						case 'prorata':
							$promotion					=	$total - self::getProrata( $total, $p->discount_amount, ( isset( $params['target_id'] ) ? $params['target_id'] : '0' ) );
							$res						=	$promotion;
							$text						=	'- '.(string)JCckEcommerceCurrency::format( $promotion );
							$total						=	$total - $promotion;
							$total						=	( $total < 0 ) ? 0 : $total;
							$results['items'][$p->id]	=	array(
																'code'=>@(string)$params['code'],
																'id'=>$p->id,
																'promotion'=>$p->discount,
																'promotion_amount'=>(string)$promotion,
																'target'=>@$params['target'],
																'text'=>$text,
																'title'=>$p->title,
																'type'=>$p->type
															);
							break;
						case 'set':
							$promotion					=	$total - $p->discount_amount;
							$res						=	$promotion;
							$text						=	'"'.JCckEcommerceCurrency::format( $p->discount_amount ).'"';
							$total						=	$total - $promotion;
							$results['items'][$p->id]	=	array(
																'code'=>@(string)$params['code'],
																'id'=>$p->id,
																'promotion'=>$p->discount,
																'promotion_amount'=>(string)$promotion,
																'target'=>@$params['target'],
																'text'=>$text,
																'title'=>$p->title,
																'type'=>$p->type,
															);
                            break;
						default:
							break;
					}

					if ( isset( $results['items'][$p->id] ) && $p->usage_limit ) {
						$results['items'][$p->id]['usage_limit']	=	true;
					} else {
						$results['items'][$p->id]['usage_limit']	=	false;
					}
				}
			}
		}

		if ( $res ) {
			$results['text']	=	$text;
			$results['total']	=	(float)$res;
			
			return (object)$results;
		}

		return null;
	}

	// count
	public static function count( $type )
	{	
		return count( JCckEcommerce::getPromotions( $type ) );
	}

	// discount
	public static function discount( $promotion, &$total, &$balance )
	{
		$total_to_pay	=	$total;

		switch ( $promotion->discount ) {
			case 'free':
				$total		=	0;
				break;
			case 'minus':
				if ( !$promotion->discount_amount ) {
					return false;
				}
				$total		=	$total - $promotion->discount_amount;

				if ( $total < 0 ) {
					$balance	=	abs( $total );
					$total		=	0;
				} else {
					$balance	=	0;
				}
				break;
			case 'percentage':
				$discount	=	$total * $promotion->discount_amount / 100;
				$total		=	$total - $promotion;
				break;
			case 'prorata':
				if ( !$promotion->discount_amount ) {
					return false;
				}

				$total		=	$total - $promotion->discount_amount;

				if ( $total < 0 ) {
					$balance	=	abs( $total );
					$total		=	0;
				} else {
					$balance	=	0;
				}
				break;
			case 'set':
				$total		=	$promotion->discount_amount;
				break;
			default:
				break;
		}

		return $total_to_pay != $total ? true : false;
	}

	// getCurrentCoupon
	public static function getCurrentCoupon( $strict = false )
	{
		$coupon	=	JFactory::getApplication()->input->getString( 'coupon', '' );

		if ( $coupon == '' && !$strict ) {
			if ( $order_id = JCckEcommerce::isCheckout( true ) ) {
				// OK
			} else {
				$user		=	JCck::getUser();
				require_once JPATH_SITE.'/modules/mod_cck_ecommerce_cart/helper.php';
				$cart_id	=	modCCKeCommerceCartHelper::getActive( $user, 'cart' );
				$cart		=	JCckEcommerce::getCart( (int)$cart_id );
				$order_id	=	$cart->order_id;	
			}
			if ( $order_id ) {
				$order	=	JCckDatabaseCache::loadObject( 'SELECT info_promotions FROM #__cck_more_ecommerce_orders AS a WHERE a.id = '.(int)$order_id );
				
				if ( is_object( $order ) && $order->info_promotions ) {
					$order_promotions	=	json_decode( $order->info_promotions );

					if ( count( $order_promotions ) ) {
						foreach ( $order_promotions as $k=>$v ) {
							if ( isset( $v->type, $v->code ) && $v->type == 'coupon' ) {
								$coupon		=	$v->code;
								break;
							}
						}
					}
				}
			}
		}

		return $coupon;
	}

	// getProrata
	public static function getProrata( $pricing, $ref_ids, $target_id = 0 )
	{
		$prorata	=	$pricing;
		$ref_ids	=	$ref_ids != '' ? $ref_ids : '0';

		$query		=	'SELECT b.price, a.end_date, c.renew_time'
					.	' FROM #__cck_more_ecommerce_subscriptions AS a'
					.	' LEFT JOIN #__cck_more_ecommerce_order_product AS b ON b.order_id = a.order_id'
					.	' LEFT JOIN #__cck_more_ecommerce_subscription_definitions AS c ON c.name = a.type'
					.	' WHERE a.state = 1'
					.	' AND a.user_id = '.JFactory::getUser()->id
					.	' AND b.product_id IN ('.$ref_ids.')'
					.	' AND b.product_id NOT IN('.(int)$target_id.')'
					.	' ORDER BY a.id DESC, price DESC'
					;

		$promo		=	JCckDatabase::loadObject( $query );

		if ( $promo !== null && $promo->end_date ) {
			$date1		=	JFactory::getDate()->modify( 'first day of this month' )->setTime( 0, 0, 0 );
			$date2		=	JFactory::getDate( $promo->end_date )->setTime( 0, 0, 0 );

			if ( $date2 <= JFactory::getDate()->modify( '+'.$promo->renew_time.' days' )->setTime( 0, 0, 0 ) ) { // Renew
				return $prorata;
			}

			$diff		=	$date1->diff( $date2 );
			$months		=	$diff->y * 12 + $diff->m;
			$prorata	=	( ( $pricing / 12 ) - ( $promo->price / 12 ) ) * $months;
		}

		return $prorata;
	}

	// getTargets
	public static function getTargets( $id )
	{
		static $cache	=	array();

		if ( !isset( $cache[$id] ) ) {
			$cache[$id]	=	JCckDatabase::loadColumn( 'SELECT product_id FROM #__cck_more_ecommerce_promotion_product WHERE promotion_id = '.(int)$id );
			$cache[$id]	=	array_flip( $cache[$id] );
		}

		return $cache[$id];
	}
}
?>