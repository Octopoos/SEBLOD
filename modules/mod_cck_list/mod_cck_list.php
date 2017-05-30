<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: mod_cck_list.php sebastienheraud $
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
$uniqId	=	'm'.$module->id;
$formId	=	'seblod_form_'.$uniqId;

JCck::loadjQuery();
JFactory::getLanguage()->load( 'com_cck_default', JPATH_SITE );

$preconfig					=	array();
$preconfig['action']		=	'';
$preconfig['client']		=	'search';
$preconfig['formId']		=	$formId;
$preconfig['submit']		=	'JCck.Core.submit_'.$uniqId;
$preconfig['search']		=	$params->get( 'search', '' );
$preconfig['search2']		=	$params->get( 'search2', '' );
$preconfig['itemId']		=	'';
$preconfig['task']			=	'search';
$preconfig['show_form']		=	'0';
$preconfig['auto_redirect']	=	0;
$preconfig['limit']			=	$params->get( 'limit', 0 );
$preconfig['limit2']		=	$params->get( 'limit2', 5 );
$preconfig['ordering']		=	$params->get( 'ordering', '' );
$preconfig['ordering2']		=	$params->get( 'ordering2', '' );

$limitstart	=	(int)$params->get( 'limitstart', '' );
$limitstart	=	( $limitstart >= 1 ) ? ( $limitstart - 1 ) : -1;

if ( $limitstart == -1 && (int)$preconfig['limit2'] > 0 ) {
	$limitstart	=	0;
}
$live		=	urldecode( $params->get( 'live' ) );
$order_by	=	$params->get( 'order_by', '' );
$pagination	=	-2;
$variation	=	$params->get( 'variation' );

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
if ( !$total && !$options->get( 'show_list_desc_no_result', '1' ) ) {
	$show_list_desc		=	0;
	$description		=	'';
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
$show_more			=	$params->get( 'show_more', 1 );
$show_link_more		=	$params->get( 'show_link_more', 0 );
$show_more_class	=	$params->get( 'link_more_class', '' );
$show_more_class	=	( $show_more_class ) ? ' class="'.$show_more_class.'"' : '';
$show_more_text		=	$params->get( 'link_more_text', '' );
if ( $show_more_text == '' ) {
	$show_more_text	=	JText::_( 'MOD_CCK_LIST_VIEW_ALL' );
} elseif ( JCck::getConfig_Param( 'language_jtext', 0 ) ) {
	$show_more_text	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $show_more_text ) ) );
}
$show_more_link		=	'';
if ( ( $show_more == 1 || ( $show_more == 2 && $total ) || ( $show_more == 3 && $total_items > $preconfig['limit2'] ) ) && $show_link_more ) {
	$show_more_link	=	'index.php?Itemid='.$show_link_more;
	$show_more_link	=	JRoute::_( $show_more_link );
	$show_more_vars	=	$params->get( 'link_more_variables', '' );
	if ( $show_more_vars ) {
		$show_more_vars	=	JCckDevHelper::replaceLive( $show_more_vars );
		if ( $show_more_vars != '' ) {
			$show_more_link	.=	( strpos( $show_more_link, '?' ) !== false ) ? '&'.$show_more_vars : '?'.$show_more_vars;
		}
	}
}
$raw_rendering		=	$params->get( 'raw_rendering', 0 );
$moduleclass_sfx	=	htmlspecialchars( $params->get( 'moduleclass_sfx' ) );
$class_sfx			=	( $params->get( 'force_moduleclass_sfx', 0 ) == 1 ) ? $moduleclass_sfx : '';
require JModuleHelper::getLayoutPath( 'mod_cck_list', $params->get( 'layout', 'default' ) );
?>