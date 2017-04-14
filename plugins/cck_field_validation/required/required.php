<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Field_ValidationRequired extends JCckPluginValidation
{
	protected static $type	=	'required';
	protected static $regex	=	'"none"';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

	// onCCK_Field_ValidationPrepareForm
	public static function onCCK_Field_ValidationPrepareForm( &$field, $fieldId, &$config )
	{
		if ( self::$type != $field->required ) {
			if ( strpos( $field->required, self::$type.'[' ) !== false ) {
				$name		=	'groupRequired';
				$fieldId	=	explode( '[', $field->required );
				$fieldId	=	substr( $fieldId[1], 0, -1 );
				$required	=	'groupRequired['.$fieldId.']';
				$required2	=	'groupRequire';
			} else {
				return;	
			}
		} else {
			$name		=	$field->required;
			$required	=	$field->required;
			$required2	=	'require';
		}

		if ( $field->required_alert != '' ) {
			$name	=	$name.'_'.$fieldId;

			$alert	=	$field->required_alert;
			if ( $config['doTranslation'] ) {
				if ( trim( $alert ) ) {
					$alert	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $alert ) ) );
				}
			}
			$prefix	=	JCck::getConfig_Param( 'validation_prefix', '* ' );
					
			$rule	=	'
					"'.$name.'":{
						"regex": '.self::$regex.',
						"alertText":"'.$prefix.$alert.'"}
					';
			
			$config['validation'][$name]	=	$rule;
			$field->validate[]				=	$required2.'['.$name.']';
		} else {
			$alert	=	JText::_( 'PLG_CCK_FIELD_VALIDATION_'.$name.'_ALERT' );
			$alert2	=	JText::_( 'PLG_CCK_FIELD_VALIDATION_'.self::$type.'_ALERT2' );
			$alert3	=	JText::_( 'PLG_CCK_FIELD_VALIDATION_'.self::$type.'_ALERT3' );
			$prefix	=	JCck::getConfig_Param( 'validation_prefix', '* ' );
			
			$rule	=	'			
					"'.$name.'":{
						"regex":'.self::$regex.',
						"alertText":"'.$prefix.$alert.'",
						"alertTextCheckboxe":"'.$prefix.$alert2.'",
						"alertTextCheckboxMultiple":"'.$prefix.$alert3.'"}
						';
			
			$config['validation'][$name]	=	$rule;
			$field->validate[]				=	$required;
		}
	}
	
	// onCCK_Field_ValidationPrepareStore
	public static function onCCK_Field_ValidationPrepareStore( &$field, $name, $value, &$config )
	{
		if ( $value != '' || $field->state == 'disabled' || strpos( $field->required, '[' ) !== false ) {
			// OK
		} else {
			$app	=	JFactory::getApplication();
			//$lang =	JFactory::getLanguage();
			//$lang->load( 'plg_cck_field_validation_'.self::$type, JPATH_ADMINISTRATOR, null, false, true );
			
			$alert	=	JText::_( 'PLG_CCK_FIELD_VALIDATION_'.self::$type.'_ALERT' ) .' - '. $name ;
			
			$app->enqueueMessage( $alert, 'error' );
			$config['validate']	=	'error';
		}
	}
}
?>