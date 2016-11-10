<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cck.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgSystemCCK extends JPlugin
{
	protected $content_objects	=	array();
	protected $multisite		=	null;
	protected $restapi			=	null;
	protected $site				=	null;
	protected $site_cfg			=	null;
	
	// __construct
	public function __construct( &$subject, $config )
	{
		parent::__construct( $subject, $config );

		$app	=	JFactory::getApplication();

		if ( $app->isAdmin() ) {
			JFactory::getLanguage()->load( 'lib_cck', JPATH_SITE );

			if ( file_exists( JPATH_SITE.'/plugins/cck_storage_location/joomla_user_note/joomla_user_note.php' ) ) {
				$this->content_objects['joomla_user_note']	=	1;
			}
		}
		jimport( 'joomla.filesystem.file' );
		
		// Content
		jimport( 'cck.content.article' );
		// jimport( 'cck.content.category' );
		jimport( 'cck.content.content' );
		jimport( 'cck.content.user' );
		
		$this->multisite	=	JCck::_setMultisite(); // todo: _isMultiSite()
		$this->restapi		=	$this->_isRestApi();

		JPluginHelper::importPlugin( 'cck_storage_location' );

		if ( $this->multisite === true ) {
			$this->site		=	null;
			$this->site_cfg	=	new JRegistry;
			
			if ( JCck::isSite() ) {
				$this->site	=	JCck::getSite();
				$this->site_cfg->loadString( $this->site->configuration );
				
				if ( $app->isSite() && $this->site ) {
					// --- Redirect to Homepage
					$homepage	=	$this->site_cfg->get( 'homepage', 0 );
					
					if ( $homepage > 0 ) {
						$current	=	JUri::current( true );
						$len		=	strlen( $current );
						
						if ( $current[$len - 1] == '/' ) {
							$current	=	substr( $current, 0, -1 );
						}
						$current	=	str_replace( array( 'http://', 'https://' ), '', $current );
						
						if ( $current == $this->site->host ) {
							$redirect_url	=	JRoute::_( 'index.php?Itemid='.$homepage );
							
							if ( $redirect_url != JUri::root(true).'/' ) {
								JFactory::getApplication()->redirect( $redirect_url );	
							}
						}
					}
					// ---
					$tag	=	$this->site_cfg->get( 'language' );
					
					if ( $tag ) {
						$forced	=	false;
						$path	=	JUri::getInstance()->getPath();
						$length	=	strlen( $path );

						if ( $path[$length - 1 ] != '/' ) {
							$path	.=	'/';
						}
						if ( $path[0] != '/' ) {
							$path	=	'/'.$path;
						}
						if ( isset( $this->site->exclusions ) && count( $this->site->exclusions ) ) {
							foreach ( $this->site->exclusions as $excl ) {
								$length	=	strlen( $excl );

								if ( $excl[$length - 1 ] != '/' ) {
									$excl	.=	'/';
								}
								if ( $excl[0] != '/' ) {
									$excl	=	'/'.$excl;
								}
								$pos	=	strpos( $path, $excl );

								if ( $pos !== false && $pos == 0 ) {
									$forced	=	true;
									break;
								}
							}
						}
						if ( $forced == true ) {
							$tag	=	JFactory::getLanguage()->getDefault();
						}
						$this->_setLanguage( $tag );
					}
				}
			}
		}
	}

	// buildRule
	public function buildRule( &$router, &$uri )
	{
		$Itemid	=	$uri->getVar( 'Itemid' );

		if ( $uri->getVar( 'option' ) == 'com_cck' && !$uri->getVar( 'task' ) && !$uri->getVar( 'view' ) ) {
			$item	=	JFactory::getApplication()->getMenu()->getItem( $Itemid );
			if ( isset( $item->query['view'] ) && ( $item->query['view'] == 'list' || $item->query['view'] == 'form' ) ) {
				$urlvars	=	$item->params->get( 'urlvars' );
				if ( $urlvars ) {
					$vars		=	explode( '&', $urlvars );
					if ( count( $vars ) ) {
						foreach ( $vars as $var ) {
							$v	=	explode( '=', $var );
							if ( $v[0] && $v[1] ) {
								$uri->setVar( $v[0], $v[1] );
							}
						}
					}
				}
			}
		}
	}

	// onAfterLoad
	public function onAfterLoad()
	{
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			JCckToolbox::process( 'onAfterLoad' );
		}
	}

	// onAfterInitialise
	public function onAfterInitialise()
	{
		$app	=	JFactory::getApplication();

		if ( $this->restapi ) {
			$format		=	JCckWebservice::getConfig_Param( 'resources_format', 'json' );
			$path		=	JUri::getInstance()->getPath();
			$segment	=	substr( $path, strrpos( $path, '/' ) + 1 );

			if ( $segment != '' ) {
				if ( ( $pos = strpos( $segment, '.' ) ) !== false ) {
					$format	=	substr( $segment, $pos + 1 );

					if ( $format[0] == 'w' ) {
						$format	=	substr( $format, 1 );
					}
				}
			}
			
			$app->input->set( 'format', $format );
		}

		if ( $app->isSite() ) {
			$router	=	JCck::on( '3.3' ) ? $app::getRouter() : $app->getRouter();
			$router->attachBuildRule( array( $this, 'buildRule' ) );
		} elseif ( $app->isAdmin() && $app->input->get( 'option' ) == 'com_config' && strpos( $app->input->get( 'component' ), 'com_cck' ) !== false ) {
			JFactory::getLanguage()->load( 'com_cck_core' );
		}
		
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) { // todo: move below
			JCckToolbox::process( 'onAfterInitialise' );
		}
		if ( $this->multisite !== true ) {
			return;
		}
		$user	=	JFactory::getUser();
		
		if ( $user->id > 0 && is_object( $this->site ) && $user->id != $this->site->guest ) {
			if ( $app->isSite() ) {
				$this->_setHomepage( $this->site_cfg->get( 'homepage', 0 ) );
				
				$style	=	$this->site_cfg->get( 'template_style', '' );
				
				if ( $style != '' ) {
					$this->site_cfg->set( 'set_template_style', true );
					$this->_setTemplateStyle( $style );
				}
			}
			
			if ( ! $this->site ) {
				JFactory::getSession()->set( 'user', JFactory::getUser( $user->id ) );
				return;
			}
			
			if ( !(int)JCck::getConfig_Param( 'multisite_login', '1' ) ) {
				if ( !$user->authorise( 'core.admin' ) ) {
					$groups		=	explode( ',', $this->site->groups );
					$hasGroups	=	0;

					if ( count( $groups ) ) {
						foreach ( $groups as $group_id ) {
							if ( isset( $user->groups[$group_id] ) ) {
								$hasGroups++;
							}
						}
						
						if ( !$hasGroups ) {
							$options	=	array( 'clientid'=>0 );
							$result		=	$app->logout( $user->id, $options );

							if ( !( $result instanceof Exception ) ) {
								$app->redirect( '/' );
							}
						}
					}
				}
			}
			
			// Groups
			$authgroups	=	$user->getAuthorisedGroups();
			$nogroups	=	JCckDatabase::loadColumn( 'SELECT groups FROM #__cck_core_sites WHERE id != '.$this->site->id );
			$nogroups	=	( is_null( $nogroups ) ) ? '' : ','.implode( ',', $nogroups ).',';
			$multisite	=	false;
			
			if ( count( $user->groups ) ) {
				foreach ( $user->groups as $g ) {
					if ( strpos( $nogroups, ','.$g.',' ) !== false ) {
						$multisite	=	true;
						break;
					}
				}
			}
			
			// Viewlevels
			$authlevels	=	$user->getAuthorisedViewLevels();
			$nolevels	=	JCckDatabase::loadColumn( 'SELECT viewlevels FROM #__cck_core_sites WHERE id != '.$this->site->id );
			$nolevels	=	( is_null( $nolevels ) ) ? array() : explode( ',', implode( ',', $nolevels ) );

			if ( $multisite ) {
				if ( count( $nolevels) ) {
					foreach ( $nolevels as $k=>$v ) {
						$nolevels[$k]	=	(int)$v;
					}
				}
				$viewlevels		=	array_diff( $authlevels, $nolevels );
				$otherlevels	=	array_diff( explode( ',', $this->site->viewlevels ), $viewlevels );
				$otherlevels	=	array_intersect( $otherlevels, $authlevels );

				if ( count( $otherlevels ) ) {
					$viewlevels	=	array_merge( $viewlevels, $otherlevels );
				}
			} else {
				$viewlevels		=	$authlevels;
			}
			
			if ( $app->isAdmin() && $this->site->guest_only_viewlevel > 0 ) {
				$viewlevels[]	=	$this->site->guest_only_viewlevel;
			}
			
			if( ( count( array_diff( $authlevels, $viewlevels ) ) ) || ( count( array_diff( $viewlevels, $authlevels ) ) ) ) {
				jimport( 'cck.joomla.user.user' );
				$userShadow		=	new CCKUser( $user->id );
				$userShadow->setAuthorisedViewLevels( $viewlevels );
				$userShadow->makeHimLive();
			}

			if ( JCck::on( '3.5' ) ) {
				jimport( 'cck.joomla.menu.menu' );
				$menuShadow		=	new CCKMenu( array( 'user_id'=>$user->id ) );
				$menuShadow->makeHimLive();
			}
		} else {
			if ( $app->isAdmin() ) {
				return;
			}
			$session	=	JFactory::getSession();
			$session->set( 'user', JFactory::getUser( 0 ) );
			
			if ( ! $this->site ) {
				return;
			}
			if ( strpos( JUri::getInstance()->toString(), 'task=registration.activate' ) !== false ) {
				return;
			}
			$user			=	new JUser( $this->site->guest );
			$user->guest	=	1;

			$session->set( 'user', $user );
			
			if ( JCck::on( '3.5' ) ) {
				jimport( 'cck.joomla.menu.menu' );
				$menuShadow		=	new CCKMenu( array( 'user_id'=>$this->site->guest ) );
				$menuShadow->makeHimLive();
			}
			$this->_setHomepage( $this->site_cfg->get( 'homepage', 0 ) );

			$style	=	$this->site_cfg->get( 'template_style', '' );

			if ( $style != '' ) {
				$this->site_cfg->set( 'set_template_style', true );
				$this->_setTemplateStyle( $style );
			}
		}
	}

	// onAfterDispatch
	public function onAfterDispatch()
	{
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			JCckToolbox::process( 'onAfterDispatch' );
		}
		$app		=	JFactory::getApplication();
		$doc		=	JFactory::getDocument();
		$id			=	$app->input->getInt( 'id', 0 );
		$option		=	$app->input->get( 'option', '' );
		$view		=	$app->input->get( 'view', '' );
		$layout		=	$app->input->get( 'layout', '' );
		
		if ( $app->isAdmin() ) {			
			switch ( $option ) {
				case 'com_config':
					$com	=	$app->input->get( 'component', '' );
					if ( $com == 'com_cck' || $com == 'com_cck_builder'
										   || $com == 'com_cck_developer'
										   || $com == 'com_cck_ecommerce'
										   || $com == 'com_cck_exporter'
										   || $com == 'com_cck_importer'
										   || $com == 'com_cck_manager'
										   || $com == 'com_cck_multilingual'
										   || $com == 'com_cck_packager'
										   || $com == 'com_cck_toolbox'
										   || $com == 'com_cck_updater'
										   || $com == 'com_cck_webservices' ) {
						JHtml::_( 'stylesheet', 'administrator/components/com_cck/assets/css/ui-big.css', array(), false );
						JCck::loadjQuery( true, true, true );
					}
					break;
				case 'com_installer':
					if ( $view == 'update' ) {
						if ( JCckDatabase::loadResult( 'SELECT extension_id FROM #__extensions WHERE type = "component" AND element = "com_cck_updater" AND enabled = 1' ) > 0 ) {
							$class	=	'btn btn-primary btn-small';
							$link	=	JRoute::_( 'index.php?option=com_cck_updater' );
							$target	=	'_self';
							$style	=	'top: -2px; position: relative;';
						} else {
							$class	=	'';
							$link	=	'https://www.seblod.com/store/extensions/634';
							$target	=	'_blank';
							$style	=	'text-decoration:underline;';
						}
						JFactory::getApplication()->enqueueMessage( JText::_( 'LIB_CCK_INSTALLER_UPDATE_WARNING_CORE' ), 'notice' );
						JFactory::getApplication()->enqueueMessage( JText::sprintf( 'LIB_CCK_INSTALLER_UPDATE_WARNING_MORE', $link, $target, $class, $style ), 'notice' );
					} elseif ( $view == 'manage' ) {
						$doc->addStyleDeclaration( 'span[data-original-title="SEBLOD (App Builder & CCK)"]{font-weight:bold;}');
					}
					break;
				case 'com_menus':
					if ( $layout || $view == 'item' ) {
						JHtml::_( 'stylesheet', 'administrator/components/com_cck/assets/css/ui-big.css', array(), false );
					}
					break;
				case 'com_modules':
					if ( $layout ) {
						JHtml::_( 'stylesheet', 'administrator/components/com_cck/assets/css/ui-big.css', array(), false );
					}
					break;
				case 'com_postinstall':
					$doc->addStyleDeclaration( 'fieldset{padding-bottom:18px;} fieldset > legend{margin-bottom:0;}' );
					break;
				case 'com_plugins':
					if ( $layout ) {
						JHtml::_( 'stylesheet', 'administrator/components/com_cck/assets/css/ui-big.css', array(), false );
					}
					break;
				default:
					$locations	=	JCckDatabase::loadObjectList( 'SELECT a.name, a.options FROM #__cck_core_objects AS a WHERE a.component = "'.JCckDatabase::escape( $option ).'"' );
					$uri		=	array( 'option'=>$option, 'view'=>$view, 'layout'=>$layout, 'id'=>$id );
					if ( count( $locations ) ) {
						foreach ( $locations as $location ) {
							$path	=	JPATH_SITE.'/plugins/cck_storage_location/'.$location->name.'/classes/integration.php';
							if ( is_file( $path ) ) {
								$data	=	array(
												'options'=>new JRegistry( $location->options )
											);
								require_once $path;
								JCck::callFunc_Array( 'plgCCK_Storage_Location'.$location->name.'_Integration', 'onCCK_Storage_LocationAfterDispatch', array( &$data, $uri ) );
							}
						}
					}
					break;
			}
		} elseif ( $app->isSite() ) {
			if ( !JCck::getConfig_Param( 'sef_canonical', 0 ) && !isset( $app->cck_canonical ) && isset( $doc->_links ) && count( $doc->_links ) ) {
				foreach ( $doc->_links as $k=>$link ) {
					if ( $link['relation'] == 'canonical' ) {
						unset( $doc->_links[$k] );
						break;
					}
				}
			}

			$itemId	=	$app->input->getInt( 'Itemid', 0 );
			$user	=	JFactory::getUser();
			
			if ( $this->multisite === true ) {
				$config		=	JFactory::getConfig();
				$site_title	=	$this->site_cfg->get( 'sitename', '' );
				$site_pages	=	$this->site_cfg->get( 'sitename_pagetitles', 0 );
				$site_desc	=	$this->site_cfg->get( 'metadesc', '' );
				$site_keys	=	$this->site_cfg->get( 'metakeys', '' );
				
				$meta_desc	=	$doc->getMetaData( 'description' );
				$meta_keys	=	$doc->getMetaData( 'keywords' );
				
				if ( $site_pages ) {
					$title	=	( $site_pages ) == 2 ? $doc->getTitle().' - '.$site_title : $site_title .' - '.$doc->getTitle();
					$doc->setTitle( $title );
				}
				if ( $site_desc && ( !$meta_desc || $meta_desc == $config->get( 'MetaDesc' ) ) ) {
					$doc->setMetaData( 'description', $site_desc );
				}
				if ( $site_keys && ( !$meta_keys || $meta_keys == $config->get( 'MetaKeys' ) ) ) {
					$doc->setMetaData( 'keywords', $site_keys );
				}
				if ( $this->site_cfg->get( 'offline' ) && !$user->authorise( 'core.login.offline' ) ) {
					$template	=	JCckDatabase::loadObject( 'SELECT template, params FROM #__template_styles WHERE template = "'.$app->getTemplate().'"' );
					$params		=	array( 'directory'=>JPATH_THEMES,
										   'file'=>'offline.php',
										   'params'=>$template->params,
										   'template'=>$template->template
									);
					if ( JCck::on( '3.6') ) {
						$params['params']	=	new JRegistry( $params['params'] );
					}
					$doc->parse( $params );
					$this->offline_buffer	=	$doc->render( false, $params );
				} elseif ( $this->site_cfg->get( 'set_template_style', false ) ) {
					$menu	=	$app->getMenu();
					
					if ( is_object( $menu ) ) {
						$active	=	$menu->getActive();
						
						if ( is_object( $active ) ) {
							$style	=	$active->template_style_id;
							
							if ( $style ) {
								$this->_setTemplateStyle( $style );
							}
						}
					}
				}
			}
			
			if ( $option == 'com_users' ) {
				$options	=	JCckDatabase::loadResult( 'SELECT a.options FROM #__cck_core_objects AS a WHERE a.name = "joomla_user"' );
				$options	=	new JRegistry( $options );
				$itemId		=	$app->input->getInt( 'Itemid', 0 );
				
				if ( $options->get( 'registration', 1 ) ) {
					if ( $view == 'profile' ) {
						$user	=	JFactory::getUser();

						if ( !$user->id ) {
							return;
						}
						if ( $layout == 'edit' ) {
							$type	=	JCckDatabase::loadResult( 'SELECT cck FROM #__cck_core WHERE storage_location="joomla_user" AND pk='.(int)$user->id );
							if ( !$type ) {
								return;
							}
							$itemId2	=	$options->get( 'profile_itemid', 0 );
							$return		=	$app->input->getBase64( 'return', '' );
							$return		=	$return ? '&return='.$return : '';

							if ( $itemId2 ) {
								$item		=	$app->getMenu()->getItem( $itemId2 );
								$urlvars	=	'';
								if ( is_object( $item ) ) {
									$urlvars	=	$item->params->get( 'urlvars' );

									if ( $urlvars != '' ) {
										$urlvars	=	'&'.$urlvars;
									}
								}
								$url		=	JRoute::_( 'index.php?option=com_cck&view=form&layout=edit&type='.$type.'&id='.$user->id.'&Itemid='.$itemId2.$urlvars.$return );
							} else {
								$url		=	'index.php?option=com_cck&view=form&layout=edit&type='.$type.'&id='.$user->id.'&Itemid='.$itemId.$return;
							}							
						} else {
							require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_user/joomla_user.php';
							$sef		=	0;
							$itemId2	=	$options->get( 'profile_itemid', 0 );
							if ( $itemId2 ) {
								$link	=	JCckDatabase::loadResult( 'SELECT link FROM #__menu WHERE id = '.(int)$itemId2 );
								if ( strpos( $link, 'index.php?option=com_cck&view=list' ) !== false ) {
									$search	=	str_replace( 'index.php?option=com_cck&view=list&search=', '', $link );
									$search	=	substr( $search, 0, strpos( $search, '&' ) );
									$search	=	JCckDatabase::loadResult( 'SELECT options FROM #__cck_core_searchs WHERE name = "'.$search.'"' );
									if ( $search != '' ) {
										$search	=	new JRegistry( $search );
										$sef	=	$search->get( 'sef', JCck::getConfig_Param( 'sef', '2' ) );
										if ( $sef ) {
											$itemId	=	$itemId2;
										}
									}
								}
							}
							$url	=	plgCCK_Storage_LocationJoomla_User::getRoute( $user->id, $sef, $itemId );
						}
						if ( $url != '' ) {
							$app->redirect( $url );
						}
					} elseif ( $view == 'registration' ) {
						$itemId2	=	(int)$options->get( 'registration_itemid', 0 );

						if ( $itemId2 ) {
							$url	=	JRoute::_( 'index.php?Itemid='.$itemId2 );
						} else {
							$type	=	$options->get( 'default_type', 'user' );
							
							if ( !$type ) {
								return;
							}
							$url	=	'index.php?option=com_cck&view=form&layout=edit&type='.$type.'&Itemid='.$itemId;
						}
						$app->redirect( $url );
					}
				}
			}
			
			if ( $option == 'com_content' && $view == 'form' && $layout == 'edit' ) {
				$aid	=	$app->input->getInt( 'a_id', 0 );
				$return	=	$app->input->getBase64( 'return' );
				if ( !$aid ) {
					return;
				}
				$bridgeType	=	JCckDatabase::loadObject( 'SELECT cck, pk FROM #__cck_core WHERE storage_location IN ("joomla_user","joomla_user_group") AND pkb='.(int)$aid );
				
				if ( is_object( $bridgeType ) && $bridgeType->cck ) {
					$type	=	$bridgeType->cck;
					$aid	=	(int)$bridgeType->pk;
				} else {
					$type	=	JCckDatabase::loadResult( 'SELECT cck FROM #__cck_core WHERE storage_location="joomla_article" AND pk='.(int)$aid );
					if ( !$type ) {
						return;
					}
				}
				$url	=	'index.php?option=com_cck&view=form&layout=edit&type='.$type.'&id='.$aid.'&Itemid='.$itemId.'&return='.$return;
				$app->redirect( $url );
			}
			
			$css_def	=	JCck::getConfig_Param( 'site_css_def', '' );
			$css		=	JCck::getConfig_Param( 'site_css', '' );
			$js			=	JCck::getConfig_Param( 'site_js', '' );
			if ( $css_def == 'custom' ) {
				$custom	=	JCck::getConfig_Param( 'site_css_def_custom', '' );
				if ( is_array( $custom ) && count( $custom ) ) {
					$css_def	=	implode( '-', $custom );
					$css_def	=	( $css_def == 'base-spacing-writing' ) ? 'all' : $css_def;
					$doc->addStyleSheet( JUri::root( true ).'/media/cck/css/definitions/'.$css_def.'.css' );
				}
			} elseif ( $css_def ) {
				$doc->addStyleSheet( JUri::root( true ).'/media/cck/css/definitions/'.$css_def.'.css' );
			}
			if ( $css != '' ) {
				$doc->addStyleDeclaration( $css );
			}
			if ( $js != '' ) {
				JCck::loadjQuery( true, false, false );
				$doc->addScriptDeclaration( 'jQuery(document).ready(function($){'.$js.'});' );
			}
		}
	}
	
	// onBeforeRender
	public function onBeforeRender()
	{
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			JCckToolbox::process( 'onBeforeRender' );
		}
		$app	=	JFactory::getApplication();
		$doc	=	JFactory::getDocument();
		
		if ( ( $app->isAdmin() || ( $app->isSite() && JCckToolbox::getConfig()->def( 'KO' ) ) ) && $doc->getType() == 'html' ) {
			$head	=	$doc->getHeadData();

			JCckToolbox::setHead( $head );
		}
		if ( $app->isSite() && isset( $app->cck_app['Header'] ) ) {
			if ( count( $app->cck_app['Header'] ) ) {
				foreach ( $app->cck_app['Header'] as $k=>$v ) {
					$app->setHeader( $k, $v, true );
				}
			}
		}
	}

	// onAfterRender
	public function onAfterRender()
	{
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			JCckToolbox::process( 'onAfterRender' );
		}
		$app		=	JFactory::getApplication();
		$doc		=	JFactory::getDocument();
		$option		=	$app->input->get( 'option', '' );
		$view		=	$app->input->get( 'view', '' );
		$layout		=	$app->input->get( 'layout', '' );
		
		if ( ( $app->isAdmin() || ( $app->isSite() && JCckToolbox::getConfig()->def( 'KO' ) ) ) && $doc->getType() == 'html' ) {
			JCckToolbox::setHeadAfterRender();
		}

		// site
		if ( $app->isSite() ) {
			if ( $this->multisite === true && $this->site_cfg->get( 'offline' ) && isset( $this->offline_buffer ) ) {
				$uri		=	JUri::getInstance();
				$app->setUserState( 'users.login.form.data', array( 'return'=>(string)$uri ) );

				if ( !isset( $app->cck_app['Header']['Status'] ) ) {
					$app->setHeader( 'Status', '503 Service Temporarily Unavailable', true );
				}
				$app->setBody( $this->offline_buffer );
			}
			return;
		}
		
		// admin
		if ( $app->isAdmin() ) {
			$buffer		=	'';
			$type		=	JFactory::getDocument()->getType();

			if ( $type == 'html' ) {
				$buffer	=	$app->getBody();
				$buffer	=	str_replace( 'icon-cck-', 'myicon-cck-', $buffer );
				
				switch ( $option ) {
					case 'com_cck':
					case 'com_cck_builder':
					case 'com_cck_developer':
					case 'com_cck_ecommerce':
					case 'com_cck_exporter':
					case 'com_cck_importer':
					case 'com_cck_manager':
					case 'com_cck_multilingual':
					case 'com_cck_packager':
					case 'com_cck_toolbox':
					case 'com_cck_updater':
					case 'com_cck_webservices':
						$buffer	=	$this->_setBasics( $buffer, $option, $view );
						break;
					case 'com_postinstall':
						$eid	=	$app->input->get( 'eid', 0 );
						$eid2	=	JCckDatabase::loadResult( 'SELECT extension_id FROM #__extensions WHERE type = "component" AND element = "com_cck"' );
						if ( $eid && $eid == $eid2 ) {
							$buffer	=	str_replace( 'com_cck', 'SEBLOD', $buffer );
							$buffer	=	str_replace( 'option=SEBLOD', 'option=com_cck', $buffer );	
						}
						break;
					case 'com_templates':
						if ( $view == 'templates' || $layout == 'edit' ) {
							break;
						}
						$search		=	'#administrator/index.php\?option=com_templates&amp;task=style.edit&amp;id=(.*)">(.*)</a>#sU';
						$list		=	JCckDatabase::loadObjectList( 'SELECT a.id, b.name FROM #__template_styles AS a LEFT JOIN #__cck_core_templates AS b ON b.name = a.template WHERE b.name != ""', 'id' );
						preg_match_all( $search, $buffer, $matches );
						if ( count( $matches[1] ) ) {
							$i		=	0;
							$style	=	'height:14px; color:#ffffff; background-color:#3b99fc; margin:0px 12px 0px 12px; padding:2px 8px 2px 8px; font-size:10px; font-weight:bold;';
							foreach ( $matches[1] as $match ) {
								if ( isset( $list[$match] ) ) {
									$replace	=	$matches[0][$i] . '<span style="'.$style.'">SEBLOD</span>Do NOT set as Default Template !';
									$buffer		=	str_replace( $matches[0][$i], $replace, $buffer );
								}
								$i++;
							}
						}
						break;
					default:
						$and		=	( $view ) ? ' AND ( a.view = "'.$view.'" OR a.view = "" )' : '';
						$locations	=	JCckDatabase::loadObjectList( 'SELECT a.name, a.options FROM #__cck_core_objects AS a WHERE a.component = "'.$option.'"'.$and );
						$uri		=	array( 'option'=>$option, 'view'=>$view, 'layout'=>$layout );
						if ( count( $locations ) ) {
							foreach ( $locations as $location ) {
								$path	=	JPATH_SITE.'/plugins/cck_storage_location/'.$location->name.'/classes/integration.php';
								if ( is_file( $path ) ) {
									$data	=	array( 'doIntegration'=>false,
													   'multilanguage'=>0,
													   'options'=>new JRegistry( $location->options ),
													   'replace_end'=>'"',
													   'return_option'=>substr( $option, 4 ),
													   'return_view'=>$view,
													   'return'=>'',
													   'search'=>'',
													   'search_alt'=>''
												);
									require_once $path;
									JCck::callFunc_Array( 'plgCCK_Storage_Location'.$location->name.'_Integration', 'onCCK_Storage_LocationAfterRender', array( &$buffer, &$data, $uri ) );
									if ( $data['doIntegration'] ) {
										$list	=	JCckDatabase::loadObjectList( 'SELECT pk, cck FROM #__cck_core WHERE storage_location="'.$location->name.'"', 'pk' );
										$buffer	=	JCckDevIntegration::rewriteBuffer( $buffer, $data, $list );
									}
								}
							}
						}
						break;
				}
				$app->setBody( $buffer );
			} elseif ( $option == 'com_cck' && $type == 'raw' ) {
				if ( $layout == 'edit3' || $layout == 'edit4' ) {
					$buffer	=	$app->getBody();
					$buffer	=	str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $buffer );
					
					if ( $buffer != '' ) {
						$app->setBody( $buffer );
					}
				}
			}
			
			return;
		}
	}
	
	// onContentPrepareForm
	public function onContentPrepareForm( $form, $data )
	{
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$event		=	'onContentPrepareForm';
			$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );

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

	// onExtensionAfterSave
	public function onExtensionAfterSave( $context, $table, $flag )
	{
		if ( $context != 'com_config.component' ) {
			return;
		}

		if ( !( is_object( $table ) && $table->type == 'component' && $table->element == 'com_cck_updater' ) ) {
			return;
		}

		$params	=	new JRegistry;
		$params->loadString( $table->params );

		if ( $proxy = (int)$params->get( 'proxy', '0' ) ) {
			require_once JPATH_ADMINISTRATOR.'/components/com_cck_updater/helpers/helper_admin.php';

			$proxy	=	Helper_Admin::getProxy( $params, 'proxy_segment' );
						
			JCckDatabase::execute( 'UPDATE #__update_sites SET location = REPLACE(location, "update.seblod.com", "'.$proxy.'") WHERE location LIKE "%update.seblod.com%" AND location != "http://update.seblod.com/pkg_cck.xml"' );
		} elseif ( !$proxy && $params->get( 'proxy_domain' ) ) {
			require_once JPATH_ADMINISTRATOR.'/components/com_cck_updater/helpers/helper_admin.php';

			$proxy	=	Helper_Admin::getProxy( $params, 'proxy_segment' );

			JCckDatabase::execute( 'UPDATE #__update_sites SET location = REPLACE(location, "'.$proxy.'", "update.seblod.com") WHERE location LIKE "%'.$proxy.'%"' );
		}
	}

	// _isRestApi
	protected function _isRestApi()
	{
		if ( JCckWebservice::getConfig()->params->def( 'KO' ) ) {
		 	return false;
		} else {
			$apis	=	JCckDatabase::loadObjectList( 'SELECT path'
													. ' FROM #__menu WHERE'
													. ' link = "index.php?option=com_cck_webservices&view=api" AND published = 1',
													'path' );
			$path	=	JUri::getInstance()->getPath();
			$prefix	=	( !JFactory::getConfig()->get( 'sef_rewrite' ) ) ? '/index.php' : '';

			if ( count( $apis ) ) {
				foreach ( $apis as $api ) {
					$api	=	$prefix.'/'.$api->path;
					$pos	=	strpos( $path, $api );

					if ( $pos !== false && $pos == 0 ) {
						return true;
					}
				}
			}
		}

		return false;	
	}

	// _reSubmenu
	protected function _reSubmenu( $buffer, $search, $replace )
	{
		preg_match( $search, $buffer, $match );
		if ( is_array( $match ) && count( $match ) ) {
			$search		=	$match[1];
			$search		=	str_replace( '</ul>', '', $match[1] );
		
			$replace	=	$match[1].'<li style="float: right;">'.$replace.'</li>';
			$buffer		=	str_replace( $search, $replace.'</ul>', $buffer );
		}
		
		return $buffer;
	}
	
	// _setBasics
	protected function _setBasics( $buffer, $option, $view )
	{
		if ( ! $view ) {
			if ( $option == 'com_cck_ecommerce' ) {
				$buffer	=	str_replace( 'icon-48-seblod', 'icon-48-seblod-ecommerce', $buffer );
			}
		}
		
		return $buffer;
	}
	
	// _setHomepage
	protected function _setHomepage( $id )
	{
		if ( !$id ) {
			return;
		}
		$app	=	JFactory::getApplication();
		$menu	=	JMenu::getInstance( 'site' );
		$home	=	$menu->getDefault();
		$my		=	$menu->getItem( $id );
		$path	=	substr( JUri::getInstance()->getPath(), 1 );
		
		// todo: need to be improved!
		if ( !( !$path || $path == 'index.php/'.@$my->alias || $path == @$my->alias.'.html' ) ) {
			$home->title		=	@$my->title;
		} else {
			$home->id			=	@$my->id;
			$home->title		=	@$my->title;
			$home->type			=	@$my->type;
			$home->access		=	@$my->access;
			$home->component	=	@$my->component;
			$home->component_id	=	@$my->component_id;
			$home->link			=	@$my->link;
			$home->params		=	@$my->params;
			$home->query  		=	@$my->query;
		}
	}
	
	// _setLanguage
	protected function _setLanguage( $tag )
	{
		$app	=	JFactory::getApplication();
		$lang	=	JLanguage::getInstance( $tag );
		
		$app->loadLanguage( $lang );
		JFactory::$language	=	$app->getLanguage();

		JFactory::getConfig()->set( 'language', $tag );
		JFactory::getLanguage()->setLanguage( $tag );
	}

	// _setTemplateStyle
	protected function _setTemplateStyle( $style )
	{
		$style	=	JCckDatabase::loadObject( 'SELECT template, params FROM #__template_styles WHERE id = '.(int)$style );
		if ( is_object( $style ) ) {
			JFactory::getApplication()->setTemplate( $style->template, $style->params );
		}
	}
}
?>