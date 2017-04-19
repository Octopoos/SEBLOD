<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: payment.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class JCckPluginPayment extends JPlugin
{
	protected static $construction	=	'cck_ecommerce_payment';
	
	// g_onCCK_PaymentValidate
	public static function g_onCCK_PaymentValidate( $data, $success, &$config )
	{
		$update	=	'pay_return = "'.JCckDatabase::escape( json_encode( $data['order'] ) ).'",'
				.	'pay_return_payments = "'.JCckDatabase::escape( json_encode( $data['payments'] ) ).'",'
				.	'state = '.$data['order_state'];

		JCckDatabase::execute( 'UPDATE #__cck_more_ecommerce_orders SET '.$update.' WHERE pay_key = "'.$config['pay_key'].'"' );

		if ( !$success ) {
			$event		=	'onCckPaymentFailure';
			$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );

			if ( isset( $processing[$event] ) ) {
				foreach ( $processing[$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new JRegistry( $p->options );
						
						include_once JPATH_SITE.$p->scriptfile;
					}
				}
			}

			return;
		}

		// Cart
		$cart_id	=	(int)JCckDatabase::loadResult( 'SELECT a.id FROM #__cck_more_ecommerce_carts AS a WHERE a.pay_key = "'.$config['pay_key'].'"' );
		
		if ( $cart_id ) {
			$cart	=	JCckEcommerce::getCart( (int)$cart_id );

			JCckDatabase::execute( 'UPDATE #__cck_more_ecommerce_carts SET pay_key = "" WHERE id = '.$cart_id );
			
			if ( !$cart->permanent ) {
				JCckDatabase::execute( 'DELETE a.* FROM #__cck_more_ecommerce_cart_product AS a WHERE a.cart_id = '.$cart_id );
			}
		}

		// Execute Processings (Invoice, Notifications, ...)
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$event      =   'onCckPaymentSuccess';
			$processing =   JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );

			if ( isset( $processing[$event] ) ) {
				foreach ( $processing[$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new JRegistry( $p->options );
						
						include_once JPATH_SITE.$p->scriptfile;
					}
				}
			}
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// g_addProcess
	public static function g_addProcess( $event, $type, &$config, $params, $priority = 3 )
	{
		if ( $event && $type ) {
			$process						=	new stdClass;
			$process->group					=	self::$construction;
			$process->type					=	$type;
			$process->params				=	$params;
			$process->priority				=	$priority;
			$config['process'][$event][]	=	$process;
		}
	}

	// g_getPath
	public static function g_getPath( $type = '' )
	{
		return JUri::root( true ).'/plugins/'.self::$construction.'/'.$type;
	}
}
?>