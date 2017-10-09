<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cck.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCck
abstract class JCck
{
	public static $_me				=	'cck';
	public static $_config			=	NULL;
	public static $_user			=	NULL;
	
	protected static $_host			=	NULL;
	protected static $_site			=	NULL;
	protected static $_sites		=	array();
	protected static $_sites_info	=	array();
	
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
	
		// Tweak Language: JText
		if ( $name == 'language_jtext' ) {
			static $tweaked	=	0;
			
			if ( !$tweaked ) {
				$isConfigView	=	false;
				
				// Protect JFactory::getApplication for CLI
				try {
					$app			=	JFactory::getApplication();
					$isConfigView	=	( ( $app->input->get( 'option' ) == 'com_cck' && $app->input->get( 'view' ) == 'field' ) || ( $app->input->get( 'option' ) == 'com_config' ) );
				} catch ( Exception $e ) {
					// Do Nothing
				}
				
				$translate		=	(int)self::$_config->params->get( 'language_jtext', 0 );
				
				if ( $translate == 2 ) {
					if ( !$isConfigView ) {
						if ( JFactory::getLanguage()->getTag() == 'en-GB' ) {
							self::$_config->params->set( 'language_jtext', 0 );
						} else {
							self::$_config->params->set( 'language_jtext', 1 );
						}
					}
				}
				
				$tweaked++;
			}
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
			$context		=	'';
			$host			=	JUri::getInstance()->getHost();
			$path			=	JUri::getInstance()->getPath();
			$path_base		=	$path;
			
			$host2			=	'';
			if ( $path ) {
				$path		=	substr( $path, 1 );
				$path		=	substr( $path, 0, strpos( $path, '/' ) );
				$host2		=	$host.'/'.$path;
			}
			self::$_sites	=	JCckDatabase::loadObjectList( 'SELECT id, title, name, context, aliases, guest, guest_only_viewlevel, groups, public_viewlevel, viewlevels, configuration, options FROM #__cck_core_sites WHERE published = 1', 'name' );
			
			if ( count( self::$_sites ) ) {
				$break		=	0;
				$context	=	'';
				$hasContext	=	false;

				self::$_sites_info['guests']	=	array();

				foreach ( self::$_sites as $s ) {
					$s->exclusions	=	array();
					$json			=	json_decode( $s->configuration, true );

					if ( isset( $json['exclusions'] ) && $json['exclusions'] != '' ) {
						$s->exclusions	=	explode( '||', $json['exclusions'] );
					}

					if ( $s->context != '' ) {
						$hasContext	=	true;
						$pos		=	strpos( $path_base, '/'.$s->context );

						if ( $pos !== false & $pos == 0 ) {
							$context	=	$s->context;
						}
					}

					self::$_sites_info['guests'][$s->guest]	=	'';
				}
				self::$_sites_info['hasContext']	=	$hasContext;
				
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

			if ( $context != '' ) {
				$host	.=	'@'.$context;
			}
			self::$_host	=	$host;

			if ( isset( self::$_sites[$host] ) ) {
				self::$_sites[$host]->host	=	( $alias ) ? $alias : self::$_sites[$host]->name;
			}

			return true;
		} else {
			return false;
		}
	}
	
	// getMultisiteInfo
	public static function getMultisiteInfo( $property = '' )
	{
		if ( $property == '' ) {
			return self::$_sites_info;
		}

		if ( !isset( self::$_sites_info[$property] ) ) {
			return null;
		}

		return self::$_sites_info[$property];
	}

	// getSite
	public static function getSite()
	{
		return self::$_sites[self::$_host];
	}
	
	// getSiteById
	public static function getSiteById( $id )
	{
		static $sites	=	NULL;

		if ( !is_array( $sites ) ) {
			$sites		=	array();

			if ( count( self::$_sites ) ) {
				foreach ( self::$_sites as $k=>$v ) {
					$sites[$v->id]	=	$v;
				}
			}
		}
		if ( !isset( $sites[$id] ) ) {
			return null;
		}

		return $sites[$id];
	}

