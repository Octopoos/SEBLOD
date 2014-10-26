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
	public static function apply( $type, &$total )
	{
		$user		=	JCck::getUser();
		$my_groups	=	$user->getAuthorisedGroups();
		$my_zones	=	JCckEcommerce::getUserZones();

		$currency	=	JCckEcommerce::getCurrency();
		$tax		=	'';
		$taxes		=	JCckEcommerce::getTaxes( $type, $my_zones );
		
		if ( count( $taxes ) ) {
			foreach ( $taxes as $p ) {
				$groups		=	explode( ',', $p->groups );
				if ( count( array_intersect( $my_groups, $groups ) ) > 0 ) {
					switch ( $p->tax ) {
						case 'plus':
							$tax		=	$p->tax_amount;
							$total		+=	$tax;
							break;
						case 'percentage':
							$tax		=	$total * $p->tax_amount / 100;
							$total		+=	$tax;
							break;
						default:
							break;
					}
					
				}
			}
		}

		return $tax;
	}
}
?>