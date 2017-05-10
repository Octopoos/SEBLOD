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

// JCckEcommerceCart
abstract class JCckEcommerceCart
{
	// _compute
	protected static function _compute( $math, $parts, $item )
	{
		$doMath	=	true;

		if ( !is_numeric( $parts[0] ) ) {
			if ( isset( $item->{$parts[0]} ) && $item->{$parts[0]} != '' ) {
				$parts[0]	=	$item->{$parts[0]};
			} else {
				$doMath	=	false;
			}
		}
		if ( !is_numeric( $parts[1] ) ) {
			if ( isset( $item->{$parts[1]} ) && $item->{$parts[1]} != '' ) {
				$parts[1]	=	$item->{$parts[1]};
			} else {
				$doMath	=	false;
			}
		}
		if ( $doMath ) {
			switch ( $math ) {
				case '*':
					return ( $parts[0] * $parts[1] );
					break;
				case '/':
					return ( $parts[0] / $parts[1] );
					break;
				default:
					break;
			}
		}

		return 1;
	}

	// computeItem
	public static function computeItem( $item, $formula )
	{
		$cur	=	array();
		$do		=	'';
		$res	=	0;

		if ( is_array( $formula ) && count( $formula ) ) {
			foreach ( $formula as $f ) {
				$value	=	'';

				if ( $f->type == 'math' ) {
					$do	=	$f->value;
				} else {
					if ( $f->type == 'dynamic' ) {
						if ( strpos( $f->value, '*' ) !== false ) {
							$parts	=	explode( '*', $f->value );
							$value	=	self::_compute( '*', $parts, $item );
						} elseif ( strpos( $f->value, '/' ) !== false ) {
							$parts	=	explode( '/', $f->value );
							$value	=	self::_compute( '/', $parts, $item );
						} else {
							$value	=	( isset( $item->{$f->value} ) && $item->{$f->value} != '' ) ? $item->{$f->value} : 1;
						}
					} else {
						$value	=	$f->value;
					}
					if ( !isset( $cur['a'] ) ) {
						$cur['a']	=	$value;
					} else {
						$cur['b']	=	$value;
					}
				}
				if ( isset( $cur['a'] ) && isset( $cur['b'] ) ) {
					switch ( $do ) {
						case '*':
							$res		=	$cur['a'] * $cur['b'];
							$cur['a']	=	$res;
							$do			=	'';
							unset( $cur['b'] );
							break;
						case '/':
							$res		=	$cur['a'] / $cur['b'];
							$cur['a']	=	$res;
							$do			=	'';
							unset( $cur['b'] );
							break;
						default:
							break;
					}
				}
			}
		}
		
		return $res;
	}

	// countItems
	public static function countItems( $definition )
	{
		static $cache	=	array();
		$count			=	0;
		$user			=	JCck::getUser();
		
		if ( !isset( $cache[$definition] ) ) {
			require_once JPATH_SITE.'/modules/mod_cck_ecommerce_cart/helper.php';
			$items	=	modCCKeCommerceCartHelper::getItems( $user, $definition );

			if ( is_array( $items ) ) {
				$cache[$definition]	=	(int)count( $items, COUNT_RECURSIVE ) - (int)count( $items );
			} else {
				$cache[$definition]	=	0;
			}
		}
		
		return $cache[$definition];
	}

	// countUserCarts
	public static function countUserCarts( $definition )
	{
		return (int)JCckDatabase::loadResult( 'SELECT COUNT(id) FROM #__cck_more_ecommerce_carts AS a WHERE a.'.JCck::getUser()->where_clause. ' AND a.type = "'.$definition.'"' );
	}

	// hasQuantity
	public static function hasQuantity( $type )
	{
		$cart_def	=	JCckEcommerce::getCartDefinition( $type );

		return $cart_def->quantity;
	}

	// isValidType
	public static function isValidType( $type )
	{
		return JCckDatabase::loadResult( 'SELECT COUNT(id) FROM #__cck_more_ecommerce_cart_definitions WHERE name = "'.JCckDatabase::escape( $type ).'" AND published = 1' );
	}

	// prepareFormula
	public static function prepareFormula( $formula )
	{
		$formula	=	trim( $formula );
		$formula	=	str_replace( ' ', '', $formula );
		$parts		=	array();

		if ( $formula != '' ) {
			$matches	=	'';
			$search		=	'#(\[([a-zA-Z0-9_\*\/]*)\])|([\*\/])|([0-9].*)#';
			preg_match_all( $search, $formula, $matches );
			
			if ( count( $matches[0] ) ) {
				foreach ( $matches[0] as $k=>$v ) {
					if ( $matches[2][$k] != '' ) {
						$p			=	new stdClass;
						$p->type	=	'dynamic';
						$p->value	=	$matches[2][$k];
						$parts[]	=	$p;
					}
					if ( $matches[3][$k] != '' ) {
						$p			=	new stdClass;
						$p->type	=	'math';
						$p->value	=	$matches[3][$k];
						$parts[]	=	$p;
					}
					if ( $matches[4][$k] != '' ) {
						$p			=	new stdClass;
						$p->type	=	'static';
						$p->value	=	$matches[4][$k];
						$parts[]	=	$p;
					}
				}
			}
		}
		
		return $parts;
	}
}
?>