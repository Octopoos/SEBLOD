<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: mod_cck_search.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$show	=	$params->get( 'url_show', '' );
$hide	=	$params->get( 'url_hide', '' );
if ( $show && JCckDevHelper::matchUrlVars( $show ) === false ) {
	return;
}
if ( $hide && JCckDevHelper::matchUrlVars( $hide ) !== false ) {
	return;
}

$app	=	JFactory::getApplication();
$form	=	'';
$uniqId	=	'm'.$module->id;
$formId	=	'seblod_form_'.$uniqId;
$itemId	=	(string)$params->get( 'menu_item', '' );

JCck::loadjQuery();
JFactory::getLanguage()->load( 'com_cck_default', JPATH_SITE );

$preconfig					=	array();
$preconfig['action']		=	'';
$preconfig['client']		=	'search';
$preconfig['formId']		=	$formId;
$preconfig['submit']		=	'JCck.Core.submit_'.$uniqId;
$preconfig['search']		=	$params->get( 'search', '' );
$preconfig['itemId']		=	$app->input->getInt( 'Itemid', 0 );
$preconfig['task']			=	( $app->input->get( 'option', '' ) == 'com_cck' && $app->input->get( 'task', '' ) ) ? 'search2' : 'no';
$preconfig['show_form']		=	1;
$preconfig['auto_redirect']	=	0;
$preconfig['limit2']		=	$params->get( 'limit2', 5 );
$preconfig['ordering']		=	$params->get( 'ordering', '' );
$preconfig['ordering2']		=	$params->get( 'ordering2', '' );

$action_url		=	'';
$action_vars	=	'';

if ( $itemId == '-1' ) {
	$action_url		=	JUri::getInstance()->toString( array( 'path' ) );
} elseif ( $itemId ) {
	$action_vars	=	'&Itemid='.$params->get( 'menu_item', '' );
}
$limitstart			=	-1;
$live				=	urldecode( $params->get( 'live' ) );
$order_by			=	'';
$target				=	$params->get( 'menu_item_search', 0 );
$variation			=	$params->get( 'variation' );

jimport( 'cck.base.list.list' );
include JPATH_SITE.'/libraries/cck/base/list/list_inc.php';

// Set
if ( !is_object( @$options ) ) {
	$options	=	new JRegistry;
}
$description		=	'';
$show_list_desc		=	$params->get( 'show_list_desc' );
$show_list_title	=	( $params->exists( 'show_list_title' ) ) ? $params->get( 'show_list_title' ) : '0';
$tag_desc			=	$params->get( 'tag_list_desc', 'div' );
if ( $show_list_title == '' ) {
	$show_list_title	=	$options->get( 'show_list_title', '1' );
	$tag_list_title		=	$options->get( 'tag_list_title', 'h2' );
	$class_list_title	=	$options->get( 'class_list_title' );
} elseif ( $show_list_title ) {
	$tag_list_title		=	$params->get( 'tag_list_title', 'h2' );
	$class_list_title	=	$params->get( 'class_list_title' );
}
if ( $show_list_desc == '' ) {
	$show_list_desc	=	$options->get( 'show_list_desc', '1' );
	$description	=	@$search->description;
} else {
	$description	=	$params->get( 'list_desc', @$search->description );
}
if ( $description != '' ) {
	$description	=	str_replace( '[title]', $module->title, $description );
	$description	=	str_replace( '$cck->get', '$cck-&gt;get', $description );
	if ( strpos( $description, '$cck-&gt;get' ) !== false ) {
		$matches	=	'';
		$regex		=	'#\$cck\-\&gt;get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_]*)\' ?\)(;)?#';
		preg_match_all( $regex, $description, $matches );
		if ( count( $matches[1] ) ) {
			foreach ( $matches[1] as $k=>$v ) {
				$fieldname		=	$matches[2][$k];
				$target			=	strtolower( $v );
				if ( count( @$doc->list ) ) {
					$description	=	str_replace( $matches[0][$k], current( $doc->list )->fields[$fieldname]->{$target}, $description );
				} else {
					$description	=	str_replace( $matches[0][$k], '', $description );
				}
			}
		}
	}
}
if ( $target ) {
	$target	=	$app->getMenu()->getItem( str_replace( '&Itemid=', '', $params->get( 'menu_item', $itemId ) ) );
	if ( isset( $target->query['option'] ) && $target->query['option'] == 'com_cck'
	  && isset( $target->query['view'] ) && $target->query['view'] == 'list'
	  && isset( $target->query['search'] ) && $target->query['search'] ) {
		$preconfig['search']	=	$target->query['search'];		
	}
}
$raw_rendering		=	$params->get( 'raw_rendering', 0 );
$moduleclass_sfx	=	htmlspecialchars( $params->get( 'moduleclass_sfx' ) );
$class_sfx			=	( $params->get( 'force_moduleclass_sfx', 0 ) == 1 ) ? $moduleclass_sfx : '';
require JModuleHelper::getLayoutPath( 'mod_cck_search', $params->get( 'layout', 'default' ) );
?>