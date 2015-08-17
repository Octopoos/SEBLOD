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

// Plugin
class plgSystemCCK extends JPlugin
{
	protected $content_objects	=	array();
	
	// plgSystemCCK
	function plgSystemCCK( &$subject, $config )
	{
		$app	=	JFactory::getApplication();
		if ( $app->isAdmin() ) {
			JFactory::getLanguage()->load( 'lib_cck', JPATH_SITE );
			if ( file_exists( JPATH_SITE.'/plugins/cck_storage_location/joomla_user_note/joomla_user_note.php' ) ) {
				$this->content_objects['joomla_user_note']	=	1;
			}
		}
		jimport( 'joomla.filesystem.file' );
		jimport( 'cck.base.cck' ); // deprecated
		
		// Content
		jimport( 'cck.content.article' );
		//jimport( 'cck.content.category' );
		jimport( 'cck.content.content' );
		jimport( 'cck.content.user' );
		
		// Development
		jimport( 'cck.development.database' ); // deprecated
		
		$this->multisite	=	JCck::_setMultisite();
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
						JFactory::getConfig()->set( 'language', $tag );
						JFactory::getLanguage()->setLanguage( $tag );
					}
				}
			}
		}
		parent::__construct( $subject, $config );
	}
	
	// buildRule
	public function buildRule( &$router, &$uri )
	{
		$Itemid	=	$uri->getVar( 'Itemid' );
		if ( $uri->getVar( 'option' ) == 'com_cck' && !$uri->getVar( 'task' ) && !$uri->getVar( 'view' ) ) {
			$item	=	JFactory::getApplication()->getMenu()->getItem( $Itemid );
			if ( isset( $item->query['view'] ) && $item->query['view'] == 'list' ) {
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
		
		if ( $user->get( 'id' ) > 0 && @$user->guest != 1 ) {
			if ( $app->isSite() ) {
				$this->_setHomepage( $this->site_cfg->get( 'homepage', 0 ) );
				$style	=	$this->site_cfg->get( 'template_style', '' );
				if ( $style != '' ) {
					$this->site_cfg->set( 'set_template_style', true );
					$this->_setTemplateStyle( $style );
				}
			}
			
			if ( isset( $user->cck_multisite ) ) {
				return;
			}
			
			if ( ! $this->site ) {
				JFactory::getSession()->set( 'user', JFactory::getUser( $user->get( 'id' ) ) );
				return;
			}
			
			// Groups
			$authgroups	=	$user->getAuthorisedGroups();
			$nogroups	=	JCckDatabase::loadColumn( 'SELECT groups FROM #__cck_core_sites WHERE id != '.$this->site->id );
			$nogroups	=	( is_null( $nogroups ) ) ? '' : ','.implode( ',', $nogroups ).',';
			
			$multisite	=	false;
			foreach ( $user->groups as $g ) {
				if ( strpos( $nogroups, ','.$g.',' ) !== false ) {
					$multisite	=	true;
					break;
				}
			}
			
			// Viewlevels
			$authlevels	=	$user->getAuthorisedViewLevels();
			$nolevels	=	JCckDatabase::loadColumn( 'SELECT viewlevels FROM #__cck_core_sites WHERE id != '.$this->site->id );
			$nolevels	=	( is_null( $nolevels ) ) ? array() : explode( ',', implode( ',', $nolevels ) );

			if ( $multisite ) {
				$viewlevels	=	array_diff( $authlevels, $nolevels );
			} else {
				$viewlevels	=	$authlevels;
			}
			
			if ( $app->isAdmin() && $this->site->guest_only_viewlevel > 0 ) {
				$viewlevels[]	=	$this->site->guest_only_viewlevel;
			}
			
			if( ( count( array_diff( $authlevels, $viewlevels ) ) ) || ( count( array_diff( $viewlevels, $authlevels ) ) ) ) {
				jimport( 'cck.joomla.user.user' );
				$shadow	=	new CCKUser( $user->get( 'id' ) );
				$shadow->setAuthorisedViewLevels( $viewlevels );
				$shadow->makeHimLive();
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
			
			$user			=	new JUser( $this->site->guest );
			$user->guest	=	1;
			$session->set( 'user', $user );
			
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
				case 'com_cck':
					if ( !JCck::on() && $view == 'sites' ) {
						JCckDevIntegration::addDropdown( $view );
					}
					break;
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
							$link	=	JRoute::_( 'index.php?option=com_cck_updater' );
							$target	=	'_self';
						} else {
							$link	=	'http://www.seblod.com/products/634';
							$target	=	'_blank';
						}
						JFactory::getApplication()->enqueueMessage( JText::_( 'LIB_CCK_INSTALLER_UPDATE_WARNING_CORE' ), 'notice' );
						JFactory::getApplication()->enqueueMessage( JText::sprintf( 'LIB_CCK_INSTALLER_UPDATE_WARNING_MORE', $link, $target ), 'notice' );
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
					$locations	=	JCckDatabase::loadObjectList( 'SELECT a.name, a.options FROM #__cck_core_objects AS a WHERE a.component = "'.$option.'"' );
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
			if ( !JCck::getConfig_Param( 'sef_canonical', 0 ) && !isset( $app->cck_canonical ) && JCck::on() && isset( $doc->_links ) && count( $doc->_links ) ) {
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
					$doc->parse( $params );
					$this->offline_buffer	=	$doc->render( false, $params );
				} elseif ( $this->site_cfg->get( 'set_template_style', false ) ) {
					$menu	=	$app->getMenu();
					if ( is_object( $menu ) ) {
						$style	=	$menu->getActive()->template_style_id;
						if ( $style ) {
							$this->_setTemplateStyle( $style );
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
						$userid	=	$user->get( 'id' );
						if ( !$userid ) {
							return;
						}
						if ( $layout == 'edit' ) {
							$type	=	JCckDatabase::loadResult( 'SELECT cck FROM #__cck_core WHERE storage_location="joomla_user" AND pk='.(int)$userid );
							if ( !$type ) {
								return;
							}
							$url	=	'index.php?option=com_cck&view=form&layout=edit&type='.$type.'&id='.$userid.'&Itemid='.$itemId;
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
							$url	=	plgCCK_Storage_LocationJoomla_User::getRoute( $userid, $sef, $itemId );
						}
						if ( $url != '' ) {
							$app->redirect( $url );
						}
					} elseif ( $view == 'registration' ) {
						$type	=	$options->get( 'default_type', 'user' );
						if ( !$type ) {
							return;
						}
						$url	=	'index.php?option=com_cck&view=form&layout=edit&type='.$type.'&Itemid='.$itemId;
						$app->redirect( $url );
					}
				}
			}
			
			if ( $option == 'com_content' && $view == 'form' && $layout == 'edit' ) {
				$itemId	=	$app->input->getInt( 'Itemid', 0 );
				$aid	=	$app->input->getInt( 'a_id', 0 );
				$return	=	$app->input->getBase64( 'return' );
				if ( !$aid ) {
					return;
				}
				$type	=	JCckDatabase::loadResult( 'SELECT cck FROM #__cck_core WHERE storage_location="joomla_article" AND pk='.(int)$aid );
				if ( !$type ) {
					return;
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
					$doc->addStyleSheet( JURI::root( true ).'/media/cck/css/definitions/'.$css_def.'.css' );
				}
			} elseif ( $css_def ) {
				$doc->addStyleSheet( JURI::root( true ).'/media/cck/css/definitions/'.$css_def.'.css' );
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
		} elseif ( $app->isSite() && isset( $app->cck_app['Header'] ) ) {
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
		$option		=	$app->input->get( 'option', '' );
		$view		=	$app->input->get( 'view', '' );
		$layout		=	$app->input->get( 'layout', '' );
		
		// site
		if ( $app->isSite() ) {
			if ( $this->multisite === true && $this->site_cfg->get( 'offline' ) && isset( $this->offline_buffer ) ) {
				$uri		=	JFactory::getURI();
				$app->setUserState( 'users.login.form.data', array( 'return'=>(string)$uri ) );

				if ( JCck::on() ) {
					$app->setHeader( 'Status', '503 Service Temporarily Unavailable', 'true' );
					$app->setBody( $this->offline_buffer );
				} else {
					JResponse::setHeader( 'Status', '503 Service Temporarily Unavailable', 'true' );
					JResponse::setBody( $this->offline_buffer );
				}
			}
			return;
		}
		
		// admin
		if ( $app->isAdmin() && JFactory::getDocument()->getType() == 'html' ) {
			
			$buffer	=	( JCck::on() ) ? $app->getBody() : JResponse::getBody();
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
						$style	=	'height:14px; color:#ffffff; background-color:#0088CC; margin:0px 12px 0px 12px; padding:2px 8px 2px 8px; font-size:10px; font-weight:bold;';
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
			if ( JCck::on() ) {
				$app->setBody( $buffer );
			} else {
				JResponse::setBody( $buffer );
			}
			
			return;
		}
	}
	
	// onContentPrepareForm
	public function onContentPrepareForm( $form, $data )
	{
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$event		=	'onContentPrepareForm';
			$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );

			if ( isset( $processing[$event] ) ) {
				foreach ( $processing[$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						include_once JPATH_SITE.$p->scriptfile;
					}
				}
			}
		}
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
		
		if ( !JCck::on() ) {
			if ( $option == 'com_cck' ) {
				if ( $view == 'types' ) {
					$replace	=	'<a href="index.php?option=com_cck&view=versions&filter_e_type=type&e_id=0">'.JText::_( 'COM_CCK_VERSIONS' ).'</a>';
					$buffer		=	$this->_reSubmenu( $buffer, '#<ul id="submenu">(.*)</ul>#sU', $replace );
				} elseif( $view == 'searchs' ) {
					$replace	=	'<a href="index.php?option=com_cck&view=versions&filter_e_type=search&e_id=0">'.JText::_( 'COM_CCK_VERSIONS' ).'</a>';
					$buffer		=	$this->_reSubmenu( $buffer, '#<ul id="submenu">(.*)</ul>#sU', $replace );
				} elseif( $view == 'versions' ) {
					$replace	=	'<a href="#" class="active">'.JText::_( 'COM_CCK_VERSIONS' ).'</a>';
					$buffer		=	$this->_reSubmenu( $buffer, '#<ul id="submenu">(.*)</ul>#sU', $replace );
				}
			}
			
			$search		=	'<ul id="submenu">';
			$replace	=	'<ul class="lavaLampNoImage" id="submenu">';
			$buffer		=	str_replace( $search, $replace, $buffer );
		}
		
		return $buffer;
	}
	
	// _setHomepage
	protected function _setHomepage( $id )
	{
		if ( !$id ) {
			return;
		}
		$app		=	JFactory::getApplication();
		$menu		=	JMenu::getInstance( 'site' );
		$home		=	$menu->getDefault();
		$my			=	$menu->getItem( $id );
		
		$path		=	substr( JURI::getInstance()->getPath(), 1 );
		
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