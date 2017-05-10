<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Field_Validation%class% extends JCckPluginValidation
{
	protected static $type	=	'%name%';
	protected static $regex	=	'/^[a-zA-Z]+$/';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

	// onCCK_Field_ValidationPrepareForm
	public static function onCCK_Field_ValidationPrepareForm( &$field, $fieldId, &$config )
	{
		if ( self::$type != $field->validation ) {
			return;
		}
		
		$validation	=	parent::g_onCCK_Field_ValidationPrepareForm( $field, $fieldId, $config, 'regex', self::$regex );
		
		$field->validate[]	=	'custom['.$validation->name.']';
	}
	
	// onCCK_Field_ValidationPrepareStore
	public static function onCCK_Field_ValidationPrepareStore( &$field, $name, $value, &$config )
	{
		parent::g_onCCK_Field_ValidationPrepareStore( $name, $value, $config, self::$type, 'regex', self::$regex );
	}
}
?>