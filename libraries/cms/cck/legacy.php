<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: legacy.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCck
abstract class JCckLegacy extends JCck
{
	// _setUser
	public static function _setUser( $userid = 0, $profile = true, $preferences = false )
	{
		if ( self::$_user ) {
			return self::$_user;
		}
		
		jimport( 'cck.content.user' );
		self::$_user	=	CCK_User::getUser( $userid, $profile, $preferences );
	}
	
	// getUser
	public static function getUser( $userid = 0, $profile = true, $preferences = false )
	{
		if ( $userid ) {
			jimport( 'cck.content.user' );
			return CCK_User::getUser( $userid, $profile, $preferences );
		}
		
		if ( ! self::$_user ) {
			self::_setUser( $userid, $profile, $preferences );
		}
		
		return self::$_user;
	}
	// setUser_Preference
	public static function setUser_Preference( $name, $value )
	{
		if ( ! self::$_user ) {
			self::_setUser();
		}
		
		$name	=	'preferences_'.$name;
		return self::$_user->$name	=	$value;
	}
	
	// setUser_Preferences
	public static function setUser_Preferences( $preferences )
	{
		if ( !$preferences ) {
			return;
		}
		
		$registry		=	new JRegistry;
		$registry->loadString( $preferences );				
		$preferences	=	$registry->toArray();
		if ( count( $preferences ) ) {
			foreach ( $preferences as $k => $v ) {
				$k					=	'preferences_'.$k;
				self::$_user->$k	=	$v;
			}
		}
	}
	
	// googleAnalytics
	public static function googleAnalytics( $url, $account )
	{
		$doc	=	JFactory::getDocument();
		$js	=	"
				var _gaq = _gaq || [];
				_gaq.push(['_setAccount', '".$account."']);
				_gaq.push(['_setDomainName', 'none']);
				_gaq.push(['_setAllowLinker', true]);
				_gaq.push(['_trackPageview', '".$url."']);
				
				(function() {
					var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
					ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
				})();
				";
				
		$doc->addScriptDeclaration( $js );
	}
}
?>