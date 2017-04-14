<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: user.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckUser
abstract class JCckUser
{
	// getIP
	public static function getIP()
	{
		return getenv( 'REMOTE_ADDR' ); // $_SERVER["REMOTE_ADDR"];
	}
	
	// getProfile
	public static function getProfile( $userid )
	{
		$res	=	JCckDatabase::loadResult( 'SELECT CONCAT(cc.introtext,cc.fulltext) FROM #__cck_core_users as s'
											. ' LEFT JOIN #__content as cc ON cc.id = s.contentid WHERE s.registration=1 AND s.userid='.$userid );
		
		return $res;
	}
	
	// getUser
	public static function &getUser( $userid = 0, $content_type = '', $profile = true )
	{
		if ( ! $userid ) {
			$user	=	JFactory::getUser();
		} else {
			$user	=	JFactory::getUser( $userid );
		}
		
		// Core
		if ( $user->id && $user->guest != 1 ) {
			$user->session_id	=	null;
			$user->where_clause	=	'user_id='.$user->id;
		} else {
			$user->session_id	=	JFactory::getSession()->getId();

			if ( empty( $user->session_id ) ) {
				$user->session_id	=	uniqid(); /* Not good, but better than empty */
			}
			$user->where_clause	=	'session_id="'.$user->session_id.'"';
		}
		
		// IP
		$user->ip	=	getenv( 'REMOTE_ADDR' ); // $_SERVER["REMOTE_ADDR"];
		
		// More
		if ( $user->id && $profile ) {
			if ( !$content_type ) {
				$content_type	=	''; // todo: config
				if ( !$content_type ) {
					$content_type	=	JCckDatabase::loadResult( 'SELECT cck FROM #__cck_core WHERE storage_location = "joomla_user" AND pk = '.(int)$user->id );
				}
			}

			$db		=	JFactory::getDbo();
			$prefix	=	$db->getPrefix();
			$tables	=	$db->getTableList();
			$tables	=	array_flip( $tables );
			
			if ( isset( $tables[$prefix.'cck_store_item_users'] ) ) {
				$fields	=	JCckDatabase::loadObject( 'SELECT * FROM #__cck_store_item_users WHERE id = '.(int)$user->id );
				if ( count( $fields ) ) {
					foreach ( $fields as $k=>$v ) {
						$user->$k	=	$v;
					}
				}
			}
			if ( isset( $tables[$prefix.'cck_store_form_'.$content_type] ) ) {
				$fields	=	JCckDatabase::loadObject( 'SELECT * FROM #__cck_store_form_'.$content_type.' WHERE id = '.(int)$user->id );
				if ( count( $fields ) ) {
					foreach ( $fields as $k=>$v ) {
						$user->$k	=	$v;
					}
				}
			}
		}
		
		return $user;
	}
}
?>