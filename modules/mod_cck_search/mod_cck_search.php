<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: mod_cck_search.php sebastienheraud $
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
$form	=	'';
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
$preconfig['itemId']		=	$app->input->getInt( 'Itemid', 0 );
$preconfig['task']			=	( $app->input->get( 'option', '' ) == 'com_cck' && $app->input->get( 'task', '' ) ) ? 'search2' : 'no';
$preconfig['show_form']		=	1;
$preconfig['auto_redirect']	=	0;
$preconfig['limit2']		=	$params->get( 'limit2', 5 );
$preconfig['ordering']		=	$params->get( 'ordering', '' );
$preconfig['ordering2']		=	$params->get( 'ordering2', '' );

$action_vars		=	( $params->get( 'menu_item', '' ) ) ? '&Itemid='.$params->get( 'menu_item', '' ) : '';
$live				=	urldecode( $params->get( 'live' ) );
$target			=	$params->get( 'menu_item_search', 0 );
$variation			=	$params->get( 'variation' );
$limitstart			=	-1;

jimport( 'cck.base.list.list' );
include JPATH_LIBRARIES_CCK.'/base/list/list_inc.php';

if ( $target ) {
	$target	=	$app->getMenu()->getItem( str_replace( '&Itemid=', '', $itemId ) );
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