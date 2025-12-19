<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;

// Plugin
class plgCCK_Field_ValidationDate extends JCckPluginValidation
{
	protected static $type		=	'date';
	protected static $regexs	=	array( 'international'=>'\d{4}[-](0?[1-9]|1[012])[-](0?[1-9]|[12][0-9]|3[01])',
										   'en'=>'([0]?[1-9]{1}|[12]\d{1}|3[01])[\/\-\.]([0]?[1-9]|1[0-2])[\/\-\.]\d{4}',
										   'fr'=>'([0]?[1-9]{1}|[12]\d{1}|3[01])[\/\-\.]([0]?[1-9]|1[0-2])[\/\-\.]\d{4}',
										   'us'=>'(0?[1-9]|1[012])[\/\-\.](0?[1-9]|[12][0-9]|3[01])[\/\-\.]\d{4}'
									);
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_ValidationPrepareForm
	public static function onCCK_Field_ValidationPrepareForm( &$field, $fieldId, &$config )
	{
		if ( self::$type != $field->validation ) {
			return;
		}
		
		$definition	=	self::_getDefinition( $field );

		if ( !is_array( $definition ) ) {
			return;
		}
		$validation			=	parent::g_onCCK_Field_ValidationPrepareForm( $field, $fieldId, $config, 'regex', $definition );
		$field->validate[]	=	'custom['.$validation->name.']';

		// Date Range
		if ( !( isset( $validation->range ) && $validation->range != '' ) ) {
			return;
		}
		if ( empty( $validation->range_fieldname ) ) {
			return;
		}
		if ( $validation->range_alert != '' ) {
			$name	=	$validation->range.'_'.$fieldId;
			$alert	=	$validation->range_alert;
			if ( $config['doTranslation'] ) {
				if ( trim( $alert ) ) {
					$alert	=	Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $alert ) ) );
				}
			}
			$prefix	=	JCck::getConfig_Param( 'validation_prefix', '* ' );
			
			$rule	=	'
					"'.$name.'":{
						"regex": "#'.$validation->range_fieldname.'",
						"alertText":"'.$prefix.$alert.'"}
					';
			
			$config['validation'][$name]	=	$rule;
			$field->validate[]				=	'isFuture['.$name.']';
		} else {
			/* TODO#SEBLOD: */
		}
	}
	
	// onCCK_Field_ValidationPrepareStore
	public static function onCCK_Field_ValidationPrepareStore( &$field, $name, $value, &$config )
	{
		$definition	=	self::_getDefinition( $field );
		parent::g_onCCK_Field_ValidationPrepareStore( $name, $value, $config, self::$type, 'regex', $definition );
	}
	
	// _getDefinition
	protected static function _getDefinition( $field )
	{
		$options	=	parent::g_getValidation( $field->validation_options );

		if ( isset( $options->region ) && $options->region ) {
			$region	=	$options->region;
			$regex	=	self::$regexs[$region];
		} else {
			$region	=	'auto';
			$regex	=	Text::_( 'COM_CCK_DATE_FORMAT_AUTO_REGEX' );
		}
		
		if ( isset( $options->separator ) && $options->separator && $region != 'international' ) {
			$regex	=	str_replace( '[\/\-\.]', '\\'.$options->separator, $regex );
		}

		if ( isset( $options->time ) && $options->time ) {
			$regex	.=	' ((0|1)[0-9]|2[0-3]):((0|1|2|3|4|5)[0-9])';

			if ( (int)$options->time > 0 ) {
				$regex	.=	':((0|1|2|3|4|5)[0-9])';
			}
		}
		$regex	=	'/^'.$regex.'$/';
		
		return array( 'definition'=>$regex, 'suffix'=>$region );
	}
}
?>