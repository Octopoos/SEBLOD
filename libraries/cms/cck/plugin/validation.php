<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: validation.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class JCckPluginValidation extends JPlugin
{
	protected static $construction	=	'cck_field_validation';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// g_onCCK_Field_ValidationPrepareForm
	public static function g_onCCK_Field_ValidationPrepareForm( &$field, $fieldId, &$config, $rule, $definition )
	{
		$validation	=	self::g_getValidation( $field->validation_options );
		
		if ( $validation->alert != '' ) {
			if ( is_array( $definition ) ) {
				$def			=	$definition['definition'];
			} else {
				$def			=	$definition;
			}
			$validation->name	=	$field->validation.'_'.$fieldId;
			$alert				=	$validation->alert;
			if ( $config['doTranslation'] ) {
				if ( trim( $alert ) ) {
					$alert	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $alert ) ) );
				}
			}
		} else {
			if ( is_array( $definition ) ) {
				$def				=	$definition['definition'];
				$validation->name	=	( isset( $definition['suffix'] ) && $definition['suffix'] ) ? $field->validation.'_'.$definition['suffix'] : $field->validation;
			} else {
				$def				=	$definition;
				$validation->name	=	$field->validation;
			}
			JFactory::getLanguage()->load( 'plg_cck_field_validation_'.$field->validation, JPATH_ADMINISTRATOR, null, false, true );
			$alert				=	JText::_( 'PLG_CCK_FIELD_VALIDATION_'.$field->validation.'_ALERT' );
		}
		
		$prefix	=	JCck::getConfig_Param( 'validation_prefix', '* ' );
		$rule	=	'
					"'.$validation->name.'":{
						"'.$rule.'": '.$def.',
						"alertText":"'.$prefix.$alert.'"}
					';
		
		$config['validation'][$validation->name]	=	$rule;
		
		return $validation;
	}
	
	// g_onCCK_Field_ValidationPrepareStore
	public static function g_onCCK_Field_ValidationPrepareStore( $name, $value, &$config, $type, $rule, $definition )
	{
		$app	=	JFactory::getApplication();
		$error	=	0;
		
		if ( $value == '' ) {
			return $error;
		}
		switch ( $rule ) {
			case 'regex':
				$regex		=	( is_array( $definition ) ) ? $definition['definition'] : $definition;
				if ( ! preg_match( $regex, $value ) ) {
					$error	=	1;
				}
				break;
			default:
				break;
		}
		
		if ( $error == 1 ) {
			JFactory::getLanguage()->load( 'plg_cck_field_validation_'.$type, JPATH_ADMINISTRATOR, null, false, true );
			$alert	=	JText::_( 'PLG_CCK_FIELD_VALIDATION_'.$type.'_ALERT' ) .' - '. $name;
			$app->enqueueMessage( $alert, 'error' );
			$config['validate']	=	'error';
		}
		
		return $error;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// g_addProcess
	public static function g_addProcess( $event, $type, &$config, $params, $priority = 3 )
	{
		if ( $event && $type ) {
			$process						=	new stdClass;
			$process->group					=	self::$construction;
			$process->type					=	$type;
			$process->params				=	$params;
			$process->priority				=	$priority;
			$config['process'][$event][]	=	$process;
		}
	}
	
	// g_getPath
	public static function g_getPath( $type = '' )
	{
		return JUri::root( true ).'/plugins/'.self::$construction.'/'.$type;
	}
	
	// g_getValidation
	public static function g_getValidation( $params, $legacy = true )
	{
		if ( ! $params ) {
			$validation			=	new stdClass;
			$validation->alert	=	'';
			
			return $validation;
		}
		
		$registry	=	new JRegistry;
		$registry->loadString( $params );

		if ( !$legacy ) {
			return $registry;
		}

		$validation	=	$registry->toObject();
		
		return $validation;
	}
}
?>