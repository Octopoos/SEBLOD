<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: integration.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckDevIntegration
abstract class JCckDevIntegration
{
	// addDropdown
	public static function addDropdown( $view, $variables = '', $options = NULL )
	{
		$doc	=	JFactory::getDocument();
		$html	=	'';
		$lang	=	JFactory::getLanguage();
		if ( is_null( $options ) ) {
			$options	=	new JRegistry;
		}
		
		if ( $view == 'form' ) {
			$id		=	'toolbar-new';
			$items	=	self::getForms();
			$link	=	'index.php?option=com_cck&view=form';
			$title	=	JText::_( 'LIB_CCK_INTEGRATION_SELECT_A_FORM' );
			$user	=	JFactory::getUser();			
			$var	=	'&type=';
			foreach ( $items as $item ) {
				if ( $user->authorise( 'core.create', 'com_cck.form.'.$item->id ) ) {
					$key	=	'APP_CCK_FORM_'.$item->name;
					$lang->load( 'pkg_app_cck_'.$item->folder_app, JPATH_SITE, null, false, false );			
					if ( $lang->hasKey( $key ) == 1 ) {
						$text	=	JText::_( $key );
					} else {
						$text	=	( strlen( $item->title ) > 30 ) ? substr( $item->title, 0, 30 ) . '..' : $item->title;
					}
					$html	.=	'<li><a href="'.$link.$var.$item->name.$variables.'">' . $text . '</a></li>';
				}
			}
		} elseif ( $view == 'module' ) {
			$id		=	'toolbar-popup-new';
			$items	=	JCckDatabase::loadObjectList( 'SELECT name as text, extension_id as value FROM #__extensions WHERE element LIKE "mod_cck_%" AND client_id = 0 ORDER BY text' );
			$link	=	'index.php?option=com_modules&task=module.add';
			$title	=	JText::_( 'LIB_CCK_INTEGRATION_SELECT_A_MODULE_TYPE' );
			$var	=	'&eid=';
			foreach ( $items as $item ) {
				$text	=	JText::_( $item->text );
				$html	.=	'<li><a href="'.$link.$var.$item->value.$variables.'">' . $text . '</a></li>';
			}
		} else {
			$id		=	'toolbar-new';
			switch ( $view ) {
				case 'types':
					$items	=	JCckDatabase::loadObjectList( 'SELECT CONCAT(a.title, " (", LCASE(p.title), ")") as text, a.id as value FROM #__cck_core_folders AS a LEFT JOIN #__cck_core_folders AS p ON p.id = a.parent_id WHERE a.featured = 1 ORDER BY text' );
					$link	=	'index.php?option=com_cck&task=type.add';
					$var	=	'&skeleton_id=';
					$title	=	JText::_( 'COM_CCK_TIP_NEW_TYPE' );
					break;
				case 'fields':
					$items	=	JCckDatabase::loadObjectList( 'SELECT name as text, element as value FROM #__extensions WHERE folder = "cck_field" AND enabled = 1 ANd element != "storage" ORDER BY text' );	
					$link	=	'index.php?option=com_cck&task=field.add';
					$var	=	'&ajax_type=';
					$title	=	JText::_( 'COM_CCK_TIP_NEW_FIELD' );
					break;
				case 'searchs':
					$items	=	JCckDatabase::loadObjectList( 'SELECT title as text, name as value FROM #__cck_core_types WHERE published = 1 ORDER BY text' );
					$link	=	'index.php?option=com_cck&task=search.add';
					$var	=	'&content_type=';
					$title	=	JText::_( 'COM_CCK_TIP_NEW_SEARCH' );
					break;
				case 'templates':
					$items	=	array( (object)array( 'text'=>JText::_( 'COM_CCK_CONTENT_FORM' ), 'value'=>0 ), (object)array( 'text'=>JText::_( 'COM_CCK_LIST' ), 'value'=>2 ) );
					$link	=	'index.php?option=com_cck&task=template.add';
					$var	=	'&mode=';
					$title	=	JText::_( 'COM_CCK_TIP_NEW_TEMPLATE' );
					break;
				case 'sites':
					$items	=	array( (object)array( 'text'=>JText::_( 'COM_CCK_BASIC' ), 'value'=>'7' ),
									   (object)array( 'text'=>JText::_( 'COM_CCK_STANDARD' ), 'value'=>'2,7' ),
									   (object)array( 'text'=>JText::_( 'COM_CCK_ADVANCED' ), 'value'=>'2,3,6,7' ) );
					$link	=	'index.php?option=com_cck&task=site.add';
					$var	=	'&type=';
					$title	=	JText::_( 'COM_CCK_TIP_NEW_SITE' );
					break;
				default:
					break;
			}
			foreach ( $items as $item ) {
				$text	=	( strlen( $item->text ) > 30 ) ? substr( $item->text, 0, 30 ) . '..' : $item->text;
				$html	.=	'<li><a href="'.$link.$var.$item->value.$variables.'">' . $text . '</a></li>';
			}
		}
		
		if ( count( $items ) && $html != '' ) {			
			if ( JCck::on() ) {
				$legacy	=	$options->get( 'add_alt' );
				if ( $legacy == 1 ) {
					$above	=	'<li class="nav-header">'.JText::_( 'LIB_CCK_JOOMLA' ).'</li><li><a href="'.$options->get( 'add_alt_link', '#' ).'" id="joomla-standard-content">'.JText::_( 'LIB_CCK_INTEGRATION_STANDARD_CONTENT' ).'</a></li><li class="nav-header">'.JText::_( 'LIB_CCK_SEBLOD' ).'</li>';
					$below	=	'';
				} elseif ( $legacy == 2 ) {
					$above	=	'<li class="nav-header">'.JText::_( 'LIB_CCK_SEBLOD' ).'</li>';
					$below	=	'<li class="nav-header">Joomla!</li><li><a href="'.$options->get( 'add_alt_link', '#' ).'" id="joomla-standard-content">'.JText::_( 'LIB_CCK_INTEGRATION_STANDARD_CONTENT' ).'</a></li>';
				} else {
					$above	=	'';
					$below	=	'';
				}
				$html	=	'<ul class="dropdown-menu">'.$above.$html.$below.'</ul>';
				$css	=	'.subhead .dropdown-menu {text-shadow: none;} #toolbar ul.dropdown-menu{margin-left:18px;} #toolbar ul.dropdown-menu li {font-size:12px;}';
				$js		=	'
							jQuery(document).ready(function($){
								$("#'.$id.' > button").addClass("dropdown-toggle").attr("data-toggle","dropdown").attr("onclick","return;");
								$("#'.$id.'").append("'.addslashes( $html ).'");
							});
							';
			} else {
				$css	=	'
							ul.toolbar-tiplist {padding: 0px;}
							ul.toolbar-tiplist li { list-style: none; margin-top: 3px; margin-bottom: 3px; padding: 5px;}
							ul.toolbar-tiplist li:hover {background-color: #ffffff; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px;}
							';
				$doc->addStyleDeclaration( $css );
				$doc->addStyleSheet( JURI::root( true ).'/media/cck/scripts/jquery-qtip/css/jquery.qtip.css' );
				JCck::loadjQuery();
				$doc->addScript( JURI::root( true ).'/media/cck/scripts/jquery-qtip/js/jquery.qtip.min.js' );
				
				// Tooltip
				$html		=	'<div><ul class="toolbar-tiplist">'.$html.'</ul></div>' . '<div class="clr"></div>';
				$search		=	array( '.' , '<', '>', '"', '%', ';' );
				$replace	=	array( '\.', '\<', '\>', '\"', '\%', '\;' );
				$html		=	preg_replace( "/(\r\n|\n|\r)/", " ", $html );
				$html		=	str_replace( $search, $replace, $html );
			
				$js	=	'
						jQuery(document).ready(function($){
							$("#'.$id.' a").qtip({
								prerender: true,
								content: { text: "'.$html.'", title: { text: "'.$title.'" } },
								hide: { event: "unfocus" },
								style: { tip: true, classes: "ui-tooltip-grey ui-tooltip-rounded" },
								position: { at: "bottom center", my: "top center" }
							})
						});
						';
			}
			$doc->addStyleDeclaration( $css );
			$doc->addScriptDeclaration( $js );
		}
	}
	
	// addModalBox
	public static function addModalBox( $layout = 'icon', $variables = '', $options = NULL )
	{
		if ( JCck::on() ) {
			JCck::loadjQuery();
			$layout	=	JPATH_ADMINISTRATOR.'/components/com_cck/views/form/tmpl/modal_'.$layout.'.php';
			self::appendModal( $layout, 'collapseModal2', '#toolbar-new > button', array(), $variables, $options );
		} else {
			JCck::loadjQuery();
			$doc	=	JFactory::getDocument();
			$uri	=	JFactory::getURI();
			$return	=	base64_encode( $uri );

			$doc->addScript( JURI::root( true ).'/media/cck/scripts/jquery-colorbox/js/jquery.colorbox-min.js' );
			$doc->addStyleSheet( JURI::root( true ).'/media/cck/scripts/jquery-colorbox/css/colorbox.css' );
			$href	=	'index.php?option=com_cck&view=form&layout=select&tmpl=component&variables='.base64_encode( $variables ).'&return='.$return;
			$js		=	'
						jQuery(document).ready(function($){
							var origin = $("#toolbar-new a").attr("onclick");
							$("#toolbar-new a").attr("onclick","").attr("onclick2",origin).attr("href","'.$href.'");
							$("#toolbar-new a").live("click", function(e) { e.preventDefault();
								$.fn.colorbox({href:$(this).attr(\'href\'),open:true,iframe:true,innerWidth:850,innerHeight:430,scrolling:true,overlayClose:false,fixed:true});
								return false;
							});
						});
						';
			$doc->addScriptDeclaration( $js );
		}
	}

	// addWarning
	public static function addWarning( $type )
	{
		$doc	=	JFactory::getDocument();
		$text	=	JText::_( 'LIB_CCK_INTEGRATION_WARNING_COPY' );

		if ( JCck::on() ) {
			$js		=	'jQuery(document).ready(function(){ if(jQuery("#batch-category-id")) {jQuery("#batch-category-id").parent().after("'.addslashes( '<em>'.$text.'</em>' ).'"); }});';
		} else {
			$html	=	'<img id="cck_warning" class="hasTip2" title="'.htmlspecialchars( $text, ENT_COMPAT, 'UTF-8' ).'"'
					.	' src="components/com_cck/assets/images/16/icon-16-notice.png" alt="Copy is not supported yet." />';
			
			$js		=	'jQuery(document).ready(function(){ if(jQuery("#batch-category-id")) {jQuery("#batch-category-id").after("'.addslashes( $html ).'"); var JTooltips = new Tips($$(\'.hasTip2\'), { maxTitleChars: 50, fixed: false}); }});';
		}
		
		JCck::loadjQuery();
		$doc->addScriptDeclaration( $js );
	}

	// appendModal
	public static function appendModal( $layout, $target_id, $trigger, $params = array(), $variables = '', $options = NULL )
	{
		$doc	=	JFactory::getDocument();

		if ( is_null( $options ) ) {
			$options	=	new JRegistry;
		}
		if ( is_file( $layout ) ) {
			ob_start();
			include $layout;
			$html	=	ob_get_clean();
			$html	=	preg_replace( '/(\r\n|\n|\r|\t)/', '', $html );
		}
		$js		=	'jQuery(document).ready(function($){
						$("'.$trigger.'").attr("data-toggle","modal").attr("data-target","#'.$target_id.'").attr("onclick","return;");
						$("body").append("'.addslashes( $html ).'");
					});';
		$doc->addScriptDeclaration( $js );
	}

	// getForms
	public static function getForms( $url = '', &$type = '', $grouping = '' )
	{
		$app	=	JFactory::getApplication();
		$items	=	array();

		if ( is_object( $url ) ) {
			$option	=	$url->get( 'option', '' );
			$view	=	$url->get( 'view', '' );
		} elseif ( $url != 'none' ) {
			$option	=	$app->input->get( 'option', '' );
			$view	=	$app->input->get( 'view', '' );
		} else {
			$option	=	'';
		}
		
		$in			=	'';
		$where		=	'';
		if ( $option ) {
			$where		=	'WHERE a.component = "'.$option.'"';
			if ( $view ) {
				$where	.=	' AND ( a.view = "'.$view.'" OR a.view = "" )';
			}
		}
		$locations	=	JCckDatabase::loadObjectList( 'SELECT a.name, a.vars FROM #__cck_core_objects AS a '.$where );
		if ( count( $locations ) ) {
			$state	=	true;
			foreach ( $locations as $location ) {
				if ( $location->vars ) {
					$state	=	JCckDevHelper::matchUrlVars( $location->vars, $url );
				}
				if ( $state !== false ) {
					$in	.=	'"'.$location->name.'",';
				}
			}
		}
		
		if ( $in )  {
			$type	=	substr( $in, 1, -2 );
			$in		.=	'""';
			if ( $grouping == 'folder' ) {
				$call		=	'loadObjectListArray';
				$index		=	'folder_id';
				$order_by	=	' ORDER BY folder ASC, title ASC';
			} else {
				$call		=	'loadObjectList';
				$index		=	NULL;
				$order_by	=	' ORDER BY title';				
			}
			$items	=	JCckDatabase::$call( 'SELECT a.id, a.title, a.name, a.description, b.id as folder_id, b.title as folder, b.app as folder_app, b.icon_path as folder_icon'
											.' FROM #__cck_core_types AS a'
											.' LEFT JOIN #__cck_core_folders AS b ON b.id = a.folder'
											.' WHERE a.published = 1 AND a.location != "none" AND a.location != "site"'
											.' AND a.storage_location IN ('.$in.')'.$order_by, $index );
		}
		
		return $items;
	}

	// redirect
	public static function redirect( $type, $more = '' )
	{
		$ignore	=	JFactory::getApplication()->input->get( 'cck', '0' );

		if ( !$type || $type == '-1' || $ignore ) {
			return;
		}
		$id	=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_core_types WHERE name = "'.$type.'"' );
		if ( !JFactory::getUser()->authorise( 'core.create', 'com_cck.form.'.$id ) ) {
			return;
		}
		
		$url	=	'index.php?option=com_cck&view=form&layout=edit&type='.$type.$more;
		JFactory::getApplication()->redirect( $url );
	}

	// rewriteBuffer
	public static function rewriteBuffer( $buffer, $data, $list, $list_assoc = array() )
	{
		$app			=	JFactory::getApplication();
		$class			=	JCck::on( '3.4' ) ? ' class="hasTooltip"' : '';
		$idx			=	0;
		$idx2			=	2;
		$idx3			=	3;
		$items			=	array();
		$list2			=	array();
		$multilanguage	=	0;
		
		if ( JCckDevHelper::hasLanguageAssociations() ) {
			$multilanguage	=	( isset( $data['multilanguage'] ) && $data['multilanguage'] ) ? 1 : 0;
		}
		$pks				=	'';
		$return				=	( $data['return_view'] ) ? '&return_o='.$data['return_option'].'&return_v='.$data['return_view'] : '&return_o='.$data['return_option'];

		preg_match_all( $data['search'], $buffer, $matches );

		if ( strpos( $data['search'], '>' ) !== false ) {
			$isComplete		=	true;
			$markup_end		=	'';
		} else {
			$isComplete		=	false;
			$markup_end		=	'>';
		}
		$opt_default_type	=	$data['options']->get( 'default_type', '' );
		$opt_edit_alt		=	$data['options']->get( 'edit_alt', 1 );

		if ( count( $matches[$idx2] ) ) {
			if ( $data['options']->get( 'edit', 0 ) == 1 ) {
				$i	=	0;
				foreach ( $matches[$idx2] as $k=>$m ) {
					$type		=	@$list[$m]->cck;
					$type		=	( $type ) ? '&type='.$type : '&type='.$opt_default_type;
					$search		=	$matches[$idx][$k];
					$list2[$m]	=	array( 'link'=>'index.php?option=com_cck&amp;view=form'.$return.$type.'&id='.$m.$data['replace_end'] );
					$replace=	'<a'.$class.' href="'.$list2[$m]['link'];	
					if ( $isComplete ) {
						$replace	.=	' '.$matches[$idx3][$k].'>';	
					}
					$buffer		=	str_replace( $search, $replace, $buffer );
					$items[$i]	=	$matches[$idx][$k];
					$i++;
				}
			} else {
				$i	=	0;
				foreach ( $matches[$idx2] as $k=>$m ) {
					if ( isset( $list[$m]->cck ) ) {
						$type		=	$list[$m]->cck;
						$type		=	( $type ) ? '&type='.$type : '&type='.$opt_default_type;
						$search		=	$matches[$idx][$k];
						$list2[$m]	=	array( 'link'=>'index.php?option=com_cck&amp;view=form'.$return.$type.'&id='.$m.$data['replace_end'] );
						$replace	=	'<a'.$class.' href="'.$list2[$m]['link'];
						if ( $isComplete ) {
							$replace	.=	' '.$matches[$idx3][$k].'>';	
						}
						$buffer		=	str_replace( $search, $replace, $buffer );
						$items[$i]	=	$matches[$idx][$k];
						$i++;
					}
				}
			}
		}
		if ( JCck::on() ) {
			if ( $data['search_alt'] ) {
				$search	=	$data['search_alt'];
				preg_match_all( $search, $buffer, $matches2 );
				if ( count( $matches2[0] ) ) {
					if ( $multilanguage ) {
						$languages	=	JLanguageHelper::getLanguages( 'lang_code' );
					}
					foreach ( $matches2[0] as $k=>$m ) {
						$pk			=	$matches[$idx2][$k];
						$pre		=	'';
						$row		=	$matches2[0][$k];
						$search		=	'';
						$t_add		=	'';
						$t_edit		=	'';
						if ( isset( $matches[$idx][$k] ) ) {
							if ( $opt_edit_alt ) {
								if ( isset( $list2[$pk] ) ) {
									$text		=	'<span class="icon-pencil"></span> '.JText::_( 'JTOOLBAR_EDIT' ).' ('.JText::_( 'LIB_CCK_LEGACY' ).')';
									$pre		=	$matches[$idx][$k].$markup_end.$text.'</a></li>';
								} else {
									$link		=	'index.php?option=com_cck&amp;view=form'.$return.'&type='.$opt_default_type.'&id='.$pk.$data['replace_end'];
									$text		=	'<span class="icon-pencil"></span> '.JText::_( 'JTOOLBAR_EDIT' ).' ('.JText::_( 'LIB_CCK_SEBLOD' ).')';
									$pre		=	'<a href="'.$link.'">'.$text.'</a></li>';
								}
							}
							if ( $multilanguage ) {
								if ( isset( $list[$pk] ) && $list[$pk]->key ) {
									$cur	=	$list[$pk]->language;
									$key	=	$list[$pk]->key;
									$link	=	'index.php?option=com_cck&amp;view=form'.$return.'&type='.$list[$pk]->cck;
									foreach ( $languages as $l=>$v ) {
										if ( $cur != $l ) {
											if ( isset( $list_assoc[$key][$l] ) ) {
												$link2	=	$link.'&amp;id='.$list_assoc[$key][$l]->id.$data['replace_end'];
												$t_edit	.=	'<li><a href="'.$link2.'&plop=1"><span class="icon-arrow-right-3"> '.$l.'</a></li>';
											} else {
												$link2	=	$link.'&amp;translate='.$l.'&amp;translate_id='.$pk.$data['replace_end'];
												$t_add	.=	'<li><a href="'.$link2.'"><span class="icon-arrow-right-3"> '.$l.'</a></li>';
											}
										}
									}
									if ( $t_edit || $t_add ) {
										$pre		.=	'<li class="divider"></li>';
										if ( $t_edit ) {
											$pre	.=	'<li><a href="javascript:void(0);"><span class="icon-comments-2"></span> '.JText::_( 'LIB_CCK_TRANSLATE_EDIT' ).'</a></li>'.$t_edit;
										}
										if ( $t_add ) {
											$pre	.=	'<li><a href="javascript:void(0);"><span class="icon-comments-2"></span> '.JText::_( 'LIB_CCK_TRANSLATE' ).'</a></li>'.$t_add;
										}
									}
								}
							}
							if ( $pre != '' ) {
								$pre		.=	'<li class="divider"></li>';
								$buffer		=	str_replace( $row, $pre.'<li>'.$row, $buffer );	
							}
						}
					}
				}
			}
		} elseif ( $data['options']->get( 'edit_alt', 1 ) ) {
			$search	=	'#<a href="index.php\?option=com_cck&amp;view=form(.*):(.*)([a-z\-0-9]*)\)(.*)</p>#sU';
			preg_match_all( $search, $buffer, $matches2 );
			
			if ( count( $matches2[3] ) ) {
				foreach ( $matches2[3] as $k=>$m ) {
					if ( isset( $matches[$idx][$k] ) ) {
						$search		=	': '.$matches2[3][$k].')';
						$replace	=	': '.$items[$k].' style="color: #888888;">'.$m.'</a>)';
						$matches0	=	str_replace( $search, $replace, $matches2[0][$k] );
						$buffer		=	str_replace( $matches2[0][$k], $matches0, $buffer );
					}
				}
			}
		}
		
		return $buffer;
	}
}
?>