	// isSite
	public static function isSite( $master = false )
	{
		if ( self::$_host != '' && isset( self::$_sites[self::$_host] ) ) {
			if ( $master && self::$_sites[self::$_host]->name != self::$_sites[self::$_host]->host ) {
				return false;
			}

			return true;
		} else {
			return false;
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // User
	
	// _setUser
	protected static function _setUser( $userid = 0, $content_type = '', $profile = true )
	{
		self::$_user	=	JCckUser::getUser( $userid, '', true );
	}
	
	// getUser
	public static function getUser( $userid = 0, $content_type = '', $profile = true )
	{
		// Legacy Code, just in case..
		if ( is_bool( $content_type ) ) {
			return JCckLegacy::getUser( $userid, $content_type, $profile );
		}
		$update		=	false;

		if ( is_array( $userid ) ) {
			$update	=	(bool)$userid[1];
			$userid	=	(int)$userid[0];
		}
		if ( $userid ) {
			if ( $update ) {
				self::_setUser( $userid, $content_type, $profile );

				return self::$_user;
			} else {
				return JCckUser::getUser( $userid, '', true );
			}
		}
		
		if ( ! self::$_user ) {
			self::_setUser( $userid, $content_type, $profile );
		}
		
		return self::$_user;
	}
	
	// getUser_Value
	public static function getUser_Value( $name, $default = '' )
	{
		if ( ! self::$_user ) {
			self::_setUser();
		}
				
		return ( @self::$_user->$name != '' ) ? @self::$_user->$name : $default;
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
			$version	=	'2.25.0';
			if ( is_file( JPATH_ADMINISTRATOR.'/components/com_cck_ecommerce/_VERSION.php' ) ) {
				require_once JPATH_ADMINISTRATOR.'/components/com_cck_ecommerce/_VERSION.php';
				$version	=	new JCckEcommerceVersion;
				$version	=	$version->getApiVersion();
			}
			$doc->addScript( JUri::root( true ).'/media/cck_ecommerce/js/cck.ecommerce-'.$version.'.min.js' );
		}
		
		$loaded[$key]	=	true;
	}
	
	// loadjQuery + noConflit + jQueryMore + jQueryDev
	public static function loadjQuery( $noconflict = true, $more = true, $dev = false )
	{
		$app	=	JFactory::getApplication();
		$doc	=	JFactory::getDocument();
		$root	=	JUri::root( true );

		JHtml::_( 'bootstrap.framework' );
		
		if ( $dev !== false && !( isset( $app->cck_jquery_dev ) && $app->cck_jquery_dev === true ) ) {
			if ( $dev === true ) {
				$doc->addScript( $root.'/media/cck/js/cck.dev-3.7.0.min.js' );
				$doc->addScript( $root.'/media/cck/js/jquery.ui.effects.min.js' );
				$app->cck_jquery_dev	=	true;
			} elseif ( is_array( $dev ) && count( $dev ) ) {
				if ( $app->input->get( 'tmpl' ) == 'raw' ) {
					foreach ( $dev as $v ) {
						echo '<script src="'.$root.'/media/cck/js/'.$v.'" type="text/javascript"></script>';
					}
				} else {			
					foreach ( $dev as $v ) {
						$doc->addScript( $root.'/media/cck/js/'.$v );
					}
				}
				$app->cck_jquery_dev	=	true;
			}
		}
		if ( $more === true && !( isset( $app->cck_jquery_more ) && $app->cck_jquery_more === true ) && !( isset( $app->cck_jquery_dev ) && $app->cck_jquery_dev === true ) ) {
			$context	=	'';

			if ( JCck::isSite() && JCck::getSite()->context ) {
				$context	=	'/'.JCck::getSite()->context;
			}
			$doc->addScript( $root.'/media/cck/js/cck.core-3.15.1.min.js' );
			$doc->addScriptDeclaration( 'JCck.Core.baseURI = "'.JUri::base( true ).$context.'";' );
			$doc->addScriptDeclaration( 'JCck.Core.sourceURI = "'.substr( JUri::root(), 0, -1 ).'";' );
			
			$app->cck_jquery_more	=	true;
		}
	}
	
	// loadjQueryUI
	public static function loadjQueryUI()
	{
		$app	=	JFactory::getApplication();
		if ( !( isset( $app->cck_jquery_ui ) && $app->cck_jquery_ui === true ) ) {
			$doc	=	JFactory::getDocument();
			$doc->addScript( JUri::root( true ).'/media/cck/js/jquery.ui.min.js' );
			$app->cck_jquery_ui	=	true;
		}
	}
	
	// loadModalBox
	public static function loadModalBox()
	{
		$app	=	JFactory::getApplication();
		$root	=	JUri::root( true );

		if ( !( isset( $app->cck_modal_box ) && $app->cck_modal_box === true ) ) {
			$style	=	$app->isClient( 'administrator' ) ? 'css/' : 'styles/'.self::getConfig_Param( 'site_modal_box_css', 'style0' ).'/';
			$doc	=	JFactory::getDocument();
			$doc->addStyleSheet( $root.'/media/cck/scripts/jquery-colorbox/'.$style.'colorbox.css' );
			$doc->addScript( $root.'/media/cck/scripts/jquery-colorbox/js/jquery.colorbox-min.js' );
			$app->cck_modal_box	=	true;
		}
	}
}
?>