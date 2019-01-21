<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: store_inc.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$app		=	JFactory::getApplication();
$author		=	null;
$client		=	$preconfig['client'];
$context	=	'';
$lang   	=	JFactory::getLanguage();
$post		=	JRequest::get( 'post' );
$session	=	JFactory::getSession();
$user		=	JFactory::getUser();
$unique		=	( $preconfig['unique'] ) ? $preconfig['unique'] : 'seblod_form';
$id			=	@(int)$post['id'];
$isNew		=	( $id > 0 ) ? 0 : 1;
$hash		=	JApplication::getHash( $id.'|'.$preconfig['type'].'|'.$preconfig['id'].'|'.$preconfig['copyfrom_id'] );
$hashed		=	$session->get( 'cck_hash_'.$unique );

if ( $id && $preconfig['id'] ) {
	$session->clear( 'cck_hash_'.$unique );
}
if ( $task == 'save2copy' ) {
	$id					=	0;
	$isNew				=	1;
	$preconfig['id']	=	0;
}
if ( !$user->authorise( 'core.admin' ) && $hashed !== null && ( $hash != $hashed ) ) {
	$config	=	array(
					'pk'=>0,
					'options'=>'',
					'url'=>@$preconfig['url'],
					'validate'=>''
				);

	$app->enqueueMessage( JText::_( 'COM_CCK_ERROR_DATA_INTEGRITY_CHECK_FAILED' ), 'error' );
	return 0;
}

// Type
$type		=	CCK_Form::getType( $preconfig['type'], 'store' );
if ( ! $type ) {
	$app->enqueueMessage( 'Oops! Content Type not found.. ; (', 'error' ); return;
}

$options	=	new JRegistry;
$options->loadString( $type->{'options_'.$client} );

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
require_once JPATH_PLUGINS.'/cck_field_validation/required/required.php';
$lang->load( 'plg_cck_field_validation_required', JPATH_ADMINISTRATOR, null, false, true );

JPluginHelper::importPlugin( 'cck_field' );
JPluginHelper::importPlugin( 'cck_field_restriction' );
JPluginHelper::importPlugin( 'cck_storage_location' );

if ( !$isNew ) {
	$author	=	JCckDatabase::loadObject( 'SELECT author_id AS id, author_session AS session FROM #__cck_core WHERE cck = "'.JCckDatabase::escape( $type->name ).'" AND pk = '.(int)$id );
}
$dispatcher	=	JEventDispatcher::getInstance();
$integrity	=	array();
$processing	=	array();
if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
	$processing =	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );
}
$storages	=	array();
$config		=	array( 'author'=>( is_object( $author ) ? $author->id : 0 ),
					   'author_session'=>( is_object( $author ) ? $author->session : '' ),
					   'client'=>$client,
					   'copyfrom_id'=>@(int)$preconfig['copyfrom_id'],
					   'doTranslation'=>JCck::getConfig_Param( 'language_jtext', 0 ),
					   'doValidation'=>(int)JCck::getConfig_Param( 'validation', '3' ),
					   'error'=>false,
					   'fields'=>array(),
					   'id'=>$preconfig['id'],
					   'isNew'=>$isNew,
					   'Itemid'=>$preconfig['itemId'],
					   'location'=>$type->storage_location,
					   'message'=>$preconfig['message'],
					   'message_style'=>'',
					   'options'=>'',
					   'pk'=>$id,
					   'post' => $post,
					   'process'=>array(),
					   'stage'=>-1,
					   'storages'=>array(),
					   'task'=>$task,
					   'tmp'=>array(),
					   'type'=>$preconfig['type'],
					   'type_id'=>(int)$type->id,
					   'url'=>$preconfig['url'],
					   'validate'=>''
					);
CCK_Form::applyTypeOptions( $config, $preconfig['client'] );

if ( $preconfig['client'] ) {
	if ( !isset( $config['options']['redirection'] ) ) {
		$config['options']['redirection']	=	'';
	}
}

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
	if ( ! ( $can['edit.own'] && $config['author'] == $user->id || $can['edit.own.content'] ) ) {
		CCK_Form::redirect( $cannot['action'], $cannot['redirect'], $cannot['message'], $cannot['style'], $config, $doDebug ); return;
	}
}
if ( $type->storage_location == 'joomla_user' && $config['isNew'] ) {
	if ( !( $user->id && !$user->guest ) && JComponentHelper::getParams( 'com_users' )->get( 'allowUserRegistration' ) == 0 ) {
		CCK_Form::redirect( $cannot['action'], $cannot['redirect'], $cannot['message'], $cannot['style'], $config, $doDebug ); return;
	}
}

