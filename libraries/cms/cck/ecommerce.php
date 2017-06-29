<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: ecommerce.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommerce
abstract class JCckEcommerce
{
	public static $_me			=	'cck_ecommerce';
	public static $_config		=	NULL;
	
	public static $currency		=	NULL;
	public static $promotions	=	NULL;
	public static $rules		=	NULL;
	public static $taxes		=	NULL;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Config
	
	// _setConfig
	public static function _setConfig()
	{		
		if ( self::$_config ) {
			return self::$_config;
		}

		$config			=	new stdClass;
		$config->params =	JComponentHelper::getParams( 'com_'.self::$_me );
		
		self::$_config	=&	$config;
	}
	
	// getConfig
	public static function getConfig()
	{		
		if ( ! self::$_config ) {
			self::_setConfig();
		}
		
		return self::$_config;
	}
	
	// getConfig_Param
	public static function getConfig_Param( $name, $default = '' )
	{
		if ( ! self::$_config ) {
			self::_setConfig();
		}
		
		return self::$_config->params->get( $name, $default );
	}
	
	// getUIX
	public static function getUIX()
	{
		return ( self::getConfig_Param( 'uix', '' ) == 'nano' ) ? 'compact' : 'full';
	}

	// isOffline
	public static function isOffline()
	{
		return ( ( self::getConfig_Param( 'offline' ) == 1 || ( self::getConfig_Param( 'offline' ) == 2 && !JFactory::getUser()->authorise( 'core.admin' ) ) ) ) ? true : false;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Cart
	
	// getCart
	public static function getCart( $id )
	{
		static $cache	=	array();
		
		if ( !isset( $cache[$id] ) ) {
			$cache[$id]	=	JCckDatabase::loadObject( 'SELECT id, title, type, order_id, permanent, state FROM #__cck_more_ecommerce_carts WHERE id = '.(int)$id );
		}
		
		return $cache[$id];
	}

	// getCartDefinition
	public static function getCartDefinition( $name )
	{
		static $definitions	=	array();
		
		if ( !isset( $definitions[$name] ) ) {
			$definitions[$name]	=	JCckDatabase::loadObject( 'SELECT title, name, storage_location, storage_table, storage_field, formula, multicart, multistore, ordering, persistent, quantity, request, request_code, request_payment, request_payment_table, request_payment_field, request_payment_field_live, request_payment_field_live_options, request_shipping, request_shipping_field, request_state_id'
															. ' FROM #__cck_more_ecommerce_cart_definitions WHERE name = "'.JCckDatabase::escape( $name ).'"' );
			if ( strpos( $definitions[$name]->request_payment_field, '$' ) !== false ) {
				$definitions[$name]->request_payment_field	=	str_replace( '$', strtolower( JCckEcommerce::getCurrency()->code ), $definitions[$name]->request_payment_field );
			}
			if ( $definitions[$name]->request_payment_field_live != '' ) {
				JPluginHelper::importPlugin( 'cck_field_live' );

				$config			=	array();
				$field			=	(object)array(
										'live'=>$definitions[$name]->request_payment_field_live,
										'live_options'=>$definitions[$name]->request_payment_field_live_options,
									);
				$suffix			=	'';
				
				JEventDispatcher::getInstance()->trigger( 'onCCK_Field_LivePrepareForm', array( &$field, &$suffix, &$config ) );

				if ( $suffix != '' ) {
					if ( $definitions[$name]->request_payment_field != '' ) {
						$definitions[$name]->request_payment_field	.=	'_'.$suffix;
					} else {
						$definitions[$name]->request_payment_field	=	$suffix;
					}
				}
			}
			$definitions[$name]->request_state	=	0;
			
			if ( $definitions[$name]->formula != '' ) {
				$definitions[$name]->formula		=	JCckEcommerceCart::prepareFormula( $definitions[$name]->formula );
			}
			if ( $definitions[$name]->request_state_id ) {
				$definitions[$name]->request_state	=	JCckDatabase::loadResult( 'SELECT value FROM #__cck_more_ecommerce_order_states WHERE id = '.(int)$definitions[$name]->request_state_id );
			}
		}
		
		return $definitions[$name];
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Currency
	
	// getCurrency
	public static function getCurrency( $id = 0 )
	{
		static $currency	=	NULL;
		
		if ( (int)$id > 0 ) {
			return JCckDatabase::loadObject( 'SELECT a.id, a.title, a.code, a.conversion_rate, a.lft, a.rgt'
										   . ' FROM #__cck_more_ecommerce_currencies AS a WHERE a.id = "'.$id.'"' );
		}
		if ( !is_object( $currency ) ) {
			$app		=	JFactory::getApplication();
			$user		=	JCck::getUser();
			
			$code		=	'';
			if ( self::getConfig_Param( 'currency_dynamic', 0 ) ) {
				$code	=	$app->input->cookie->get( 'cck_ecommerce_currency', '' );
			}
			if ( !$code ) {
				$property	=	JCckEcommerce::getConfig_Param( 'currency_user' );
				if ( isset( $user->$property ) ) {
					$code	=	$user->$property;
				}
				if ( !$code ) {
					$code	=	JCckEcommerce::getConfig_Param( 'currency', 0 );
					if ( !$code ) {
						$lang	=	JFactory::getLanguage();
						if ( !$lang->hasKey( 'COM_CCK_CURRENCY_AUTO' ) == 1 ) {
							$lang->load( 'com_cck_default', JPATH_SITE );
						}
						$code	=	JText::_( 'COM_CCK_CURRENCY_AUTO' );
					}
				}
			}
			if ( !$code ) {
				$code	=	'USD';
			}
			$currency	=	JCckDatabase::loadObject( 'SELECT a.id, a.title, a.code, a.conversion_rate, a.lft, a.rgt'
													. ' FROM #__cck_more_ecommerce_currencies AS a WHERE a.code = "'.$code.'"' );
		}
		
		return $currency;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Orders

	// getOrderByPayKey
	public static function getOrderByPayKey( $pay_key, $isCached = true )
	{
		static $cache	=	array();
		
		if ( !isset( $cache[$pay_key] ) ) {
			$cache[$pay_key]	=	JCckDatabase::loadObject( 'SELECT a.number, b.id, b.pk, a.type, a.state, a.user_id, a.session_id, a.total, a.total_ht, a.total_paid, a.weight, a.invoice, a.info_billing'
															. ' FROM #__cck_more_ecommerce_orders AS a'
															. ' LEFT JOIN #__cck_core AS b ON (b.pk = a.id AND b.storage_location = "cck_ecommerce_order")'
															. ' WHERE a.pay_key = "'.$pay_key.'"' );
			if ( !$isCached ) {
				$temp	=	$cache[$pay_key];
				
				unset( $cache[$pay_key] );

				return $temp;
			}
		}

		return $cache[$pay_key];
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Payments

	// getGateway
	public static function getGateway( $type )
	{
		static $cache	=	array();
		
		if ( !isset( $cache[$type] ) ) {
			$cache[$type]			=	JCckDatabase::loadObject( 'SELECT id, title, options, token'
																. ' FROM #__cck_more_ecommerce_gateways WHERE type = "'.$type.'"' );
			$cache[$type]->options	=	new JRegistry( $cache[$type]->options );
		}
		
		return $cache[$type];
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Products

	// getCartDefinition
	public static function getProductDefinition( $name )
	{
		static $definitions	=	array();
		
		if ( !isset( $definitions[$name] ) ) {
			$definitions[$name]	=	JCckDatabase::loadObject( 'SELECT title, name, type, content_type, quantity, request_stock_field, request_weight_field, attribute, attributes'
															. ' FROM #__cck_more_ecommerce_product_definitions WHERE name = "'.JCckDatabase::escape( $name ).'"' );

			if ( $definitions[$name]->attributes == '' ) {
				$definitions[$name]->attributes	=	array();
			} else {
				$definitions[$name]->attributes	=	explode( '||', $definitions[$name]->attributes );
				$definitions[$name]->attributes	=	array_flip( $definitions[$name]->attributes );
			}
		}
		
		return $definitions[$name];
	}

	// getTotal
	public static function getTotal( $items, $cart_type, $params = array() )
	{
		$apply_promotions	=	false;
		$apply_taxes		=	false;
		$cart_definition	=	self::getCartDefinition( $cart_type );
		$total				=	0;
		
		if ( isset( $params['apply_promotions'] ) ) {
			$apply_promotions	=	$params['apply_promotions'];

			unset( $params['apply_promotions'] );
		}
		if ( isset( $params['apply_taxes'] ) ) {
			$apply_taxes		=	$params['apply_taxes'];

			unset( $params['apply_taxes'] );
		}
		$params['target']	=	'product';
		
		if ( count( $items ) ) {
			foreach ( $items as $item_list ) {
				if ( !is_array( $item_list ) ) {
					$item_list	=	array( '_'=>$item_list );
				}
				if ( count( $item_list ) ) {
					foreach ( $item_list as $item ) {  
						$options				=	$params;
						$options['target_id']	=	$item->product_id;
						$price					=	$item->price;

						// Taxes
						if ( $apply_taxes ) {
							JCckEcommerceTax::apply( '', $price, $items, $options );
						}
						
						// Formula
						if ( !empty( $cart_definition->formula ) ) {
							$item->price	=	$price;
							$price			=	JCckEcommerceCart::computeItem( $item, $cart_definition->formula );
						}
						
						// Promotions
						if ( $apply_promotions ) {
							JCckEcommercePromotion::apply( '', $price, $items, $options );

							$options['target']	=	'product2';
							JCckEcommercePromotion::apply( '', $price, $items, $options );
						}
						
						// Quantity /* Alter Price */
						$total	+=	$price * $item->quantity;
					}
				}
			}
		}
		
		return $total;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Promotions
	
	// getPromotions
	public static function getPromotions( $type = '' )
	{
		if ( !self::$promotions ) {
			self::$promotions	=	self::_setPromotions();
		}
		
		if ( $type ) {
			return ( isset( self::$promotions[$type] ) ) ? self::$promotions[$type] : array();	
		} else {
			$promotions	=	array();
			if ( count( self::$promotions ) ) {
				foreach ( self::$promotions as $k=>$p ) {
					foreach ( $p as $v ) {
						$promotions[]	=	$v;
					}
				}
			}

			return $promotions;
		}
	}
	
	// _setPromotions
	protected static function _setPromotions()
	{
		$db		=	JFactory::getDbo();
		$null	=	$db->getNullDate();
		$now	=	JFactory::getDate()->toSql();

		$promotions	=	JCckDatabase::loadObjectListArray( 'SELECT a.id, a.title, a.type, a.code, a.discount, a.discount_amount, a.groups, a.target, a.target_attributes, a.target_products'
														.  ' FROM #__cck_more_ecommerce_promotions AS a'
														.  ' WHERE a.published = 1'
														.  ' AND (a.publish_up = '.JCckDatabase::quote( $null ).' OR '.'a.publish_up <= '.JCckDatabase::quote( $now ).')'
														.  ' AND (a.publish_down = '.JCckDatabase::quote( $null ).' OR '.'a.publish_down >= '.JCckDatabase::quote( $now ).')'
														.  ' ORDER BY a.title', 'type' );
		
		return $promotions;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Shipping

	// getShippingMethod
	public static function getShippingMethod( $type )
	{
		static $cache	=	array();
		
		if ( !isset( $cache[$type] ) ) {
			$cache[$type]			=	JCckDatabase::loadObject( 'SELECT id, title, options'
																. ' FROM #__cck_more_ecommerce_shipping_methods WHERE type = "'.$type.'"' );
			$cache[$type]->options	=	new JRegistry( $cache[$type]->options );
		}
		
		return $cache[$type];
	}

	// getShippingRules
	public static function getShippingRules( $type = '', $zones = array() )
	{
		if ( !self::$rules ) {
			self::$rules	=	self::_setShippingRules( $zones );
		}
		
		if ( $type ) {
			return ( isset( self::$rules[$type] ) ) ? self::$rules[$type] : array();
		} else {
			$rules	=	array();
			if ( count( self::$rules ) ) {
				foreach ( self::$rules as $k=>$p ) {
					foreach ( $p as $v ) {
						$rules[]	=	$v;
					}
				}
			}

			return $rules;
		}
	}
	
	// _setTaxes
	protected static function _setShippingRules( $zones )
	{
		$db			=	JFactory::getDbo();
		$null		=	$db->getNullDate();
		$now		=	substr( JFactory::getDate()->toSql(), 0, -3 );

		$zones[]	=	0;
		
		$query		=	'SELECT a.id, a.title, a.type, a.cost, a.cost_amount, a.target_products, a.min, a.max, a.mode, a.target_type'
					.	' FROM #__cck_more_ecommerce_shipping_rules AS a'
					.	' LEFT JOIN #__cck_more_ecommerce_zone_rule AS b ON b.rule_id = a.id'
					.	' WHERE a.published = 1'
					.	' AND (a.publish_up = '.JCckDatabase::quote( $null ).' OR '.'a.publish_up <= '.JCckDatabase::quote( $now ).')'
					.	' AND (a.publish_down = '.JCckDatabase::quote( $null ).' OR '.'a.publish_down >= '.JCckDatabase::quote( $now ).')'
					.	' AND b.zone_id IN ('.implode( ',', $zones ).')'
					.	' ORDER BY a.title';
		$rules		=	JCckDatabase::loadObjectListArray( $query, 'type' );

		return $rules;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stores

	// getStore
	public static function getStore( $id )
	{
		static $cache	=	array();
		
		if ( !isset( $cache[$id] ) ) {
			$cache[$id]	=	JCckDatabase::loadObject( 'SELECT id, title, home, parent_id, parent_fee, parent_amount, options'
															. ' FROM #__cck_more_ecommerce_stores WHERE id = '.(int)$id );
			$cache[$id]->options	=	new JRegistry( $cache[$id]->options );
		}
		
		return $cache[$id];
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Taxes
	
	// getTaxes
	public static function getTaxes( $type = '', $zones = array() )
	{
		if ( !self::$taxes ) {
			self::$taxes	=	self::_setTaxes( $zones );
		}
		
		if ( $type ) {
			return ( isset( self::$taxes[$type] ) ) ? self::$taxes[$type] : array();	
		} else {
			$taxes	=	array();
			if ( count( self::$taxes ) ) {
				foreach ( self::$taxes as $k=>$p ) {
					foreach ( $p as $v ) {
						$taxes[]	=	$v;
					}
				}
			}

			return $taxes;
		}
	}
	
	// _setTaxes
	protected static function _setTaxes( $zones )
	{
		$db			=	JFactory::getDbo();
		$null		=	$db->getNullDate();
		$now		=	substr( JFactory::getDate()->toSql(), 0, -3 );

		$zones[]	=	0;
		
		$query		=	'SELECT a.id, a.title, a.type, a.tax, a.tax_amount, a.target_type, a.groups, a.target'
					.	' FROM #__cck_more_ecommerce_taxes AS a'
					.	' LEFT JOIN #__cck_more_ecommerce_zone_tax AS b ON b.tax_id = a.id'
					.	' WHERE a.published = 1'
					.	' AND (a.publish_up = '.JCckDatabase::quote( $null ).' OR '.'a.publish_up <= '.JCckDatabase::quote( $now ).')'
					.	' AND (a.publish_down = '.JCckDatabase::quote( $null ).' OR '.'a.publish_down >= '.JCckDatabase::quote( $now ).')'
					.	' AND b.zone_id IN ('.implode( ',', $zones ).')'
					.	' ORDER BY a.title';
		$taxes		=	JCckDatabase::loadObjectListArray( $query, 'type' );
		
		return $taxes;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Taxes

	// getUserZones
	public static function getUserZones()
	{
		$user	=	JCck::getUser();
		$zones	=	array();

		if ( !( isset( $user->country ) && $user->country != '' ) ) {
			return $zones;
		}
		$where	=	'countries = "'.$user->country.'" OR countries LIKE "'.$user->country.'||%" OR countries LIKE "%||'.$user->country.'" OR countries LIKE "%||'.$user->country.'||%"';
		$items	=	JCckDatabaseCache::loadObjectList( 'SELECT id, profile FROM #__cck_more_ecommerce_zones WHERE published = 1 AND ('.$where.') ORDER BY CHARACTER_LENGTH(countries) ASC' );

		if ( count( $items ) ) {
			foreach ( $items as $item ) {
				$isValid	=	true;

				if ( $item->profile ) {
					$profile	=	json_decode( $item->profile );

					if ( is_object( $profile ) ) {
						$target	=	$profile->trigger;

						if ( $profile->match == 'isFilled' ) {
							if ( $user->$target == '' ) {
								$isValid	=	false;
							}
						} elseif ( $profile->match == 'isEmpty' ) {
							if ( $user->$target != '' ) {
								$isValid	=	false;
							}
						}
					}
				}

				if ( $isValid ) {
					$zones[]	=	$item->id;
				}
			}
		}
		return $zones;
	}
}
?>