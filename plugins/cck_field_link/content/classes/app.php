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
class plgCCK_Field_LinkContent_App extends plgCCK_FieldGeneric_App
{	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Export

	// onCCK_FieldExportField
	public static function onCCK_Field_LinkExportField( &$field, &$data, &$extensions )
	{
	}
	
	// onCCK_FieldExportType_Field
	public static function onCCK_Field_LinkExportType_Field( $field, &$field_join, &$data, &$extensions )
	{
		self::_updateExportItemId( $field, $field_join, $data );
	}
	
	// onCCK_FieldExportSearch_Field
	public static function onCCK_Field_LinkExportSearch_Field( $field, &$field_join, &$data, &$extensions )
	{
		self::_updateExportItemId( $field, $field_join, $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Import
	
	// onCCK_FieldImportField
	public static function onCCK_Field_LinkImportField( &$field, $data )
	{
	}
	
	// onCCK_FieldImportType_Field
	public static function onCCK_Field_LinkImportType_Field( $field, &$field_join, $data )
	{
		self::_updateImportItemId( $field, $field_join, $data );
	}
	
	// onCCK_FieldImportSearch_Field
	public static function onCCK_Field_LinkImportSearch_Field( $field, &$field_join, $data )
	{
		self::_updateImportItemId( $field, $field_join, $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// _updateExportItemId
	protected static function _updateExportItemId( $field, &$field_join, &$data )
	{
		$json	=	new JRegistry( (string)$field_join->link_options );
		$itemId	=	$json->get( 'itemid', '' );

		if ( $itemId ) {
			$itemId	=	JCckDatabase::loadResult( 'SELECT alias FROM #__menu WHERE id = '.(int)$itemId );
			
			if ( $itemId != '' ) {
				$json->set( 'itemid', $itemId );
				$field_join->link_options	=	$json->toString();
			}
		}
	}
	
	// _updateImportItemId
	protected static function _updateImportItemId( $field, &$field_join, $data )
	{
		$json	=	new JRegistry( (string)$field_join->link_options );
		$itemId	=	$json->get( 'itemid', '' );

		if ( $itemId ) {
			$itemId	=	JCckDatabase::loadResult( 'SELECT id FROM #__menu WHERE alias = "'.(string)$itemId.'"' );

			if ( $itemId != '' ) {
				$json->set( 'itemid', $itemId );
				$field_join->link_options	=	$json->toString();
			}
		}
	}
}
?>