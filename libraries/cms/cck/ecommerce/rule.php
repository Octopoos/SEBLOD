<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: rule.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommerceRule
abstract class JCckEcommerceRule
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
		$cost		=	'';
		$rules		=	JCckEcommerce::getShippingRules( $type, $my_zones );
		$total2		=	(int)$total;

		if ( count( $rules ) ) {
			foreach ( $rules as $r ) {
				if ( isset( $r->target_type ) && $r->target_type ) {
					if ( !isset( $params['target_types'][$r->target_type] ) ) {
						continue;
					}
				}
				if ( $total2 < (int)$r->min ) {
					continue;
				}
				if ( (int)$r->max && $total2 > (int)$r->max ) {
					continue;
				}
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
}
?>