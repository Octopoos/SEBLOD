<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: form_inc.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( !JCck::on( '3.4' ) ) {
	JHtml::_( 'behavior.framework' );
} else {
	JHtml::_( 'behavior.core' );
}

$app			=	JFactory::getApplication();
$data			=	'';
$id				=	0;
$translate_id	=	0;
if ( $option == 'com_cck' && $view == 'form' ) {
	$id				=	$app->input->getInt( 'id', 0 );
	$translate_id	=	$app->input->getInt( 'translate_id', 0 );
	if ( $translate_id > 0 ) {
		$id			=	$translate_id;
	}
}
$lang   		=	JFactory::getLanguage();
$stage			=	-1;
$user 			=	JCck::getUser();

// Type
$type			=	CCK_Form::getType( $preconfig['type'], $id );
if ( ! $type ) {
	$config		=	array( 'action'=>$preconfig['action'], 'core'=>true, 'formId'=>$preconfig['formId'], 'javascript'=>'', 'submit'=>$preconfig['submit'], 'validation'=>array(), 'validation_options'=>array() );
	$app->enqueueMessage( 'Oops! Content Type not found.. ; (', 'error' ); return;
}
$lang->load( 'pkg_app_cck_'.$type->folder_app, JPATH_SITE, null, false, false );

$options	=	new JRegistry;
$options->loadString( $type->{'options_'.$preconfig['client']} );

$current		=	( $options->get( 'redirection' ) == 'current_full' ) ? JFactory::getURI()->toString() : JURI::current();
$no_message		=	$options->get( 'message_no_access' );
$no_redirect	=	$options->get( 'redirection_url_no_access', 'index.php?option=com_users&view=login' );
$no_style		=	$options->get( 'message_style_no_access', 'error' );
$no_action		=	$options->get( 'action_no_access' );
$stages			=	$options->get( 'stages', 1 );
$stage			=	-1;

if ( $id > 0 ) {
	$isNew				=	0;
	$canAccess			=	$user->authorise( 'core.edit', 'com_cck.form.'.$type->id );
	//if ( $user->id && !$user->guest ) {
		$canEditOwn		=	$user->authorise( 'core.edit.own', 'com_cck.form.'.$type->id );
	//} else {
	//	$canEditOwn		=	false; // todo: guest
	//}
	
	// canEditOwnContent
	jimport( 'cck.joomla.access.access' );
	$canEditOwnContent	=	CCKAccess::check( $user->get( 'id' ), 'core.edit.own.content', 'com_cck.form.'.$type->id );
	if ( $canEditOwnContent ) {
		$remote_field		=	JCckDatabase::loadObject( 'SELECT storage, storage_table, storage_field FROM #__cck_core_fields WHERE name = "'.$canEditOwnContent.'"' );
		$canEditOwnContent	=	false;
		if ( is_object( $remote_field ) && $remote_field->storage == 'standard' ) {
			$related_content_id		=	JCckDatabase::loadResult( 'SELECT '.$remote_field->storage_field.' FROM '.$remote_field->storage_table.' WHERE id = '.(int)$id );
			$related_content		=	JCckDatabase::loadObject( 'SELECT author_id, pk FROM #__cck_core WHERE storage_location = "joomla_article" AND pk = '.$related_content_id );

			if ( $related_content->author_id == $user->get( 'id' ) ) {
				$canEditOwnContent	=	true;
			}
		}
	}
} else {
	$isNew				=	1;
	if ( $type->location && (( $app->isAdmin() && $type->location != 'admin' ) || ( $app->isSite() && $type->location != 'site' )) ) {
		CCK_Form::redirect( $no_action, $no_redirect, $no_message, $no_style, $config ); return;
	}
	$actionACL			=	'create';
	$canAccess			=	$user->authorise( 'core.create', 'com_cck.form.'.$type->id );
	$canEditOwn			=	false;
	$canEditOwnContent	=	false;
}
if ( $stages > 1 ) {
	if ( $isNew ) {
		$stage		=	1;
	} else {
		$stage		=	$app->input->getUInt( 'stage', 1 );
	}
	if ( $stage == $stages ) {
		$stage		=	0;
	}
}
$retry	=	$app->input->get( 'retry', '' );
$post	=	( $retry && $retry == $type->name ) ? JRequest::get( 'post' ) : array();
$config	=	array( 'action'=>$preconfig['action'],
				   'asset'=>'com_content',
				   'asset_id'=>0,
				   'author'=>0,
				   'client'=>$preconfig['client'],
				   'core'=>true,
				   'custom'=>'',
				   'doTranslation'=>JCck::getConfig_Param( 'language_jtext', 0 ),
				   'doValidation'=>JCck::getConfig_Param( 'validation', '2' ),
   				   'error'=>0,
				   'fields'=>array(),
				   'formId'=>$preconfig['formId'],
				   'isNew'=>$isNew,
				   'javascript'=>'',
				   'pk'=>$id,
   				   'submit'=>$preconfig['submit'],
				   'storages'=>array(),
				   'translate_id'=>$translate_id,
				   'type'=>$type->name,
				   'type_id'=>$type->id,
				   'url'=>( ( @$preconfig['url'] ) ? $preconfig['url'] : $current ),
				   'validate'=>array(),
				   'validation'=>array(),
				   'validation_options'=>array()
				);

