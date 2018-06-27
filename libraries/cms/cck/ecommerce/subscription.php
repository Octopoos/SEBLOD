<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: subscription.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommerceSubscription
abstract class JCckEcommerceSubscription
{
	protected static function _getItems( $user_id = 0, $state = 1, $date_state = '' )
	{
		$db		=	JFactory::getDbo();
		$now	=	JFactory::getDate()->toSql();
		$null	=	$db->getNullDate();
		$query	=	$db->getQuery( true );

		$query->select( 'a.id, a.start_date, a.end_date, b.groups, a.user_id' )
			  ->from( '`#__cck_more_ecommerce_subscriptions` AS a' )
			  ->join( 'LEFT', '`#__cck_more_ecommerce_subscription_definitions` AS b ON b.name = a.type' );

		$query->where( 'a.state = '.(int)$state );

		if ( (int)$user_id > 0 ) {
			$query->where( 'a.user_id = '.(int)$user_id );
		}

		if ( $date_state == 'expired' ) {
			$query->where( '( a.end_date != '.$db->quote( $null ).' AND a.end_date < '.$db->quote( $now ).' )' );
		} elseif ( $date_state == 'active' ) {
			$query->where( '( a.start_date = '.$db->quote( $null ).' OR a.start_date <= '.$db->quote( $now ).' )'
					. ' AND ( a.end_date = '.$db->quote( $null ).' OR a.end_date >= '.$db->quote( $now ).' )' );
		} elseif ( $date_state == 'upcoming' ) {
			// TODO
		}

		$db->setQuery( $query );

		return $db->loadObjectList();
	}

	// expire
	public static function expire()
	{
		$expired_items		=	self::_getItems( 0, 1, 'expired' );
		$expired_triggers	=	array();

		if ( count( $expired_items ) ) {
			$user_groups	=	array();

			foreach ( $expired_items as $item ) {
				if ( !$item->id || !$item->user_id ) {
					continue;
				}

				if ( !isset( $user_groups[$item->user_id] ) ) {
					$user_groups[$item->user_id]	=	array();
				}

				// Set as Expired
				JCckDatabase::execute( 'UPDATE #__cck_more_ecommerce_subscriptions SET state = 0 WHERE id = '.(int)$item->id );

				$expired_triggers[]				=	array(
														'author'=>$item->user_id,
														'pk'=>(int)$item->id
													);
				$user_groups[$item->user_id]	=	array_merge( $user_groups[$item->user_id], explode( ',', $item->groups ) );
			}

			foreach ( $user_groups as $user_id=>$groups ) {
				$groups	=	array_flip( $groups );

				// Unset from Groups
				if ( count( $groups ) ) {
					$valid_groups	=	array();
					$active_items	=	self::_getItems( $user_id, 1, 'active' );

					if ( count( $active_items ) ) {
						foreach ( $active_items as $item ) {
							$valid_groups	=	array_merge( $valid_groups, explode( ',', $item->groups ) );
						}
						$valid_groups	=	array_flip( $valid_groups );
					}
					foreach ( $groups as $k=>$v ) {
						$k	=	(int)$k;

						if ( $k && !isset( $valid_groups[$k] ) ) {
							JUserHelper::removeUserFromGroup( $user_id, $k );
						}
					}
				}
			}

			// Trigger "onCckSubsriptionExpiration" after suitable groups have been removed
			if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
				if ( count( $expired_triggers ) ) {
					foreach ( $expired_triggers as $trigger_config ) {
						JCckToolbox::process( 'onCckSubscriptionExpiration', $trigger_config );
					}
				}
			}
		}
	}

	// getActive
	public static function getActive( $user_id, $date_state = '' )
	{
		return self::_getItems( $user_id, 1, $date_state );
	}

	// getExpired
	public static function getExpired( $user_id, $date_state = '' )
	{
		return self::_getItems( $user_id, 0, $date_state );
	}
}
?>