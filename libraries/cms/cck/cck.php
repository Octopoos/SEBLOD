<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cck.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Utilities\ArrayHelper;

// JCck
abstract class JCck
{
	public static $_me				=	'cck';
	public static $_config			=	null;
	public static $_user			=	null;
	
	protected static $_host			=	null;
	protected static $_site			=	null;
	protected static $_sites		=	array();
	protected static $_sites_info	=	array();
	
	public static function callFunc( $class, $method, &$args = null, $ref = false )
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
				
				$translate		=	(int)self::$_config->params->get( 'language_jtext', 1 );
				
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
	public static function is( $minimum = '3' )
	{
		static $current	=	null;

		if ( $current === null ) {
			if ( is_file( JPATH_ADMINISTRATOR.'/components/com_cck/_VERSION.php' ) ) {
				require_once JPATH_ADMINISTRATOR.'/components/com_cck/_VERSION.php';

				$version	=	new JCckVersion;
				
				if ( $version->DEV_STATUS == 'dev-4.0' ) {
					$current	=	'4.0.0';
				} else {
					$current	=	$version->RELEASE.'.'.$version->DEV_LEVEL;
				}
			} else {
				$current	=	'3.0.0';
			}
		}

		return version_compare( $current, $minimum, 'ge' );
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

	// v
	public static function v()
	{		
		return JCck::on( '4' ) ? '' : '_3x';
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Site

	// _setMultisite
	public static function _setMultisite()
	{
		if ( (int)self::getConfig_Param( 'multisite', 0 ) ) {
			$alias			=	'';
			$base			=	JUri::base( true );
			$context		=	'';
			$host			=	JUri::getInstance()->getHost();
			$host2			=	'';
			$path			=	JUri::getInstance()->getPath();
			$path_base		=	$path;

			if ( JCckDevHelper::isMultilingual( true ) ) {
				$lang_sef	=	JCckDevHelper::getLanguageCode();
				
				if ( $lang_sef != '' ) {
					$lang_sef	=	'/'.$lang_sef;
				}
			} else {
				$lang_sef		=	'';
			}

			if ( $path ) {
				$path		=	substr( $path, 1 );
				$path		=	substr( $path, 0, strpos( $path, '/' ) );
				$host2		=	$host.'/'.$path;

				/* TODO#SEBLOD4: not quite sure that $host2 is right... check final "/" y/n? */
			}
			
			$query	=	'SELECT id, title, name, context, aliases, guest, guest_only_viewlevel, usergroups, public_viewlevel, viewlevels, configuration, options, parent_id'
					.	' FROM #__cck_core_sites'
					.	' WHERE published = 1'
					.	' AND access IN ('.implode( ',', JFactory::getUser()->getAuthorisedViewLevels() ).')';

			self::$_sites	=	JCckDatabase::loadObjectList( $query, 'name' );
			
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
						$pos		=	strpos( $path_base.'/', $base.$lang_sef.'/'.$s->context.'/' );

						if ( $pos !== false && $pos == 0 ) {
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
				self::$_sites[$host]->environment	=	1;
				self::$_sites[$host]->host			=	( $alias ) ? $alias : self::$_sites[$host]->name;

				if ( self::$_sites[$host]->parent_id ) {
					$parent		=	self::getSiteById( self::$_sites[$host]->parent_id );
					$properties	=	array(
										'guest_only_viewlevel',
										'usergroups',
										'public_viewlevel',
										'viewlevels'
									);

					if ( self::$_sites[$host]->guest ) {
						if ( !( self::$_sites[$host]->name.'@$' == $parent->name ) ) {
							self::$_sites[$host]->environment	=	0;
						}
					} else {
						$properties[]	=	'guest';
					}

					/*
					guest: used to load the user in session > should not be overriden as the user (fake) already got the right group assigned
					guest_only_group: -
					guest_only_viewlevel: appended on client=administrator
					public_viewlevel: -
					*/

					// Keeping own public_viewlevel (for logged-in users)
					if ( self::$_sites[$host]->public_viewlevel ) {
						$json						=	json_decode( self::$_sites[$host]->configuration, true );
						$parent->public_viewlevel	.=	','.self::$_sites[$host]->public_viewlevel;

						if ( isset( $json['viewlevels2'] ) && is_array( $json['viewlevels2'] ) && count( $json['viewlevels2'] ) ) {
							$parent->public_viewlevel	.=	','.implode( ',', $json['viewlevels2'] );
						}

						$parent->public_viewlevel	=	explode( ',', $parent->public_viewlevel );
						$parent->public_viewlevel	=	ArrayHelper::toInteger( $parent->public_viewlevel );
					}

					foreach ( $properties as $property ) {
						self::$_sites[$host]->$property	=	$parent->$property;
					}
				} else {
					$json	=	json_decode( self::$_sites[$host]->configuration, true );

					if ( isset( $json['viewlevels2'] ) && is_array( $json['viewlevels2'] ) && count( $json['viewlevels2'] ) ) {
						self::$_sites[$host]->public_viewlevel	.=	','.implode( ',', $json['viewlevels2'] );
						self::$_sites[$host]->public_viewlevel	=	explode( ',', self::$_sites[$host]->public_viewlevel );
						self::$_sites[$host]->public_viewlevel	=	ArrayHelper::toInteger( self::$_sites[$host]->public_viewlevel );	
					}
				}

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
		if ( !isset( self::$_sites[self::$_host] ) ) {
			return (object)array(
							'context'=>'',
							'id'=>0,
							'name'=>'',
							'parent_id'=>0,
							'title'=>''
						   );
		}
		
		if ( is_object( self::$_sites[self::$_host] ) && is_string( self::$_sites[self::$_host]->configuration ) ) {
			self::$_sites[self::$_host]->configuration	=	new JRegistry( self::$_sites[self::$_host]->configuration );
		}

		return self::$_sites[self::$_host];
	}
	
	// getSiteById
	public static function getSiteById( $id )
	{
		static $sites	=	null;

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
	public static function isSite( $master = false, $status = '' )
	{
		if ( self::$_host != '' && isset( self::$_sites[self::$_host] ) ) {
			if ( $master && self::$_sites[self::$_host]->name != self::$_sites[self::$_host]->host ) {
				return false;
			}
			if ( $status ) {
				if ( $status == 'production' || $status == 'prod' ) {
					if ( !self::$_sites[self::$_host]->environment ) {
						return false;
					}
				} elseif ( self::$_sites[self::$_host]->environment ) {
					return false;
				}
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
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// _
	public static function _( $key )
	{
		static $loaded	=	array();
		if ( isset( $loaded[$key] ) ) {
			return;
		}
		
		$doc	=	JFactory::getDocument();
		if ( $key == 'cck.ecommerce' ) { /* TODO#SEBLOD: explode & dispatch */
			JHtml::_( 'behavior.core' );

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

		if ( JCck::on( '4.0' ) ) {
			JHtml::_( 'jquery.framework' );
		}
		if ( (int)JCck::getConfig_Param( 'core_js_bootstrap', '0' ) ) {
			$doc->addScript( $root.'/media/cck/js/bootstrap.min.js' );
		} else {
			JHtml::_( 'bootstrap.framework' );
		}
		
		if ( $dev !== false && !( isset( $app->cck_jquery_dev ) && $app->cck_jquery_dev === true ) ) {
			if ( $dev === true ) {
				$doc->addScript( $root.'/media/cck/js/cck.dev-3.22.0.min.js' );
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
			$doc->addScript( $root.'/media/cck/js/cck.core-3.18.2.min.js' );
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