// ACL
if ( ! $canAccess ) {
	if ( $isNew ) {
		CCK_Form::redirect( $no_action, $no_redirect, $no_message, $no_style, $config ); return;
	}
	if ( ! ( $canEditOwn || $canEditOwnContent ) ) {
		CCK_Form::redirect( $no_action, $no_redirect, $no_message, $no_style, $config ); return;
	}
}

// Fields
$fields		=	CCK_Form::getFields( array( $type->name, $type->parent ), $preconfig['client'], $stage, '', true, true );
if ( ! count( $fields ) ) {
	$app->enqueueMessage( 'Oops! Fields not found.. ; (', 'error' ); return;
}

// Template
$P				=	'template_'.$preconfig['client'];
$templateStyle	=	CCK_Form::getTemplateStyle( $type->$P, array( 'rendering_css_core'=>$type->stylesheets )  );
if ( ! $templateStyle ) {
	$app->enqueueMessage( 'Oops! Template not found.. ; (', 'error' ); return;
}

// Template Override
$tpl['home']	=	$app->getTemplate();
if ( file_exists( JPATH_ADMINISTRATOR.'/templates/'.$tpl['home'].'/html/tpl_'.$templateStyle->name ) ) {
	$path		=	JPATH_ADMINISTRATOR.'/templates/'.$tpl['home'].'/html/tpl_'.$templateStyle->name;
	$path_root	=	JPATH_ADMINISTRATOR.'/templates/'.$tpl['home'].'/html';
	$tmpl		=	'tpl_'.$templateStyle->name;
} else {
	$path		=	JPATH_SITE.'/templates/'.$templateStyle->name;
	$path_root	=	JPATH_SITE.'/templates';
	$tmpl		=	$templateStyle->name;
}

// INIT
$live		=	explode( '||', $live );
$lives		=	array();
foreach ( $live as $liv ) {
	if ( $liv != '' ) {
		$l				=	explode( '=', $liv );
		$lives[$l[0]]	=	$l[1];
	}
}
$variation	=	explode( '||', $variation );
$variations	=	array();
foreach ( $variation as $var ) {
	if ( $var != '' ) {
		$v					=	explode( '=', $var );
		if ( $v[1] == 'none' ) { $v[1] = 'hidden'; } // TODO: FIX TO REMOVE AFTER GA
		$variations[$v[0]]	=	$v[1];
	}
}

