<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Field_LinkContent extends JCckPluginLink
{
	protected static $type	=	'content';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_LinkPrepareContent
	public static function onCCK_Field_LinkPrepareContent( &$field, &$config = array() )
	{		
		if ( self::$type != $field->link ) {
			return;
		}
		
		// Prepare
		$link	=	parent::g_getLink( $field->link_options );
		
		// Set
		$field->link	=	'';
		self::_link( $link, $field, $config );
	}
	
	// _link
	protected static function _link( $link, &$field, &$config )
	{
		$app		=	JFactory::getApplication();
		$sef		=	$link->get( 'sef', $config['doSEF'] );
		if ( !JFactory::getConfig()->get( 'sef' ) ) {
			$sef	=	0;
		}
		$itemId		=	( $sef ) ? $link->get( 'itemid', '' ) : '';
		$content	=	$link->get( 'content', '' );
		$custom		=	$link->get( 'custom', '' );
		$path_type	=	(int)$link->get( 'path_type', 0 );
		
		// Prepare
		if ( !$itemId ) {
			$view	=	$app->input->get( 'view', '' );
			$layout	=	$app->input->get( 'layout', '' );
			if ( ( $view == 'category' && $layout == 'blog' ) || $view == 'featured' ) {
				$sef	=	0;
			}
			if ( $sef ) {
				$itemId		=	$app->input->getInt( 'Itemid', 0 );
			}
		}
		$lang_tag			=	$link->get( 'language', '' );
		$link_attr			=	$link->get( 'attributes', '' );
		$link_class			=	$link->get( 'class', '' );
		$link_rel			=	$link->get( 'rel', '' );
		$link_target		=	$link->get( 'target', '' );
		$link_title			=	$link->get( 'title', '' );
		$link_title2		=	$link->get( 'title_custom', '' );
		$tmpl				=	$link->get( 'tmpl', '' );
		$tmpl				=	( $tmpl == '-1' ) ? $app->input->getCmd( 'tmpl', '' ) : $tmpl;
		$tmpl				=	( $tmpl ) ? 'tmpl='.$tmpl : '';
		$vars				=	$tmpl;
		
		if ( $link_target == 'modal' ) {
			if ( strpos( $link_attr, 'data-cck-modal' ) === false ) {
				$modal_json	=	$link->get( 'target_params', '' );

				if ( $modal_json != '' ) {
					$modal_json	=	'=\''.$modal_json.'\'';
				}
				$link_attr	=	trim( $link_attr.' data-cck-modal'.$modal_json );				
			}
		}

		if ( ( $content == '2' || (int)$itemId < 0 ) && $sef ) {
			$field->link	=	'';
			
			if ( $content == '2' ) {
				$location	=	$link->get( 'content_location' );
				$pk			=	0;
			} else {
				$location	=	$config['location'];
				$pk			=	$config['pk'];
			}
			parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'fieldname'=>$link->get( 'content_fieldname', '' ), 'fieldname2'=>$link->get( 'itemid_fieldname', '' ), 'fieldnames'=>$link->get( 'itemid_mapping', '' ), 'itemId'=>$itemId, 'location'=>$location, 'pk'=>$pk, 'sef'=>$sef, 'vars'=>$vars, 'custom'=>$custom ) );
		}
		$custom				=	parent::g_getCustomVars( self::$type, $field, $custom, $config );
		
		// Set
		if ( ( $content == '4' || $content == '5' ) ) {
			//$goto				=	self::_goTo( $app->input->getInt( 'Itemid', $config['Itemid'] ) );
			$field->link		=	'';
			if ( $content == '5' ) {
				//
			} else {
				//
			}
		} elseif ( $content != '2' ) {
			$field->link		=	( $config['location'] ) ? JCck::callFunc_Array( 'plgCCK_Storage_Location'.$config['location'], 'getRouteByStorage', array( &$config['storages'], $sef, $itemId, $config, $lang_tag ) ) : '';
		} else {
			$field->link		=	'';
		}
		if ( $field->link ) {
			if ( $vars ) {
				$field->link	.=	( strpos( $field->link, '?' ) !== false ) ? '&'.$vars : '?'.$vars;
			}
			if ( $custom ) {
				$field->link	.=	( $custom[0] == '#' ) ? $custom : ( ( strpos( $field->link, '?' ) !== false ) ? '&'.$custom : '?'.$custom );
			}
		}
		if ( $app->isClient( 'administrator' ) ) {
			$field->link	=	str_replace( '/administrator', '', $field->link );

			$link_attr		=	' data-cck-route="'.base64_encode( $field->link ).'"';

			static $loaded	=	0;

			if ( !$loaded ) {
				$loaded	=	1;
				$js		=	'(function ($){
								$(document).ready(function() {
									
									$("a[data-cck-route]").each(function(i) {
										var $el = $(this);
										$.ajax({
											cache: false,
											data: "link="+encodeURIComponent( $el.attr("data-cck-route") ),
											type: "GET",
											url: "'. JCckDevHelper::getAbsoluteUrl( 'auto', 'task=route&format=raw', 'root' ) .'",
											beforeSend:function(){},
											success: function(resp){ $el.attr("href",resp); $el.removeAttr("data-cck-route"); }
										});
									});
								});
							})(jQuery);';
				JFactory::getDocument()->addScriptDeclaration( $js );
			}
		}
		if ( $path_type ) {
			if ( $site_id = $link->get( 'site', '' ) ) {
				$base		=	'';
				$site		=	JCck::getSiteById( $site_id );
				
				if ( is_object( $site ) && $site->name != '' ) {
					$base	=	JUri::getInstance()->getScheme().'://'.$site->name;
				}
			} else {
				$base		=	JUri::getInstance()->toString( array( 'scheme', 'host' ) );
			}
			if ( $path_type == 2 || $path_type == 3 ) {
				$field->link	=	$base.$field->link;
				$segment		=	JRoute::_( 'index.php?Itemid='.$itemId );

				if ( $segment == '/' ) {
					$segment	=	'';
				}
				$base			.=	$segment.'/';
				$field->link	=	str_replace( $base, '', $field->link );
				$field->link	=	'#'.$field->link;

				if ( $path_type == 2 ) {
					$field->link	=	$base.$field->link;
				}
			} else {
				$field->link	=	$base.$field->link;
			}
		}
		$field->link_attributes	=	$link_attr ? $link_attr : ( isset( $field->link_attributes ) ? $field->link_attributes : '' );
		$field->link_class		=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
		$field->link_rel		=	$link_rel ? $link_rel : ( isset( $field->link_rel ) ? $field->link_rel : '' );
		$field->link_state		=	$link->get( 'state', 1 );
		$field->link_target		=	$link_target ? ( $link_target == 'modal' ? '' : $link_target ) : ( isset( $field->link_target ) ? $field->link_target : '' );

		if ( $link_title ) {
			if ( $link_title == '2' ) {
				$field->link_title	=	$link_title2;
			} elseif ( $link_title == '3' ) {
				$field->link_title	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $link_title2 ) ) );
			}
			if ( !isset( $field->link_title ) ) {
				$field->link_title	=	'';
			}
		} else {
			$field->link_title		=	'';
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_LinkBeforeRenderContent
	public static function onCCK_Field_LinkBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$itemId		=	@$process['itemId'];
		$name		=	$process['name'];
		$fieldname	=	$process['fieldname'];
		$location	=	$process['location'];
		if ( isset( $process['pk'] ) && $process['pk'] ) {
			$pk		=	$process['pk'];
		} else {
			$pk		=	isset( $fields[$fieldname] ) ? (int)$fields[$fieldname]->value : 0;
		}

		if ( !$pk ) {
			if ( isset( $process['matches'] ) && count( $process['matches'][1] ) ) {
				parent::g_setCustomVars( $process, $fields, $name );
			}
			return;
		}

		if ( $itemId == '-2' ) {
			$itemId				=	JFactory::getApplication()->input->getInt( 'Itemid' );
			$fieldname2			=	$process['fieldname2'];
			if ( isset( $fields[$fieldname2] ) ) {
				$itemId			=	(int)$fields[$fieldname2]->value;
			}
		} elseif ( $itemId == '-3' ) {
			$itemId		=	JFactory::getApplication()->input->getInt( 'Itemid' );
			$itemIds	=	$process['fieldnames'];
			$items		=	explode( '||', $itemIds );
			if ( count( $items ) ) {
				foreach ( $items as $item ) {
					if ( $item != '' ) {
						$parts	=	explode( '=', $item );
						if ( $parts[1] ) {
							$checks		=	json_decode( $parts[0], true );
							$count		=	count( $checks );
							$found		=	0;
							if ( $count ) {
								foreach ( $checks as $k=>$v ) {
									if ( isset( $fields[$k] ) && $fields[$k]->value == $v ) {
										$found++;
									}
								}
							}
							if ( $found == $count ) {
								$itemId	=	$parts[1];
								break;
							}
						}
					}
				}
			}
		}

		$fields[$name]->link	=	JCck::callFunc_Array( 'plgCCK_Storage_Location'.$location, 'getRoute', array( $pk, $process['sef'], $itemId, $config ) );
		$target					=	 $fields[$name]->typo_target;

		if ( isset( $fields[$name]->typo_mode ) && $fields[$name]->typo_mode ) {
			$target	=	'typo';
		}
		if ( $fields[$name]->link ) {
			if ( $process['vars'] ) {
				$fields[$name]->link	.=	( strpos( $fields[$name]->link, '?' ) !== false ) ? '&'.$process['vars'] : '?'.$process['vars'];
			}
			if ( $process['custom'] ) {
				$process['custom']		=	parent::g_getCustomVars( self::$type, $fields[$name], $process['custom'], $config );
				$fields[$name]->link	.=	( $process['custom'][0] == '#' ) ? $process['custom'] : ( ( strpos( $fields[$name]->link, '?' ) !== false ) ? '&'.$process['custom'] : '?'.$process['custom'] );
			}
			JCckPluginLink::g_setHtml( $fields[$name], $target );
		}
		if ( $fields[$name]->typo ) {
			$html						=	( isset( $fields[$name]->html ) ) ? $fields[$name]->html : '';
			if ( strpos( $fields[$name]->typo, $fields[$name]->$target ) === false ) {
				$fields[$name]->typo	=	$html;
			} else {
				$fields[$name]->typo	=	str_replace( $fields[$name]->$target, $html, $fields[$name]->typo );
			}
		}
		if ( isset( $process['matches'] ) && count( $process['matches'][1] ) ) {
			parent::g_setCustomVars( $process, $fields, $name );
		}
	}
	
	// _goto
	protected static function _goTo( $itemId )
	{
		$menu	=	JFactory::getApplication()->getMenu();
		$item	=	$menu->getItem( $itemId );
		$search	=	$item->query['search'];

		$items	=	self::_gotoItems( $search, $itemId );
	}

	// _goToItems
	protected static function _goToItems( $search, $itemId )
	{
		static $items				=	array();
		$goto						=	$search.'_'.$itemId;

		if ( !isset( $items[$goto] ) ) {
			$class_sfx					=	'';
			
			$uniqId						=	'f'.$field->id;
			$formId						=	'seblod_list_'.$uniqId;
			
			$preconfig					=	array();
			$preconfig['action']		=	'';
			$preconfig['client']		=	'search';
			$preconfig['formId']		=	$formId;
			$preconfig['submit']		=	'JCck.Core.submit_'.$uniqId;
			$preconfig['search']		=	$search;
			$preconfig['itemId']		=	$itemId;
			$preconfig['task']			=	'search';
			$preconfig['show_form']		=	0;
			$preconfig['show_list']		=	0;
			$preconfig['auto_redirect']	=	0;
			$preconfig['limit2']		=	0; // todo
			$preconfig['ordering']		=	''; // todo
			$preconfig['ordering2']		=	''; // todo
			
			$live						=	'';
			$variation					=	'';
			$limitstart					=	-1;
			
			$params						=	new JRegistry;	// todo : remove+inherit?
			$params->set( 'order_by', '' );
			
			// Prepare
			jimport( 'cck.base.list.list' );
			include JPATH_SITE.'/libraries/cck/base/list/list_inc.php';

			$items[$goto]	=	$items;
		}

		return $items[$goto];
	}
}
?>