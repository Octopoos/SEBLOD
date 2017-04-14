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

jimport( 'cck.construction.field.generic_app' );

// Class
class plgCCK_FieldButton_Free_App extends plgCCK_FieldGeneric_App
{	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Export

	// onCCK_FieldExportField
	public static function onCCK_FieldExportField( &$field, &$data, &$extensions )
	{
		$options2	=	JCckDev::fromJSON( $field->options2 );

		if ( isset( $options2['button_link'] ) && $options2['button_link'] ) {
			CCK_Export::exportPlugin( 'cck_field_link', $options2['button_link'], $data, $extensions );
		}
	}
	
	// onCCK_FieldExportType_Field
	public static function onCCK_FieldExportType_Field( $field, &$field_join, &$data, &$extensions )
	{
	}
	
	// onCCK_FieldExportSearch_Field
	public static function onCCK_FieldExportSearch_Field( $field, &$field_join, &$data, &$extensions )
	{
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Import
	
	// onCCK_FieldImportField
	public static function onCCK_FieldImportField( &$field, $data )
	{
	}
	
	// onCCK_FieldImportType_Field
	public static function onCCK_FieldImportType_Field( $field, &$xml, $data )
	{
	}
	
	// onCCK_FieldImportSearch_Field
	public static function onCCK_FieldImportSearch_Field( $field, &$xml, $data )
	{
	}
}
?>