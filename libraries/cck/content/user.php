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

// CCK_User
class CCK_User
{	
	// getIP
	public static function getIP()
	{
		$res	=	getenv( 'REMOTE_ADDR' ); // $_SERVER["REMOTE_ADDR"]
	
		return $res;
	}
	
	// getProfile
	public static function getProfile( $userid )
	{
		$res	=	JCckDatabase::loadResult( 'SELECT CONCAT(cc.introtext,cc.fulltext) FROM #__cck_core_users as s'
									.	' LEFT JOIN #__content as cc ON cc.id = s.contentid WHERE s.registration=1 AND s.userid='.$userid );
		
		return $res;
	}
	
	// getProfileId
	public static function getProfileId( $userid )
	{
		$res	=	JCckDatabase::loadResult( 'SELECT contentid FROM #__cck_core_users WHERE registration=1 AND userid='.$userid );
	
		return $res;
	}
	
	// getSession
	public static function getSession()
	{
		$session	=	JFactory::getSession();
		$res		=	$session->getId();
	
		return $res;
	}
	
	// getUser
	public static function &getUser( $userid = 0, $profile = true, $preferences = false )
	{
		if ( ! $userid ) {
			$user	=	JFactory::getUser();
		} else {
			$user	=	JFactory::getUser( $userid );
		}
		
		// Session
		if ( $user->id && $user->guest != 1 ) {
			$user->session_id	=	null;
			$user->where_clause	=	'user_id='.$user->id;
		} else {
			$user->session_id	=	JFactory::getSession()->getId();
			$user->where_clause	=	'session_id="'.$user->session_id.'"';
		}
		
		// IP
		$user->ip	=	getenv( 'REMOTE_ADDR' ); // $_SERVER["REMOTE_ADDR"]
		
		// Profile
		if ( $user->id && $profile ) {
			$text	=	'';	//CCK_User::getProfile( $user->id );
			
			if ( $profile ) {
				$regex	=	CCK_Content::getRegex();
				preg_match_all( $regex, $text, $matches );
				if ( count( $matches[1] ) ) {
					foreach ( $matches[1] as $key => $val ) {
						$user->$val	=	$matches[2][$key];
					}
				}
			}
		}
		
		// Preferences
		if ( $user->id && $preferences ) {
			$preferences	=	JCckDatabase::loadResult( 'SELECT a.options FROM #__cck_core_preferences AS a WHERE a.userid = '.(int)$user->id );
			if ( $preferences ) {
				$registry		=	new JRegistry;
				$registry->loadString( $preferences );
				$preferences	=	$registry->toArray();
				if ( count( $preferences ) ) {
					foreach ( $preferences as $k => $v ) {
						$k			=	'preferences_'.$k;
						$user->$k	=	$v;
					}
				}
			}
		}
		
		return $user;
	}
	
	// render
	public static function render( $userid )
	{
	}
	
	// setPreference
	public static function setPreference( $name, $value )
	{
		$user	=	JFactory::getUser();
		$db		=	JFactory::getDbo();
		
		$preferences	=	JCckDatabase::loadResult( 'SELECT a.options FROM #__cck_core_preferences AS a WHERE a.userid = '.(int)$user->id );
		
		if ( $preferences ) {
			$registry			=	new JRegistry;
			$registry->loadString( $preferences );
			$preferences		=	$registry->toArray();
			$preferences[$name]	=	$value;
			
			$json	=	'';
			foreach ( $preferences as $k => $v ) {
				$json	.=	'"'.$k.'"'.':'.'"'.$v.'"'.',';
			}
			if ( $json ) {
				$json	=	'{' . substr( $json, 0, -1 ) . '}';
			}
			JCckDatabase::execute( 'UPDATE #__cck_core_preferences AS a SET a.options = "'.$db->escape( $json ).'" WHERE a.userid = '.(int)$user->id );
		} else {
			$json	=	'{' . '"'.$name.'"'.':'.'"'.$value.'"' . '}';
			JCckDatabase::execute( 'INSERT INTO #__cck_core_preferences ( userid, options ) VALUES ( '.(int)$user->id.', "'.$db->escape( $json ).'" )' );
		}
	}
	
	// setPreferences
	public static function setPreferences( $name, $value )
	{
		$user	=	JFactory::getUser();
		$db		=	JFactory::getDbo();
		
		$preferences	=	JCckDatabase::loadResult( 'SELECT a.options FROM #__cck_core_preferences AS a WHERE a.userid = '.(int)$user->id );
		
		if ( $preferences ) {
			$registry			=	new JRegistry;
			$registry->loadString( $preferences );
			$preferences		=	$registry->toArray();
			$preferences[$name]	=	$value;
			
			$json	=	'';
			foreach ( $preferences as $k => $v ) {
				$json	.=	'"'.$k.'"'.':'.'"'.$v.'"'.',';
			}
			if ( $json ) {
				$json	=	'{' . substr( $json, 0, -1 ) . '}';
			}
			JCckDatabase::execute( 'UPDATE #__cck_core_preferences AS a SET a.options = "'.$db->escape( $json ).'" WHERE a.userid = '.(int)$user->id );
		} else {
			$json	=	'{' . '"'.$name.'"'.':'.'"'.$value.'"' . '}';
			JCckDatabase::execute( 'INSERT INTO #__cck_core_preferences ( userid, options ) VALUES ( '.(int)$user->id.', "'.$db->escape( $json ).'" )' );
		}
	}
	
	// setValue
	public static function setValue( $id, $fieldname, $value, $old_value = '' )
	{
		$profileId	=	CCK_User::getProfileId( $id );
		
		if ( ! CCK_Article::setValue( $profileId, $fieldname, $value, $old_value ) ) {
			return false;
		}
		
		return true;
	}
	
	// setValues
	public static function setValues( $id, $fieldnames, $values, $old_values = '' )
	{
		$profileId	=	CCK_User::getProfileId( $id );
		
		if ( ! ARTICLE_setValues( $profileId, $fieldnames, $values, $old_values ) ) {
			return false;
		}
		
		return true;
	}
}
?>