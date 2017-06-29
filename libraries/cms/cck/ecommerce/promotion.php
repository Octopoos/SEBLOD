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
				if ( isset( $params['target'] ) && $params['target'] ) {
					if ( $params['target'] == 'order2' && $p->target == 0 ) {
						// OK
					} elseif ( $params['target'] == 'order' && $p->target == -1 ) {
						// OK
					} elseif ( ( $params['target'] == 'product' && $p->target == 1 )
						    || ( $params['target'] == 'product2' && $p->target == 2 ) ) {
						if ( $p->target_products == 0 ) {
							// OK
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
																'promotion'=>$p->discount,
																'promotion_amount'=>(string)$promotion,
																'target'=>@$params['target'],
																'text'=>$text,
																'title'=>$p->title,
																'type'=>$p->type
															);
                            break;
						default:
							break;
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

	// getCurrentCoupon
	public static function getCurrentCoupon( $strict = false )
	{
		$coupon	=	JFactory::getApplication()->input->getString( 'coupon', '' );

		if ( $coupon == '' && !$strict ) {
			$user		=	JCck::getUser();
			require_once JPATH_SITE.'/modules/mod_cck_ecommerce_cart/helper.php';
			$cart_id	=	modCCKeCommerceCartHelper::getActive( $user, 'cart' );
			$cart		=	JCckEcommerce::getCart( (int)$cart_id );
			$order_id	=	$cart->order_id;
			
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