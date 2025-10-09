<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: list_inc_list.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
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

$go_for_item	=	0;
$go_for_both	=	0;
$rparams		=	array( 'template' => $tmpl, 'file' => 'index.php', 'directory' => $path_root );
$validation		=	'';

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

$config	=	array(
				'client'=>'list',
				'doTranslation'=>$config_list['doTranslation'],
				'ids'=>( isset( $config['ids'] ) ? $config['ids'] : '' ),
				'pks'=>( isset( $config['pks'] ) ? $config['pks'] : '' ),
				'total'=>$config_list['total']
			);

foreach ( $fields as $field ) {
	if ( $field->position == '_above_' || $field->position == '_below_' ) {
		if ( !$field->name ) {
			continue;
		}
		
		$fieldName			=	$field->name;
		$field->typo_target	=	'value';
		$value				=	'';
		
		if ( $field->variation_override ) {
			$override	=	json_decode( $field->variation_override, true );

			if ( count( $override ) ) {
				foreach ( $override as $k=>$v ) {
					$field->$k	=	$v;
				}
			}

			$field->variation_override	=	null;
		}

		$Pt				=	( $field->storage_table != '' ) ? $field->storage_table : '_';

		/*
		if ( $Pt && ! isset( $config['storages'][$Pt] ) ) {
			if ( ! isset( $storages[$Pt] ) ) {
				$storages[$Pt]					=	'';
				if ( !$list['isCore'] || $Pt == '_' ) {
					$config['storages'][$Pt]	=	$items[$i];
				} else {
					$app->triggerEvent( 'onCCK_Storage_LocationPrepareItems', array( &$field, &$storages, $config['pks'], &$config, true ) );
					$config['storages'][$Pt]				=	isset( $storages[$Pt][$config['pk']] ) ? $storages[$Pt][$config['pk']] : null;
					if ( $storages['_'] && !isset( $config['storages'][$storages['_']] ) ) {
						$config['storages'][$storages['_']]	=	$storages[$storages['_']][$config['pk']];
					}
				}
			} else {
				if ( !$list['isCore'] || $Pt == '_' ) {
					$config['storages'][$Pt]	=	$items[$i];						
				} else {
					$app->triggerEvent( 'onCCK_Storage_LocationPrepareItems', array( &$field, &$storages, $config['pks'], &$config, false ) );
					$config['storages'][$Pt]				=	isset( $storages[$Pt][$config['pk']] ) ? $storages[$Pt][$config['pk']] : null;
					if ( $storages['_'] && !isset( $config['storages'][$storages['_']] ) ) {
						$config['storages'][$storages['_']]	=	$storages[$storages['_']][$config['pk']];
					}
				}
			}
		}

		$app->triggerEvent( 'onCCK_StoragePrepareContent', array( &$field, &$value, &$config['storages'][$Pt] ) );
		*/
		
		if ( is_string( $value ) ) {
			$storage_mode	=	(int)$field->storage_mode;
			$value			=	trim( $value );

			if ( $storage_mode && $value != '' ) {
				if ( $storage_mode == -1 ) {
					$json		=	json_decode( $value );
					$value		=	isset( $json->$lang_default ) ? $json->$lang_default : '';
				} else {
					$json		=	json_decode( $value );
					$value		=	isset( $json->$lang_tag ) ? $json->$lang_tag : '';
				}
			}
		}
		
		$hasLink	=	( $field->link != '' ) ? 1 : 0;
		$app->triggerEvent( 'onCCK_FieldPrepareContent'.$suffix, array( &$field, $value, &$config ) );
		$target		=	$field->typo_target;
		if ( $hasLink ) {
			$app->triggerEvent( 'onCCK_Field_LinkPrepareContent', array( &$field, &$config ) );
			if ( $field->link ) {
				JCckPluginLink::g_setHtml( $field, $target );
			}
		}
		if ( @$field->typo && ( $field->$target !== '' || $field->typo_label == -2 ) ) {
			$app->triggerEvent( 'onCCK_Field_TypoPrepareContent', array( &$field, $field->typo_target, &$config ) );
		} else {
			$field->typo	=	'';
		}

		// Set Field
		$doc->fields[$fieldName]	=	$field;
		
		//Set Position
		$pos						=	$field->position;
		$positions[$pos][]			=	$fieldName;
	}
}

// Finalize
$infos				=	array( 'context'=>'', 'infinite'=>$isInfinite, 'params'=>$templateStyle->params, 'path'=>$path, 'root'=>JUri::root( true ), 'template'=>$templateStyle->name, 'theme'=>$tpl['home'] );
$doc->finalize( 'content', $search->name, 'list', $positions, $positions_p, $infos );
$data['buffer']		=	$doc->render( false, $rparams );
?>