// Fields
$stage		=	-1;
$stages		=	( isset( $config['options']['stages'] ) ) ? $config['options']['stages'] : 1;
if ( $stages > 1 ) {
	$stage	=	$preconfig['stage'];
}
$parent		=	JCckDatabase::loadObject( 'SELECT parent as name, parent_inherit as inherit FROM #__cck_core_types WHERE name = "'.JCckDatabase::escape( $preconfig['type'] ).'"' );
$target		=	$parent->inherit ? array( $preconfig['type'], $parent->name ) : $preconfig['type'];
$fields		=	CCK_Form::getFields( $target, $preconfig['client'], $stage, '', true );

// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare Context

if ( isset( $config['Itemid'] ) && $config['Itemid'] ) {
	$app->input->set( 'Itemid', $config['Itemid'] );
}
if ( isset( $preconfig['tmpl'] ) && $preconfig['tmpl'] != '' ) {
	$app->input->set( 'tmpl', $preconfig['tmpl'] );
}

$context	=	$session->get( 'cck_hash_'.$unique.'_context' );

if ( $context ) {
	$context	=	json_decode( $context, true );
	$excluded	=	array(
						'cid'=>'',
						'copyfrom_id'=>'',
						'id'=>'',
						'limit'=>'',
						'option'=>'',
						'pk'=>'',
						'return'=>'',
						'search'=>'',
						'skip'=>'',
						'stage'=>'',
						'task'=>'',
						'tid'=>'',
						'type'=>'',
						'view'=>''
					);
	foreach ( $context as $k=>$v ) {
		if ( isset( $excluded[$k] ) ) {
			continue;
		}
		$app->input->set( $k, $v );
	}
}

$session->clear( 'cck_hash_'.$unique.'_context' );

// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare Store

if ( count( $fields ) ) {
	foreach ( $fields as $field ) {
		$field->state	=	'';
		$toBeChecked	=	false;

		// Restriction
		if ( isset( $field->restriction ) && $field->restriction ) {
			$field->authorised	=	JCck::callFunc_Array( 'plgCCK_Field_Restriction'.$field->restriction, 'onCCK_Field_RestrictionPrepareStore', array( &$field, &$config ) );
			
			if ( !$field->authorised ) {
				continue;
			}
			/*
			else {
				$field->value	=	null;
			}
			*/
		}
		if ( $task != 'save2copy' && ( $field->variation == 'hidden' || $field->variation == 'hidden_anonymous' || $field->variation == 'hidden_auto' || $field->variation == 'hidden_isfilled' || $field->variation == 'disabled' || $field->variation == 'value' ) && !$field->live && $field->live_value != '' ) {
			$value	=	$field->live_value;
		} else {
			if ( isset( $post[$field->name] ) ) {
				$value			=	$post[$field->name];
			} else {
				$value			=	null;
				$field->state	=	'disabled';
			}
			if ( ( $field->variation == 'hidden_auto' || $field->variation == 'hidden_isfilled' ) && !$field->live && $session->has( 'cck_hash_live_'.$field->name ) ) {
				$toBeChecked	=	true;
			}
		}
		$dispatcher->trigger( 'onCCK_FieldPrepareStore', array( &$field, $value, &$config ) );

		if ( !$config['copyfrom_id'] ) {
			if ( !$id && $field->live && ( ( $field->variation == 'hidden' || $field->variation == 'hidden_anonymous' || $field->variation == 'disabled' || $field->variation == 'value' ) || ( ( $field->variation == 'hidden_auto' || $field->variation == 'hidden_isfilled' ) && $session->has( 'cck_hash_live_'.$field->name ) ) ) ) {
				$toBeChecked	=	true;
			}
		}
		if ( $toBeChecked && !in_array( $field->name, $config['options']['data_integrity_excluded'] ) ) {
			$hash		=	JApplication::getHash( $value );
			$hashed		=	$session->get( 'cck_hash_live_'.$field->name );
			$session->clear( 'cck_hash_live_'.$field->name );
			
			if ( $hash !=  $hashed ) {
				$config['validate'] =	'error';
				$integrity[]		=	$field->name;
			}
		}
		if ( $field->storages != '' ) {
			// More storages
			$storages	=	JCckDev::fromJSON( $field->storages, 'object' );
		}

		// Was it the last one?
		if ( $config['error'] == 2 ) {
			break;
		}
	}
}
if ( $config['error'] == 2 ) {
	$config['error']	=	false;
}