// Positions
$positions		=	array();
$positions_w	=	'a.typeid = '.(int)$type->id;
if ( $type->parent != '' ) {
	$parent_id		=	(int)JCckDatabase::loadResult( 'SELECT id FROM #__cck_core_types WHERE name = "'.$type->parent.'"' );
	if ( $parent_id ) {
		$positions_w	=	'('.$positions_w.' OR a.typeid = '.$parent_id.')';
	}
}
$positions_more	=	JCckDatabase::loadObjectList( 'SELECT * FROM #__cck_core_type_position AS a WHERE '.$positions_w.' AND a.client ="'.(string)$preconfig['client'].'"', 'position' );

// Begin Doc
jimport( 'cck.rendering.document.document' );
$doc		=	CCK_Document::getInstance( 'html' );
$rparams	=	array( 'template' => $tmpl, 'file' => 'index.php', 'directory' => $path_root );

JPluginHelper::importPlugin( 'cck_field' );
JPluginHelper::importPlugin( 'cck_field_live' );
JPluginHelper::importPlugin( 'cck_field_restriction' );
if ( $id ) {
	JPluginHelper::importPlugin( 'cck_storage' );
	JPluginHelper::importPlugin( 'cck_storage_location' );
}
$dispatcher	=	JDispatcher::getInstance();
$session	=	JFactory::getSession();

// Validation
if ( JCck::getConfig_Param( 'validation', 2 ) > 1 ) {
	$lang->load( 'plg_cck_field_validation_required', JPATH_ADMINISTRATOR, null, false, true );
	require_once JPATH_PLUGINS.'/cck_field_validation/required/required.php';
}

// Process
foreach ( $fields as $field ) {
	$name	=	$field->name;
	$value	=	'';
	
	// Variation
	if ( $field->variation_override ) {
		$override	=	json_decode( $field->variation_override, true );
		if ( count( $override ) ) {
			foreach ( $override as $k=>$v ) {
				$field->$k	=	$v;
			}
		}
		$field->variation_override	=	NULL;
	}
	$field->variation	=	( isset( $variations[$name] ) ) ? ( $variations[$name] == 'form' ? '' : $variations[$name] ) : $field->variation;
	
	// Prepare
	if ( $retry == $type->name ) {
		$value	=	( isset( $post[$name] ) ) ? $post[$name] : '';
	} else {
		if ( $id ) {
			$Pt	=	$field->storage_table;
			if ( $Pt && ! isset( $config['storages'][$Pt] ) ) {
				$config['storages'][$Pt]	=	'';
				$dispatcher->trigger( 'onCCK_Storage_LocationPrepareForm', array( &$field, &$config['storages'][$Pt], $id, &$config ) );
				if ( !isset( $config['base'] ) ) {
					$config['base']				=	new stdClass;
					$config['base']->location	=	$field->storage_location;
					$config['base']->table		=	$Pt;
				}
				if ( $config['author'] ) {
					// ACL
					if ( $canEditOwn && ! $canAccess ) {
						if ( ( $user->get( 'id' ) != $config['author'] ) && !$canEditOwnContent ) {
							CCK_Form::redirect( $no_action, $no_redirect, $no_message, $no_style, $config ); return;
						}
					} elseif ( ! $canEditOwn && $canAccess ) {
						if ( $user->get( 'id' ) == $config['author'] ) {
							CCK_Form::redirect( $no_action, $no_redirect, $no_message, $no_style, $config ); return;
						}
					}
				}
			}
			$dispatcher->trigger( 'onCCK_StoragePrepareForm', array( &$field, &$value, &$config['storages'][$Pt], &$config ) );
		} else {
			if ( $field->live ) {
				$dispatcher->trigger( 'onCCK_Field_LivePrepareForm', array( &$field, &$value, &$config ) );
				$hash		=	JApplication::getHash( $value );
				$session->set( 'cck_hash_live_'.$field->name, $hash );
			} else {
				$value	=	( isset( $lives[$name] ) ) ? $lives[$name] : $field->live_value;
			}
		}
	}
	$field->value	=	$value;
	$dispatcher->trigger( 'onCCK_FieldPrepareForm', array( &$field, $value, &$config, array() ) );
	
	$position				=	$field->position;
	$positions[$position][]	=	$field->name;

	// Was it the last one?
	if ( $config['error'] ) {
		break;
	}
}

