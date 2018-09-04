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

use Joomla\Utilities\ArrayHelper;

// JCckEcommerceSubscription
abstract class JCckEcommerceSubscription
{
	// _getItems
	protected static function _getItems( $user_id, $state, $date_state, $limit = 0 )
	{
		$db			=	JFactory::getDbo();
		$grouped	=	false;
		$now		=	JFactory::getDate()->toSql();
		$null		=	$db->getNullDate();
		$query		=	$db->getQuery( true );

		$query->select( 'a.id, a.start_date, a.end_date, b.groups, a.user_id' )
			  ->from( '`#__cck_more_ecommerce_subscriptions` AS a' )
			  ->join( 'LEFT', '`#__cck_more_ecommerce_subscription_definitions` AS b ON b.name = a.type' )
			  ->join( 'INNER', '`#__users` AS c ON c.id = a.user_id' )
			  ;

		$query->where( 'a.state = '.(int)$state );

		if ( is_array( $user_id ) ) {
			$grouped	=	true;
			$user_id	=	ArrayHelper::toInteger( $user_id );
			$query->where( 'a.user_id IN ('.implode( ',', $user_id ).')' );
		} else {
			if ( (int)$user_id > 0 ) {
				$query->where( 'a.user_id = '.(int)$user_id );
			}
		}

		if ( $date_state == 'expired' ) {
			$query->where( '( a.end_date != '.$db->quote( $null ).' AND a.end_date < '.$db->quote( $now ).' )' );
		} elseif ( $date_state == 'active' ) {
			$query->where( '( a.start_date = '.$db->quote( $null ).' OR a.start_date <= '.$db->quote( $now ).' )'
					. ' AND ( a.end_date = '.$db->quote( $null ).' OR a.end_date >= '.$db->quote( $now ).' )' );
		} elseif ( $date_state == 'upcoming' ) {
			// TODO
		}

		$query->order( 'a.id ASC' );
		
		$db->setQuery( $query, 0, $limit );

		$list	=	$db->loadObjectList();

		if ( $grouped ) {
			$items	=	array();

			foreach ( $list as $item ) {
				if ( !isset( $items[$item->user_id] ) ) {
					$items[$item->user_id]	=	array();
				}

				$items[$item->user_id][]	=	$item;
			}

			return $items;
		} elseif ( $limit ) {
			$ids	=	array();

			foreach ( $list as $item ) {
				$ids[]	=	$item->id;
			}

			JCckDatabase::execute( 'UPDATE `#__cck_more_ecommerce_subscriptions` SET state = -2 WHERE id IN ('.implode( ',', $ids ).')' );
		}

		return $list;
	}

	// expire
	public static function expire( $limit = 0 )
	{
		$expired_items		=	self::_getItems( 0, 1, 'expired', $limit );
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

				$expired_triggers[]				=	array(
														'author'=>$item->user_id,
														'pk'=>(int)$item->id
													);
				$user_groups[$item->user_id]	=	array_merge( $user_groups[$item->user_id], explode( ',', $item->groups ) );
			}

			$user_ids			=	array_keys( $user_groups );
			$all_active_items	=	self::_getItems( $user_ids, 1, 'active' );

			foreach ( $user_groups as $user_id=>$groups ) {
				$groups	=	array_flip( $groups );

				// Unset from Groups
				if ( count( $groups ) ) {
					$valid_groups	=	array();

					if ( isset( $all_active_items[$user_id] ) ) {
						foreach ( $all_active_items[$user_id] as $item ) {
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
				foreach ( $expired_triggers as $trigger_config ) {
					JCckToolbox::process( 'onCckSubscriptionExpiration', $trigger_config );

					// Set as Expired
					JCckDatabase::execute( 'UPDATE #__cck_more_ecommerce_subscriptions SET state = 0 WHERE id = '.(int)$trigger_config['pk'] );
				}
			} else {
				foreach ( $expired_triggers as $trigger_config ) {
					// Set as Expired
					JCckDatabase::execute( 'UPDATE #__cck_more_ecommerce_subscriptions SET state = 0 WHERE id = '.(int)$trigger_config['pk'] );
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