// Merge
if ( count( $config['fields'] ) ) {
	foreach ( $config['fields'] as $k=>$v ) {
		if ( $v->restriction != 'unset' ) {
			$fields[$k]	=	$v;
		}
	}
	$config['fields']	=	null;
	unset( $config['fields'] );
}

// Stage (and the next stage is...!)
if ( $stages > 1 ) {
	if ( $stage ) {
		if ( $config['task'] != 'apply' ) {
			if ( ! $config['validate'] ) {
				$stage++;
			}
		}
		if ( $stage <= $stages ) {
			if ( !( isset( $preconfig['skip'] ) && $preconfig['skip'] == '1' ) ) {
				$config['message']			=	'';
				$config['message_style']	=	0;
			}
			$config['url']		=	'';
			$config['stage']	=	$stage;
		} elseif ( $stage == $stages ) {
			$config['stage']	=	0;
		}
	} else {
		if ( $config['task'] == 'apply' ) {
			if ( !( isset( $preconfig['skip'] ) && $preconfig['skip'] == '1' ) ) {
				$config['message']			=	'';
				$config['message_style']	=	0;
			}
			$config['stage']	=	$stages;
			$config['url']		=	'';
		}
	}
}

// Validate
if ( $config['validate'] ) {
	if ( count( $integrity ) ) {
		$app->enqueueMessage( JText::sprintf( 'COM_CCK_ERROR_DATA_INTEGRITY_CHECK_FAILED_VALUES', implode( ', ', $integrity ) ), 'error' );
	}
	return 0;
}

// -------- -------- -------- -------- -------- -------- -------- -------- // Do Store

// BeforeStore
$event	=	'onCckPreBeforeStore';
if ( isset( $processing[$event] ) ) {
	foreach ( $processing[$event] as $p ) {
		if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
			$options	=	new JRegistry( $p->options );
			
			include_once JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config, $user */
		}
	}
}
if ( isset( $config['process']['beforeStore'] ) && count( $config['process']['beforeStore'] ) ) {
	foreach ( $config['process']['beforeStore'] as $process ) {
		if ( $process->type ) {
			JCck::callFunc_Array( 'plg'.$process->group.$process->type, 'on'.$process->group.'BeforeStore', array( $process->params, &$fields, &$config['storages'], &$config ) );
		}
	}
}

// Stop here if an error occurred
if ( $config['error'] !== false ) {
	return $config;
}

// Validate
if ( $config['validate'] ) {
	return 0;
}

$event	=	'onCckPostBeforeStore';
if ( isset( $processing[$event] ) ) {
	foreach ( $processing[$event] as $p ) {
		if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
			$options	=	new JRegistry( $p->options );

			include_once JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config, $user */
		}
	}
}

// Store
$k	=	0;
foreach ( $config['storages'] as $data ) {
	if ( isset( $data['_'] ) && $data['_'] && $data['_']->state !== true && $config['error'] !== true ) {
		$dispatcher->trigger( 'onCCK_Storage_LocationStore', array( $data['_']->location, $data, &$config, $id ) );
		$k++;
	}
}
if ( !$k ) {
	$config['pk']	=	-1;
}

// Stop here if an error occurred
if ( $config['error'] !== false ) {
	return $config;
}
if ( (int)$config['pk'] > 0 ) {
	JCckContent::reloadInstance( array( $config['location'], (int)$config['pk'] ) );
}

// AfterStore
$event	=	'onCckPreAfterStore';
if ( isset( $processing[$event] ) ) {
	foreach ( $processing[$event] as $p ) {
		if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
			$options	=	new JRegistry( $p->options );

			include_once JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config, $user */
		}
	}
}
if ( isset( $config['process']['afterStore'] ) && count( $config['process']['afterStore'] ) ) {
	foreach ( $config['process']['afterStore'] as $process ) {
		if ( $process->type ) {
			JCck::callFunc_Array( 'plg'.$process->group.$process->type, 'on'.$process->group.'AfterStore', array( $process->params, &$fields, &$config['storages'], &$config ) );
		}
	}
}
$event	=	'onCckPostAfterStore';
if ( isset( $processing[$event] ) ) {
	foreach ( $processing[$event] as $p ) {
		if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
			$options	=	new JRegistry( $p->options );

			include_once JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config, $user */
		}
	}
}
?>