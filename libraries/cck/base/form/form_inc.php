<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: form_inc.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JHtml::_( 'behavior.core' );

$app			=	JFactory::getApplication();
$copyfrom_id	=	0;
$data			=	'';
$id				=	0;

if ( $option == 'com_cck' && $view == 'form' ) {
	$copyfrom_id	=	$app->input->getInt( 'copyfrom_id', 0 );
	$id				=	$app->input->getInt( 'id', 0 );
	
	if ( $copyfrom_id > 0 ) {
		$id			=	$copyfrom_id;
	}
}
$client			=	$preconfig['client'];
$lang   		=	JFactory::getLanguage();
$stage			=	-1;
$user 			=	JCck::getUser();

// Type
$type			=	CCK_Form::getType( $preconfig['type'] );
if ( ! $type ) {
	$config		=	array(
						'action'=>$preconfig['action'],
						'core'=>true,
						'formId'=>$preconfig['formId'],
						'javascript'=>'',
						'submit'=>$preconfig['submit'],
						'type'=>'',
						'validation'=>array(),
						'validation_options'=>array()
					);
	$app->enqueueMessage( 'Oops! Content Type not found.. ; (', 'error' ); return;
}
$lang->load( 'pkg_app_cck_'.$type->folder_app, JPATH_SITE, null, false, false );

$options	=	new JRegistry;
$options->loadString( $type->{'options_'.$client} );

if ( $id > 0 ) {
	$isNew	=	0;
} else {
	$isNew	=	1;
}

if ( $type->admin_form && $app->isClient( 'site' ) && $user->authorise( 'core.admin.form', 'com_cck.form.'.$type->id ) ) {
	if ( $type->admin_form == 1 || ( $type->admin_form == 2 && !$isNew ) ) {
		$preconfig['client']	=	'admin';
		$more_options			=	$type->{'options_'.$preconfig['client']};

		if ( $more_options != '' ) {
			$more_options		=	json_decode( $more_options, true );
		}
		$options->loadArray( $more_options );
	}
}

$author			=	null;
$current		=	( $options->get( 'redirection' ) == 'current_full' ) ? JUri::getInstance()->toString() : JUri::current();
$doDebug		=	(int)JCck::getConfig_Param( 'debug', 0 );
$doDebug		=	( $doDebug == 1 || ( $doDebug == 2 && $user->authorise( 'core.admin' ) ) ) ? 1 : 0;
$stages			=	$options->get( 'stages', 1 );
$stage			=	-1;

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
if ( !$isNew ) {
	if ( !$copyfrom_id ) {
		$author	=	JCckDatabase::loadObject( 'SELECT author_id AS id, author_session AS session FROM #__cck_core WHERE cck = "'.JCckDatabase::escape( $type->name ).'" AND pk = '.(int)$id );
	}
}

$retry	=	$app->input->get( 'retry', '' );
$post	=	( $retry && $retry == $type->name ) ? JRequest::get( 'post' ) : array();
$config	=	array( 'action'=>$preconfig['action'],
				   'asset'=>'com_content',
				   'asset_id'=>0,
				   'author'=>( is_object( $author ) ? $author->id : 0 ),
				   'author_session'=>( is_object( $author ) ? $author->session : '' ),
				   'client'=>$client,
				   'context'=>array(),
				   'copyfrom_id'=>$copyfrom_id,
				   'core'=>true,
				   'custom'=>'',
				   'doTranslation'=>JCck::getConfig_Param( 'language_jtext', 0 ),
				   'doValidation'=>(int)JCck::getConfig_Param( 'validation', '3' ),
   				   'error'=>0,
				   'fields'=>array(),
				   'formId'=>$preconfig['formId'],
				   'isNew'=>$isNew,
				   'javascript'=>'',
				   'pk'=>$id,
   				   'submit'=>$preconfig['submit'],
				   'storages'=>array(),
				   'type'=>$type->name,
				   'type_id'=>$type->id,
				   'url'=>( ( @$preconfig['url'] ) ? $preconfig['url'] : $current ),
				   'validate'=>array(),
				   'validation'=>array(),
				   'validation_options'=>array()
				);

