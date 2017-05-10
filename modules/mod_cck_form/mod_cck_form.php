<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: mod_cck_form.php sebastienheraud $
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
$data	=	'';
$uniqId	=	'm'.$module->id;
$formId	=	'seblod_form_'.$uniqId;

JCck::loadjQuery();
JFactory::getLanguage()->load( 'com_cck_default', JPATH_SITE );

$option					=	$app->input->get( 'option', '' );
$view					=	'';
$preconfig				=	array();
$preconfig['action']	=	'';
$preconfig['client']	=	'site';
$preconfig['formId']	=	$formId;
$preconfig['submit']	=	'JCck.Core.submit_'.$uniqId;
$preconfig['task']		=	$app->input->get( 'task', '' );
$preconfig['type']		=	$params->get( 'type', '' );
$preconfig['url']		=	'';

$live		=	urldecode( $params->get( 'live' ) );
$variation	=	$params->get( 'variation' );

jimport( 'cck.base.form.form' );
include JPATH_SITE.'/libraries/cck/base/form/form_inc.php';
JFactory::getSession()->set( 'cck_hash_'.$formId, JApplication::getHash( '0|'.$preconfig['type'].'|0|0' ) );

$raw_rendering		=	$params->get( 'raw_rendering', 0 );
$moduleclass_sfx	=	htmlspecialchars( $params->get( 'moduleclass_sfx' ) );
$class_sfx			=	( $params->get( 'force_moduleclass_sfx', 0 ) == 1 ) ? $moduleclass_sfx : '';
require JModuleHelper::getLayoutPath( 'mod_cck_form', $params->get( 'layout', 'default' ) );
?>