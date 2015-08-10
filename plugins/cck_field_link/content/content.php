<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
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
		$tmpl				=	$link->get( 'tmpl', '' );
		$tmpl				=	$tmpl ? 'tmpl='.$tmpl : '';
		$vars				=	$tmpl;
		
		if ( ( $content == '2' || (int)$itemId < 0 ) && $sef ) {
			$field->link	=	'';
			$pk				=	( $content == '2' ) ? 0 : $config['pk'];
			parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'fieldname'=>$link->get( 'content_fieldname', '' ), 'fieldname2'=>$link->get( 'itemid_fieldname', '' ), 'fieldnames'=>$link->get( 'itemid_mapping', '' ), 'itemId'=>$itemId, 'location'=>$link->get( 'content_location', $config['location'] ), 'pk'=>$pk, 'sef'=>$sef, 'vars'=>$vars, 'custom'=>$custom ) );
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
		} else {
			$field->link		=	( $config['location'] ) ? JCck::callFunc_Array( 'plgCCK_Storage_Location'.$config['location'], 'getRouteByStorage', array( &$config['storages'], $sef, $itemId, $config, $lang_tag ) ) : '';
		}
		if ( $field->link ) {
			if ( $vars ) {
				$field->link	.=	( strpos( $field->link, '?' ) !== false ) ? '&'.$vars : '?'.$vars;
			}
			if ( $custom ) {
				$field->link	.=	( $custom[0] == '#' ) ? $custom : ( ( strpos( $field->link, '?' ) !== false ) ? '&'.$custom : '?'.$custom );
			}
		}
		if ( $app->isAdmin() ) {
			$field->link	=	str_replace( '/administrator', '', $field->link );
		}
		if ( $link->get( 'path_type', 0 ) ) {
			$field->link	=	JUri::getInstance()->toString( array( 'scheme', 'host' ) ).$field->link;
		}
		$field->link_attributes	=	$link_attr ? $link_attr : ( isset( $field->link_attributes ) ? $field->link_attributes : '' );
		$field->link_class		=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
		$field->link_rel		=	$link_rel ? $link_rel : ( isset( $field->link_rel ) ? $field->link_rel : '' );
		$field->link_state		=	$link->get( 'state', 1 );
		$field->link_target		=	$link_target ? $link_target : ( isset( $field->link_target ) ? $field->link_target : '' );
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
			$itemId				=	JFactory::getApplication()->input->get( 'Itemid' );
			$fieldname2			=	$process['fieldname2'];
			if ( isset( $fields[$fieldname2] ) ) {
				$itemId			=	(int)$fields[$fieldname2]->value;
			}
		} elseif ( $itemId == '-3' ) {
			$itemId		=	JFactory::getApplication()->input->get( 'Itemid' );
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
		if ( $fields[$name]->link ) {
			if ( $process['vars'] ) {
				$fields[$name]->link	.=	( strpos( $fields[$name]->link, '?' ) !== false ) ? '&'.$process['vars'] : '?'.$process['vars'];
			}
			if ( $process['custom'] ) {
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
			require_once JPATH_SITE.'/components/com_cck/helpers/helper_define.php';
			require_once JPATH_SITE.'/components/com_cck/helpers/helper_include.php';
			
			// Prepare
			jimport( 'cck.base.list.list' );
			include JPATH_SITE.'/libraries/cck/base/list/list_inc.php';

			$items[$goto]	=	$items;
		}

		return $items[$goto];
	}
}
?>