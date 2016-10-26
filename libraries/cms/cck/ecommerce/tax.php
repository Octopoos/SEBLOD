<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: tax.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommerceTax
abstract class JCckEcommerceTax
{
	// apply
	public static function apply( $type, &$total, $items, $params = array() )
	{
		$user		=	JCck::getUser();
		$my_groups	=	$user->groups; /* $user->getAuthorisedGroups(); */
		$my_zones	=	JCckEcommerce::getUserZones();

		$currency	=	JCckEcommerce::getCurrency();
		$res		=	0;
		$results	=	array( 'items'=>array() );
		$tax		=	'';
		$taxes		=	JCckEcommerce::getTaxes( $type, $my_zones );

		if ( count( $taxes ) ) {
			foreach ( $taxes as $t ) {
				if ( isset( $params['target'] ) && $params['target'] ) {
					if ( $params['target'] == 'order' && $t->target == 0 ) {
						// OK
					} elseif ( $params['target'] == 'product' && $t->target == 1 ) {
						// OK
					} elseif ( $params['target'] == 'shipping' && $t->target == 3 ) {
						// OK
					} else {
						continue;
					}
				}
				$groups		=	explode( ',', $t->groups );
				
				if ( count( array_intersect( $my_groups, $groups ) ) > 0 ) {
					if ( isset( $items[@$params['target_id']]->quantity ) && $items[$params['target_id']]->quantity ) {
						$quantity	=	$items[$params['target_id']]->quantity;
					} else {
						$quantity	=	1;
					}
					switch ( $t->tax ) {
						case 'plus':

							$tax						=	(float)number_format( $t->tax_amount, 2 );

							if ( $params['target'] == 'product' ) {
								$tax					=	$tax * $quantity;
							}
							$res						+=	$tax;
							$total						+=	$tax;
							$results['items'][$t->id]	=	array(
																'target'=>@$params['target'],
																'tax'=>$t->tax,
																'tax_amount'=>(string)$tax,
																'text'=>'',
																'title'=>$t->title,
																'type'=>$t->type
															);
							break;
						case 'percentage':
							$tax						=	(float)number_format( $total * $t->tax_amount / 100, 2 );
							$res						+=	$tax;
							$total						+=	$tax;
							$results['items'][$t->id]	=	array(
																'target'=>@$params['target'],
																'tax'=>$t->tax,
																'tax_amount'=>(string)$tax,
																'text'=>'',
																'title'=>$t->title,
																'type'=>$t->type
															);

							break;
						case 'product_amount':
							$tax						=	0;
							
							if ( $params['target'] == 'shipping' ) {
								continue;
							} elseif ( $params['target'] == 'product' ) {
								if ( !isset( $items[$params['target_id']] ) ) {
									continue;
								}
								if ( empty( $items[$params['target_id']]->price ) ) {
									continue;
								}
								$tax					=	(float)number_format( $items[$params['target_id']]->tax, 2 );
								$tax					=	$tax * $quantity;
							} else {
								if ( count( $items ) ) {
									if ( isset( $params['target_id'] ) && $params['target_id'] ) {
										if ( empty( $items[$params['target_id']]->price ) ) {
											continue;
										}
										$tax			=	(float)number_format( $items[$params['target_id']]->tax, 2 );
										$tax			=	$tax * $quantity;
									} else {
										foreach ( $items as $item ) {
											if ( empty( $item->price ) ) {
												continue;
											}
											if ( isset( $item->tax ) && $item->tax != '' ) {
												$amount	=	(float)number_format( $item->tax, 2 );

												if ( isset( $item->quantity ) && $item->quantity ) {
													$qty	=	$item->quantity;
												} else {
													$qty	=	1;
												}
												$amount		=	$amount * $qty;
												$tax		+=	$amount;
											}
										}
									}
								}
							}
							$res						+=	$tax;
							$total						+=	$tax;
							$results['items'][$t->id]	=	array(
																'target'=>@$params['target'],
																'tax'=>$t->tax,
																'tax_amount'=>(string)$tax,
																'text'=>'',
																'title'=>$t->title,
																'type'=>$t->type
															);
							break;
						default:
							break;
					}
				}
			}
		}

		if ( $res ) {
			$results['total']	=	(float)$res;
			
			return (object)$results;
		}

		return null;
	}
}
?>