// ACL
$can	=	CCK_Form::getPermissions( $type, $config );
$cannot	=	CCK_Form::getNoAccessParams( $options );

if ( $can === false ) {	
	CCK_Form::redirect( $cannot['action'], $cannot['redirect'], $cannot['message'], $cannot['style'], $config, $doDebug ); return;
}
if ( $can['guest.edit'] ) {
	if ( !$can['edit.own'] ) {
		CCK_Form::redirect( $cannot['action'], $cannot['redirect'], $cannot['message'], $cannot['style'], $config, $doDebug ); return;
	}
} elseif ( ! $can['do'] ) {
	if ( $config['isNew'] ) {
		CCK_Form::redirect( $cannot['action'], $cannot['redirect'], $cannot['message'], $cannot['style'], $config, $doDebug ); return;
	}
	if ( ! ( $can['edit.own'] || $can['edit.own.content'] ) ) {
		CCK_Form::redirect( $cannot['action'], $cannot['redirect'], $cannot['message'], $cannot['style'], $config, $doDebug ); return;
	}
}
if ( $type->storage_location == 'joomla_user' && $config['isNew'] ) {
	if ( !( $user->id && !$user->guest ) && JComponentHelper::getParams( 'com_users' )->get( 'allowUserRegistration' ) == 0 ) {
		CCK_Form::redirect( $cannot['action'], $cannot['redirect'], $cannot['message'], $cannot['style'], $config, $doDebug ); return;
	}
}

// Fields
$target		=	$type->parent_inherit ? array( $type->name, $type->parent ) : $type->name;
$fields		=	CCK_Form::getFields( $target, $preconfig['client'], $stage, '', true, true );
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
if ( !isset( $lives ) ) {
	$live		=	explode( '||', $live );
	$lives		=	array();
	foreach ( $live as $liv ) {
		if ( $liv != '' ) {
			$l				=	explode( '=', $liv );
			$lives[$l[0]]	=	$l[1];
		}
	}
}
$variation	=	explode( '||', $variation );
$variations	=	array();
foreach ( $variation as $var ) {
	if ( $var != '' ) {
		$v					=	explode( '=', $var );
		if ( $v[1] == 'none' ) { $v[1] = 'hidden'; } /* TODO#SEBLOD: FIX TO REMOVE AFTER GA */
		$variations[$v[0]]	=	$v[1];
	}
}

// Positions
$positions		=	array();
$positions_w	=	'a.typeid = '.(int)$type->id;
if ( $type->parent_inherit && $type->parent != '' ) {
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
$dispatcher	=	JEventDispatcher::getInstance();

// Validation
if ( (int)JCck::getConfig_Param( 'validation', '3' ) > 1 ) {
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
		$field->variation_override	=	null;
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

					if ( !@$config['id'] && $config['base']->location ) {
						$config['id']	=	JCck::callFunc( 'plgCCK_Storage_Location'.$config['base']->location, 'getId', $config );
					}
				}
				if ( $can['guest.edit'] ) {
					// Do nothing as we already checked permissions.
				} elseif ( $config['author'] ) {
					// ACL
					if ( $can['edit.own'] && ! $can['do'] ) {
						if ( ( $user->id != $config['author'] ) && !$can['edit.own.content'] ) {
							CCK_Form::redirect( $cannot['action'], $cannot['redirect'], $cannot['message'], $cannot['style'], $config, $doDebug ); return;
						}
					} elseif ( ! $can['edit.own'] && $can['do'] ) {
						if ( $user->id == $config['author'] ) {
							CCK_Form::redirect( $cannot['action'], $cannot['redirect'], $cannot['message'], $cannot['style'], $config, $doDebug ); return;
						}
					}
				}
			}
			$dispatcher->trigger( 'onCCK_StoragePrepareForm', array( &$field, &$value, &$config['storages'][$Pt], &$config ) );
		} else {
			if ( $field->live ) {
				$dispatcher->trigger( 'onCCK_Field_LivePrepareForm', array( &$field, &$value, &$config ) );

				if ( !( $field->variation == 'hidden_auto' || $field->variation == 'hidden_isfilled' ) ) {
					JCckDevHelper::secureField( $field, $value );
				}
			} else {
				$value	=	( isset( $lives[$name] ) ) ? $lives[$name] : $field->live_value;
			}
		}
	}
	$field->value	=	$value;
	
	if ( $field->variation == 'hidden_isfilled' ) {
		if ( $value != '' ) {
			$field->variation	=	'hidden';

			if ( !$id ) {
				JCckDevHelper::secureField( $field, $value );
			}
		} else {
			$field->variation	=	'';
		}
	}
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
	foreach ( $config['fields'] as $k=>$v ) {
		if ( isset( $v->restriction ) &&  $v->restriction != 'unset' ) {
			$fields[$k]	=	$v;
		}
	}
	$config['fields']	=	null;
	unset( $config['fields'] );
}

