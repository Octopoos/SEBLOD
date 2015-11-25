<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: tax.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommerceTax
abstract class JCckEcommerceTax
{
	// apply
	public static function apply( $type, &$total, $params = array() )
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
			foreach ( $taxes as $p ) {
				if ( isset( $params['target'] ) && $params['target'] ) {
					if ( $params['target'] == 'order' && $p->target == 0 ) {
						// OK
					} elseif ( $params['target'] == 'product' && $p->target == 1 ) {
						// OK
					} else {
						continue;
					}
				}
				$groups		=	explode( ',', $p->groups );
				
				if ( count( array_intersect( $my_groups, $groups ) ) > 0 ) {
					switch ( $p->tax ) {
						case 'plus':
							$tax				=	$p->tax_amount;
							$res				+=	$tax;
							$total				+=	$tax;
							$results['items'][$p->id]	=	array( 'type'=>$p->type, 'tax'=>$p->tax, 'tax_amount'=>(string)$tax, 'title'=>$p->title );
							break;
						case 'percentage':
							$tax				=	$total * $p->tax_amount / 100;
							$res				+=	$tax;
							$total				+=	$tax;
							$results['items'][$p->id]	=	array( 'type'=>$p->type, 'tax'=>$p->tax, 'tax_amount'=>(string)$tax, 'title'=>$p->title );
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