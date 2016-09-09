<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: promotion.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
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
					
					if ( isset( $items[$params['target_id']] ) ) {
						$attributes	=	json_decode( $p->target_attributes );

						if ( is_object( $attributes ) ) {						
							$target	=	$attributes->trigger;

							if ( $target && isset( $items[$params['target_id']]->$target ) ) {
								if ( $attributes->match == 'isFilled' ) {
									if ( $items[$params['target_id']]->$target != '' ) {
										$attribute	=	true;
									}
								} elseif ( $attributes->match == 'isEmpty' ) {
									if ( $items[$params['target_id']]->$target == '' ) {
										$attribute	=	true;
									}
								} elseif ( $attributes->match == 'isEqual' ) {
									if ( $items[$params['target_id']]->$target == $attributes->values ) {
										$attribute	=	true;
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