// Merge
if ( count( $config['fields'] ) ) {
	$fields				=	array_merge( $fields, $config['fields'] );	// Test: a loop may be faster.
	$config['fields']	=	NULL;
	unset( $config['fields'] );
}

// ACL
if ( !$canAccess && $canEditOwn && !$config['author'] ) {
	if ( empty( $config['id'] ) ) {
		$location			=	( $type->storage_location ) ? $type->storage_location : $config['base']['location'];
		$config['author']	=	JCckDatabase::loadResult( 'SELECT a.author_id FROM #__cck_core AS a WHERE a.storage_location = "'.$location.'" AND a.pk = '.(int)$config['pk'] );
	} else {
		$config['author']	=	JCckDatabase::loadResult( 'SELECT a.author_id FROM #__cck_core AS a WHERE a.id = '.(int)$config['id'] );
	}
	if ( ( !$config['author'] || ( $config['author'] != $user->get( 'id' ) ) ) && !$canEditOwnContent ) {
		CCK_Form::redirect( $no_action, $no_redirect, $no_message, $no_style, $config ); return;
	}
}

// BeforeRender
if ( isset( $config['process']['beforeRenderForm'] ) && count( $config['process']['beforeRenderForm'] ) ) {
	foreach ( $config['process']['beforeRenderForm'] as $process ) {
		if ( $process->type ) {
			JCck::callFunc_Array( 'plg'.$process->group.$process->type, 'on'.$process->group.'beforeRenderForm', array( $process->params, &$fields, &$config['storages'], &$config ) );
		}
	}
}

// Finalize
$doc->fields	=	&$fields;
$infos			=	array( 'context'=>'', 'params'=>$templateStyle->params, 'path'=>$path, 'root'=>JURI::root( true ), 'template'=>$templateStyle->name, 'theme'=>$tpl['home'] );
$doc->finalize( 'form', $type->name, $config['client'], $positions, $positions_more, $infos ); 

// Validation
$config['validation']			=	( count( $config['validation'] ) ) ? implode( ',', $config['validation'] ) : '';
$config['validation_options']	=&	$options;

if ( @$profiler ) {
	$doc->profiler_log	=	$profiler->mark( 'afterPrepareForm' );
	$doc->profiler		=	$profiler;
}
$data	=	$doc->render( false, $rparams );

if ( $translate_id > 0 ) {
	$config['asset_id']	=	0;
	$config['author']	=	0;
	$config['custom']	=	'';
	$config['pk']		=	0;
	$config['storages']	=	array();
	$id					=	0;
	unset( $config['base'] );
}
if ( $config['pk'] && empty( $config['id'] ) ) {
	if ( ! ( isset( $config['base'] ) && is_object( $config['base'] ) ) ) {
		$config['base']				=	new stdClass;
		$config['base']->location	=	'';
		$config['base']->table		=	'';
	}
	if ( !( isset( $config['base']->location ) && $config['base']->location ) ) {
		$config['base']->location	=	$type->storage_location;
		if ( $config['base']->location ) {
			$properties				=	array( 'table' );
			$properties				=	JCck::callFunc( 'plgCCK_Storage_Location'.$config['base']->location, 'getStaticProperties', $properties );
			$config['base']->table	=	$properties['table'];
		}
	}
	$config['id']	=	JCck::callFunc( 'plgCCK_Storage_Location'.$config['base']->location, 'getId', $config );	
} else {
	$config['id']	=	( @$config['id'] ) ? $config['id'] : 0;
}

// Versions
if ( $app->isAdmin() && JCck::on( '3.2' ) && @$config['base']->location == 'joomla_article' ) {
	JToolbarHelper::versions( 'com_content.article', $config['pk'] );
}
?>