// ACL
if ( $can['guest.edit'] ) {
	// Do nothing as we already checked permissions.
} elseif ( !$can['do'] && $can['edit.own'] && !$config['author'] ) {
	if ( empty( $config['id'] ) ) {
		$location			=	( $type->storage_location ) ? $type->storage_location : $config['base']['location'];
		$config['author']	=	JCckDatabase::loadResult( 'SELECT a.author_id FROM #__cck_core AS a WHERE a.storage_location = "'.$location.'" AND a.pk = '.(int)$config['pk'] );
	} else {
		$config['author']	=	JCckDatabase::loadResult( 'SELECT a.author_id FROM #__cck_core AS a WHERE a.id = '.(int)$config['id'] );
	}
	if ( ( !$config['author'] || ( $config['author'] != $user->id ) ) && !$can['edit.own.content'] ) {
		CCK_Form::redirect( $cannot['action'], $cannot['redirect'], $cannot['message'], $cannot['style'], $config, $doDebug ); return;
	}
}

// BeforeRender
if ( isset( $config['process']['beforeRenderForm'] ) && count( $config['process']['beforeRenderForm'] ) ) {
	JCckDevHelper::sortObjectsByProperty( $config['process']['beforeRenderForm'], 'priority' );
	
	foreach ( $config['process']['beforeRenderForm'] as $process ) {
		if ( $process->type ) {
			JCck::callFunc_Array( 'plg'.$process->group.$process->type, 'on'.$process->group.'BeforeRenderForm', array( $process->params, &$fields, &$config['storages'], &$config ) );
		}
	}
}

// Finalize
$doc->fields	=	&$fields;
$infos			=	array( 'context'=>'', 'params'=>$templateStyle->params, 'path'=>$path, 'root'=>JUri::root( true ), 'template'=>$templateStyle->name, 'theme'=>$tpl['home'] );
$doc->finalize( 'form', $type->name, $config['client'], $positions, $positions_more, $infos ); 

// Validation
$config['validation']			=	( count( $config['validation'] ) ) ? implode( ',', $config['validation'] ) : '';

if ( count( $config['validation_options'] ) ) {
	foreach ( $config['validation_options'] as $k=>$v ) {
		$options->set( $k, $v );
	}
}
$config['validation_options']	=&	$options;

if ( @$profiler ) {
	$doc->profiler_log	=	$profiler->mark( 'afterPrepareForm' );
	$doc->profiler		=	$profiler;
}
$data	=	$doc->render( false, $rparams );

if ( $copyfrom_id > 0 ) {
	$config['asset_id']	=	0;
	$config['author']	=	0;
	$config['custom']	=	'';
	$config['id']		=	0;
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
if ( $app->isClient( 'administrator' ) ) {
	if ( @$config['base']->location == 'joomla_article' ) { /* TODO#SEBLOD: getContext / params from any object */
		$object_params	=	JComponentHelper::getParams( 'com_content' );

		if ( $object_params->get( 'save_history', 0 ) ) {
			JToolbarHelper::versions( 'com_content.article', $config['pk'] );
		}
	}
}
?>