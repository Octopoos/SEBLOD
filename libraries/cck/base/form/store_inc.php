<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: store_inc.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$app		=	JFactory::getApplication();
$lang   	=	JFactory::getLanguage();
$post		=	JRequest::get( 'post' );
$session	=	JFactory::getSession();
$user		=	JFactory::getUser();
$unique		=	( $preconfig['unique'] ) ? $preconfig['unique'] : 'seblod_form';
$id			=	(int)$post['id'];
$isNew		=	( $id > 0 ) ? 0 : 1;
$hash		=	JApplication::getHash( $id.'|'.$preconfig['type'].'|'.$preconfig['id'] );
$hashed		=	$session->get( 'cck_hash_'.$unique );
if ( $id && $preconfig['id'] ) {
	$session->clear( 'cck_hash_'.$unique );
}
if ( $task == 'save2copy' ) {
	$id					=	0;
	$isNew				=	1;
	$preconfig['id']	=	0;
}
if ( $app->isSite() && $hashed !== NULL && ( $hash != $hashed ) ) {
	$config	=	array(
					'pk'=>0,
					'options'=>'',
					'url'=>@$preconfig['url'],
					'validate'=>''
				);

	$app->enqueueMessage( JText::_( 'COM_CCK_ERROR_DATA_INTEGRITY_CHECK_FAILED' ), 'error' );
	return 0;
}
require_once JPATH_PLUGINS.'/cck_field_validation/required/required.php';
$lang->load( 'plg_cck_field_validation_required', JPATH_ADMINISTRATOR, null, false, true );

JPluginHelper::importPlugin( 'cck_field' );
JPluginHelper::importPlugin( 'cck_field_restriction' );
JPluginHelper::importPlugin( 'cck_storage_location' );
$dispatcher	=	JDispatcher::getInstance();
$integrity	=	array();
$processing	=	array();
if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
	$processing =	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );
}
$storages	=	array();
$config		=	array( 'author'=>0,
					   'client'=>$client,
					   'doTranslation'=>JCck::getConfig_Param( 'language_jtext', 0 ),
					   'doValidation'=>JCck::getConfig_Param( 'validation', '2' ),
					   'error'=>false,
					   'fields'=>array(),
					   'id'=>$preconfig['id'],
					   'isNew'=>$isNew,
					   'Itemid'=>$preconfig['itemId'],
					   'message'=>$preconfig['message'],
					   'message_style'=>'',
					   'options'=>'',
					   'pk'=>$id,
					   'post' => $post,
					   'process'=>array(),
					   'stage'=>-1,
					   'storages'=>array(),
					   'task'=>$task,
					   'type'=>$preconfig['type'],
					   'url'=>$preconfig['url'],
					   'validate'=>''
					);
CCK_Form::applyTypeOptions( $config );

$stage		=	-1;
$stages		=	( isset( $config['options']['stages'] ) ) ? $config['options']['stages'] : 1;
if ( $stages > 1 ) {
	$stage	=	$preconfig['stage'];
}
$parent		=	JCckDatabase::loadResult( 'SELECT parent FROM #__cck_core_types WHERE name = "'.$preconfig['type'].'"' );
$fields		=	CCK_Form::getFields( array( $preconfig['type'], $parent ), $client, $stage, '', true );

if ( count( $fields ) ) {
	foreach ( $fields as $field ) {
		$name			=	$field->name;
		$field->state	=	'';

		// Restriction
		if ( isset( $field->restriction ) && $field->restriction ) {
			$field->authorised	=	JCck::callFunc_Array( 'plgCCK_Field_Restriction'.$field->restriction, 'onCCK_Field_RestrictionPrepareStore', array( &$field, &$config ) );
			if ( !$field->authorised ) {
				continue;
			}
		}

		if ( $task != 'save2copy' && ( $field->variation == 'hidden' || $field->variation == 'hidden_auto' || $field->variation == 'disabled' || $field->variation == 'value' ) && ! $field->live && $field->live_value != '' ) {
			$value	=	$field->live_value;
		} else {
			if ( isset( $post[$name] ) ) {
				$value			=	$post[$name];
			} else {
				$value			=	NULL;
				$field->state	=	'disabled';
			}
		}
		$dispatcher->trigger( 'onCCK_FieldPrepareStore', array( &$field, $value, &$config ) );	
		if ( !$id && $field->live && ( $field->variation == 'hidden' || $field->variation == 'hidden_auto' || $field->variation == 'disabled' || $field->variation == 'value' ) ) {
			if ( !in_array( $field->name, $config['options']['data_integrity_excluded'] ) ) {
				$hash		=	JApplication::getHash( $value );
				$hashed		=	$session->get( 'cck_hash_live_'.$field->name );
				$session->clear( 'cck_hash_live_'.$field->name );
				if ( $hash !=  $hashed ) {
					$config['validate'] =	'error';
					$integrity[]		=	$field->name;
				}
			}
		}
		if ( $field->storages != '' ) {
			// More storages
			$storages	=	JCckDev::fromJSON( $field->storages, 'object' );
		}
	}
}

// Merge
if ( count( $config['fields'] ) ) {
	$fields				=	array_merge( $fields, $config['fields'] );	// Test: a loop may be faster.
	$config['fields']	=	NULL;
	unset( $config['fields'] );
}

// Stage (..and the next stage is..)
if ( $stages > 1 && $stage ) {
	if ( ! $config['validate'] ) {
		$stage++;
	}
	if ( $stage <= $stages ) {
		$config['message']			=	'';
		$config['message_style']	=	0;
		$config['stage']			=	$stage;
		// if ( !( isset( $preconfig['skip'] ) && $preconfig['skip'] == '1' ) ) {
		$config['url']				=	'';
		// }
	} elseif ( $stage == $stages ) {
		$config['stage']			=	0;
	}
}

// Validate
if ( $config['validate'] ) {
	if ( count( $integrity ) ) {
		$app->enqueueMessage( JText::sprintf( 'COM_CCK_ERROR_DATA_INTEGRITY_CHECK_FAILED_VALUES', implode( ', ', $integrity ) ), 'error' );
	}
	return 0;
}

// BeforeStore
if ( isset( $processing['onCckPreBeforeStore'] ) ) {
	foreach ( $processing['onCckPreBeforeStore'] as $p ) {
		if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
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
if ( isset( $processing['onCckPostBeforeStore'] ) ) {
	foreach ( $processing['onCckPostBeforeStore'] as $p ) {
		if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
			include_once JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config, $user */
		}
	}
}

// Validate
if ( $config['validate'] ) {
	return 0;
}

// Store
$k	=	0;
foreach ( $config['storages'] as $data ) {
	if ( $data['_'] && $data['_']->state !== true && $config['error'] !== true ) {
		$dispatcher->trigger( 'onCCK_Storage_LocationStore', array( $data['_']->location, $data, &$config, $id ) );
		$k++;
	}
}
if ( !$k ) {
	$config['pk']	=	69;
}

// AfterStore
if ( isset( $processing['onCckPreAfterStore'] ) ) {
	foreach ( $processing['onCckPreAfterStore'] as $p ) {
		if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
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
if ( isset( $processing['onCckPostAfterStore'] ) ) {
	foreach ( $processing['onCckPostAfterStore'] as $p ) {
		if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
			include_once JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config, $user */
		}
	}
}
?>