<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cck.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCck
abstract class JCck
{
	public static $_me			=	'cck';
	public static $_config		=	NULL;
	public static $_user		=	NULL;
	
	protected static $_host		=	NULL;
	protected static $_site		=	NULL;
	protected static $_sites	=	array();
	
	public static function callFunc( $class, $method, &$args = NULL, $ref = false )
	{
		return $class::$method( $args );
	}
	
	public static function callFunc_Array( $class, $method, $args )
	{
		return call_user_func_array( $class.'::'.$method, $args );
		/*
		switch ( count( $args ) ) {
			case 1: return $class::$method( $args[0] ); break;
			case 2: return $class::$method( $args[0], $args[1] ); break;
			case 3: return $class::$method( $args[0], $args[1], $args[2] ); break;
			case 4: return $class::$method( $args[0], $args[1], $args[2], $args[3] ); break;
			case 5: return $class::$method( $args[0], $args[1], $args[2], $args[3], $args[4] ); break;
			default: return call_user_func_array( $class.'::'.$method, $args ); break;
		}
		*/
	}
	
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
	
	// is
	public static function is()
	{
	}
	
	// on
	public static function on( $minimum = '3' )
	{
		$versions	=	array();
		
		if ( !isset( $versions[$minimum] ) ) {
			$versions[$minimum]	=	version_compare( JVERSION, $minimum, 'ge' );
		}
		
		return $versions[$minimum];
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Site

	// _setMultisite
	public static function _setMultisite()
	{
		if ( (int)self::getConfig_Param( 'multisite', 0 ) ) {
			$alias			=	'';
			$host			=	JURI::getInstance()->getHost();
			$path			=	JURI::getInstance()->getPath();
			$host2			=	'';
			if ( $path ) {
				$path	=	substr( $path, 1 );
				$path	=	substr( $path, 0, strpos( $path, '/' ) );
				$host2		=	$host.'/'.$path;
			}
			self::$_sites	=	JCckDatabase::loadObjectList( 'SELECT id, title, name, aliases, guest, guest_only_viewlevel, groups, viewlevels, configuration, options FROM #__cck_core_sites WHERE published = 1', 'name' );
			if ( count( self::$_sites ) ) {
				$break		=	0;
				foreach ( self::$_sites as $s ) {
					if ( $s->aliases != '' ) {
						$aliases	=	explode( '||', $s->aliases );
						if ( count( $aliases ) ) {
							foreach ( $aliases as $a ) {
								if ( strpos( $a, '/' ) !== false ) {
									if ( $a == $host2 ) {
										$alias	=	$a;
										$host	=	$s->name;
										$break	=	1;
										break;
									}
								} else {
									if ( $a == $host ) {
										$alias	=	$a;
										$host	=	$s->name;
										$break	=	1;
										break;
									}	
								}
							}
						}
						if ( $break ) {
							break;
						}
					}
				}
			}
			self::$_host				=	$host;
			if ( isset( self::$_sites[$host] ) ) {
				self::$_sites[$host]->host	=	( $alias ) ? $alias : self::$_sites[$host]->name;
			}

			return true;
		} else {
			return false;
		}
	}
	
	// getSite
	public static function getSite()
	{
		return self::$_sites[self::$_host];
	}
	
	// isSite
	public static function isSite()
	{
		return ( self::$_host != '' && isset( self::$_sites[self::$_host] ) ) ? true : false;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // User
	// todo: REFACT ALL USER STUFF !! //
	
	// _setUser
	public static function _setUser( $userid = 0, $content_type = '', $profile = true )
	{		
		if ( self::$_user ) {
			return self::$_user;
		}

		// jimport( 'cck.content.user' );
		// self::$_user	=	CCK_User::getUser( $userid, $profile, $preferences );
		self::$_user	=	JCckUser::getUser( $userid, '', true );
	}
	
	// getUser
	/*
	public static function getUser( $userid = 0, $profile = true, $preferences = false )
	{
		Use JCckLegacy::getUser().
		Note: preferences are removed since SEBLOD 3.2.0
	}
	*/
	public static function getUser( $userid = 0, $content_type = '', $profile = true )
	{
		// Legacy Code, just in case..
		if ( is_bool( $content_type ) ) {
			return JCckLegacy::getUser( $userid, $content_type, $profile );
		}
		
		if ( $userid ) {
			// jimport( 'cck.content.user' );
			// return CCK_User::getUser( $userid, $profile, $preferences );
			return JCckUser::getUser( $userid, '', true );
		}
		
		if ( ! self::$_user ) {
			self::_setUser( $userid, $content_type, $profile );
		}
		
		return self::$_user;
	}
	
	// getUser
	public static function getUser_Value( $name, $default = '' )
	{
		if ( ! self::$_user ) {
			self::_setUser();
		}
				
		return ( @self::$_user->$name != '' ) ? @self::$_user->$name : $default;
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
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// _
	public static function _( $key )
	{
		static $loaded	=	array();
		if ( isset( $loaded[$key] ) ) {
			return;
		}
		
		$doc	=	JFactory::getDocument();
		if ( $key == 'cck.ecommerce' ) { // todo: explode & dispatch
			$version	=	'1.0.0';
			if ( is_file( JPATH_ADMINISTRATOR.'/components/com_cck_ecommerce/_VERSION.php' ) ) {
				require_once JPATH_ADMINISTRATOR.'/components/com_cck_ecommerce/_VERSION.php';
				$version	=	new JCckEcommerceVersion;
			}
			$doc->addScript( JURI::root( true ).'/media/cck_ecommerce/js/cck.ecommerce-'.$version->getApiVersion().'.js' );
		}
		
		$loaded[$key]	=	true;
	}
	
	// loadjQuery + noConflit + jQueryMore + jQueryDev
	public static function loadjQuery( $noconflict = true, $more = true, $dev = false )
	{
		$app	=	JFactory::getApplication();
		$doc	=	JFactory::getDocument();
		
		if ( self::on() ) {
			JHtml::_( 'bootstrap.framework' );
		} else {
			if ( !( isset( $app->cck_jquery ) && $app->cck_jquery === true ) ) {
				$doc->addScript( JURI::root( true ).'/media/cck/scripts/jquery/js/jquery-1.8.3.min.js' );
				$app->cck_jquery	=	true;
			}
			if ( $noconflict === true && !( isset( $app->cck_jquery_noconflict ) && $app->cck_jquery_noconflict === true ) ) {
				$doc->addScript( JURI::root( true ).'/media/cck/scripts/jquery/js/jquery-noconflict.js' );
				$app->cck_jquery_noconflict	=	true;
			}
		}
		if ( $dev !== false && !( isset( $app->cck_jquery_dev ) && $app->cck_jquery_dev === true ) ) {
			if ( $dev === true ) {
				$doc->addScript( JURI::root( true ).'/media/cck/js/cck.dev-3.6.0.min.js' );
				$doc->addScript( JURI::root( true ).'/media/cck/js/jquery.ui.effects.min.js' );
				$app->cck_jquery_dev	=	true;
			} elseif ( is_array( $dev ) && count( $dev ) ) {
				if ( $app->input->get( 'tmpl' ) == 'raw' ) {
					foreach ( $dev as $v ) {
						echo '<script src="'.JURI::root( true ).'/media/cck/js/'.$v.'" type="text/javascript"></script>';
					}
				} else {			
					foreach ( $dev as $v ) {
						$doc->addScript( JURI::root( true ).'/media/cck/js/'.$v );
					}
				}
				$app->cck_jquery_dev	=	true;
			}
		}
		if ( $more === true && !( isset( $app->cck_jquery_more ) && $app->cck_jquery_more === true ) && !( isset( $app->cck_jquery_dev ) && $app->cck_jquery_dev === true ) ) {
			$doc->addScript( JURI::root( true ).'/media/cck/js/cck.core-3.6.0.min.js' );
			$doc->addScriptDeclaration( 'JCck.Core.baseURI = "'.JUri::base( true ).'";' );
			$app->cck_jquery_more	=	true;
		}
	}
	
	// loadjQueryUI
	public static function loadjQueryUI()
	{
		$app	=	JFactory::getApplication();
		if ( !( isset( $app->cck_jquery_ui ) && $app->cck_jquery_ui === true ) ) {
			$doc	=	JFactory::getDocument();
			$doc->addScript( JURI::root( true ).'/media/cck/js/jquery.ui.min.js' );
			$app->cck_jquery_ui	=	true;
		}
	}
	
	// loadModalBox
	public static function loadModalBox()
	{
		$app	=	JFactory::getApplication();
		if ( !( isset( $app->cck_modal_box ) && $app->cck_modal_box === true ) ) {
			$style	=	$app->isAdmin() ? 'css/' : 'styles/'.self::getConfig_Param( 'site_modal_box_css', 'style0' ).'/';
			$doc	=	JFactory::getDocument();
			$doc->addStyleSheet( JURI::root( true ).'/media/cck/scripts/jquery-colorbox/'.$style.'colorbox.css' );
			$doc->addScript( JURI::root( true ).'/media/cck/scripts/jquery-colorbox/js/jquery.colorbox-min.js' );
			$app->cck_modal_box	=	true;
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