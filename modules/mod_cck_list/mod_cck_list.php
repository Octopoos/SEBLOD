<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: mod_cck_list.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
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

if ( ! defined ( 'JPATH_LIBRARIES_CCK' ) ) {
	define( 'JPATH_LIBRARIES_CCK',	JPATH_SITE.'/libraries/cck' );
}
if ( ! defined ( 'JROOT_MEDIA_CCK' ) ) {
	define( 'JROOT_MEDIA_CCK',	JURI::root( true ).'/media/cck' );
}
JCck::loadjQuery();
JFactory::getLanguage()->load( 'com_cck_default', JPATH_SITE );
require_once JPATH_SITE.'/components/com_cck/helpers/helper_include.php';

$preconfig					=	array();
$preconfig['action']		=	'';
$preconfig['client']		=	'search';
$preconfig['formId']		=	$formId;
$preconfig['submit']		=	'JCck.Core.submit_'.$uniqId;
$preconfig['search']		=	$params->get( 'search', '' );
$preconfig['itemId']		=	'';
$preconfig['task']			=	'search';
$preconfig['show_form']		=	1;
$preconfig['auto_redirect']	=	0;
$preconfig['limit2']		=	$params->get( 'limit2', 5 );
$preconfig['ordering']		=	$params->get( 'ordering', '' );
$preconfig['ordering2']		=	$params->get( 'ordering2', '' );

$live		=	urldecode( $params->get( 'live' ) );
$variation	=	$params->get( 'variation' );
$limitstart	=	-1;

jimport( 'cck.base.list.list' );
include JPATH_LIBRARIES_CCK.'/base/list/list_inc.php';

// Set
if ( !is_object( @$options ) ) {
	$options	=	new JRegistry;
}
$description	=	'';
$show_list_desc	=	$params->get( 'show_list_desc' );
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
$show_more			=	$params->get( 'show_link_more', 0 );
$show_more_class	=	$params->get( 'link_more_class', '' );
$show_more_class	=	( $show_more_class ) ? ' class="'.$show_more_class.'"' : '';
$show_more_link		=	'';
if ( $show_more ) {
	$show_more_link	=	'index.php?Itemid='.$show_more;
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