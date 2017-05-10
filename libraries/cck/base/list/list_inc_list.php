<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: list_inc_list.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Template
$idx			=	( isset( $config_list['idx'] ) ) ? $config_list['idx'] : '_';
$isInfinite		=	$config_list['infinite'];
$P				=	'template_'.$client;
$templateStyle	=	CCK_List::getTemplateStyle( $search->$P, array( 'rendering_css_core'=>$search->stylesheets ) );
if ( ! $templateStyle ) {
	$app->enqueueMessage( 'Oops! Template not found.. ; (', 'error' );
	return;
}

// Template Override
$tpl['home']	=	$app->getTemplate();
if ( file_exists( JPATH_SITE.'/templates/'.$tpl['home'].'/html/tpl_'.$templateStyle->name ) ) {
	$path		=	JPATH_SITE.'/templates/'.$tpl['home'].'/html/tpl_'.$templateStyle->name;
	$path_root	=	JPATH_SITE.'/templates/'.$tpl['home'].'/html';
	$tmpl		=	'tpl_'.$templateStyle->name;
} else {
	$path		=	JPATH_SITE.'/templates/'.$templateStyle->name;
	$path_root	=	JPATH_SITE.'/templates';
	$tmpl		=	$templateStyle->name;
}

// List
jimport( 'cck.rendering.document.document' );
$doc			=	CCK_Document::getInstance( 'html' );
$dispatcher		=	JEventDispatcher::getInstance();
$rparams		=	array( 'template' => $tmpl, 'file' => 'index.php', 'directory' => $path_root );

$go_for_item	=	0;
$go_for_both	=	0;
if ( isset( $templateStyle->params['cck_client_item'] ) ) {
	if ( $templateStyle->params['cck_client_item'] == '1' ) {
		$go_for_item	=	1;
	} elseif ( $templateStyle->params['cck_client_item'] == '2' ) {
		$go_for_both	=	1;
	}
}
if ( $go_for_item || $go_for_both ) {
	if ( isset( $app->cck_idx ) ) {
		$app->cck_idx[0]	=	true;
	}
	$client			=	'item';
	$fields			=	CCK_List::getFields_Items( $search->name, $client, $access );
	$target_f		=	'fields';
	$positions		=	array();
	$positions_p	=	CCK_List::getPositions( $search->id, $client );
	
	if ( $go_for_both ) {
		$fields2		=	CCK_List::getFields_Items( $search->name, 'list', $access );
		$positions2		=	array();
		$positions2_p	=	CCK_List::getPositions( $search->id, 'list' );
	}
	
	// Template
	$P					=	'template_item';
	$templateStyleItem	=	CCK_List::getTemplateStyle( $search->$P, array( 'rendering_css_core'=>$search->stylesheets ) );
	if ( ! $templateStyleItem ) {
		$app->enqueueMessage( 'Oops! Template not found.. ; (', 'error' );
		return;
	}
	
	// Template Override
	$tpl['home']	=	$app->getTemplate();
	if ( file_exists( JPATH_ADMINISTRATOR.'/templates/'.$tpl['home'].'/html/tpl_'.$templateStyleItem->name ) ) {
		$pathI		=	JPATH_ADMINISTRATOR.'/templates/'.$tpl['home'].'/html/tpl_'.$templateStyleItem->name;
		$path_rootI	=	JPATH_ADMINISTRATOR.'/templates/'.$tpl['home'].'/html';
		$tmplI		=	'tpl_'.$templateStyleItem->name;
	} else {
		$pathI		=	JPATH_SITE.'/templates/'.$templateStyleItem->name;
		$path_rootI	=	JPATH_SITE.'/templates';
		$tmplI		=	$templateStyleItem->name;
	}
	
	$docI			=	CCK_Document::getInstance( 'html' );
	$rparamsI		=	array( 'template' => $tmplI, 'file' => 'index.php', 'directory' => $path_rootI );
	
	include JPATH_SITE.'/libraries/cck/base/list/list_inc_list_items.php';
	
	$infos					=	array( 'context'=>'', 'params'=>$templateStyleItem->params, 'path'=>$pathI, 'root'=>JUri::root( true ), 'template'=>$templateStyleItem->name, 'theme'=>$tpl['home'] );
	$doc->i_infos			=	$infos;
	$doc->i_params			=	$rparamsI;
	$doc->i_positions		=	$positions;
	$doc->i_positions_more	=	$positions_p;
	
	if ( isset( $positions2 ) ) {
		$positions		=	$positions2;
		$positions_p	=	$positions2_p;
	}
} else {
	$fields			=	CCK_List::getFields_Items( $search->name, $client, $access );
	$target_f		=	'fields_list';
	$positions		=	array();
	$positions_p	=	CCK_List::getPositions( $search->id, $client );
	
	include JPATH_SITE.'/libraries/cck/base/list/list_inc_list_items.php';
}

// Finalize
$infos			=	array( 'context'=>'', 'infinite'=>$isInfinite, 'params'=>$templateStyle->params, 'path'=>$path, 'root'=>JUri::root( true ), 'template'=>$templateStyle->name, 'theme'=>$tpl['home'] );
$doc->finalize( 'content', $search->name, 'list', $positions, $positions_p, $infos );
$data			=	$doc->render( false, $rparams );
?>