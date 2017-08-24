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
class plgCCK_FieldJForm_Category_App extends plgCCK_FieldGeneric_App
{	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Export

	// onCCK_FieldExportField
	public static function onCCK_FieldExportField( &$field, &$data, &$extensions )
	{
	}
	
	// onCCK_FieldExportType_Field
	public static function onCCK_FieldExportType_Field( $field, &$field_join, &$data, &$extensions )
	{
		if ( !$field_join->live && $field_join->live_value ) {
			$glue					=	( $field->divider != '' ) ? $field->divider : '';
			$field_join->live_value	=	self::_exportCategories( $field_join->live_value, $glue, $data );
		}
	}
	
	// onCCK_FieldExportSearch_Field
	public static function onCCK_FieldExportSearch_Field( $field, &$field_join, &$data, &$extensions )
	{
		if ( !$field_join->live && $field_join->live_value ) {
			$glue					=	( $field_join->match_value ) ? $field_join->match_value : ' ';
			$field_join->live_value	=	self::_exportCategories( $field_join->live_value, $glue, $data );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Import
	
	// onCCK_FieldImportField
	public static function onCCK_FieldImportField( &$field, $data )
	{
	}
	
	// onCCK_FieldImportType_Field
	public static function onCCK_FieldImportType_Field( $field, &$xml, $data )
	{
		if ( isset( $xml->attributes()->live ) && isset( $xml->attributes()->live_value ) ) {
			if ( !(string)$xml->attributes()->live && (string)$xml->attributes()->live_value ) {
				$glue							=	( $field->divider != '' ) ? $field->divider : '';
				$xml->attributes()->live_value	=	self::_getCategories( (string)$xml->attributes()->live_value, $glue, $data );
			}
		}
	}
	
	// onCCK_FieldImportSearch_Field
	public static function onCCK_FieldImportSearch_Field( $field, &$xml, $data )
	{
		if ( isset( $xml->attributes()->live ) && isset( $xml->attributes()->live_value ) ) {
			if ( !(string)$xml->attributes()->live && (string)$xml->attributes()->live_value ) {
				$glue							=	( isset( $xml->attributes()->match_value ) && (string)$xml->attributes()->match_value ) ? (string)$xml->attributes()->match_value : ' ';
				$xml->attributes()->live_value	=	self::_getCategories( (string)$xml->attributes()->live_value, $glue, $data );
			}
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// _exportCategories
	protected static function _exportCategories( $value, $glue, &$data )
	{
		$values	=	JCckDev::fromSTRING( $value, $glue );
		$value	=	array();
		
		foreach ( $values as $v ) {
			$table	=	JTable::getInstance( 'Category' );
			if ( $v > 0 ) {
				$table->load( $v );
			}
			$table->name	=	$data['root_category'] .'_'. str_replace( '-', '_', $table->alias );
			$value[]		=	$table->name;
			CCK_Export::exportContent( 'joomla_category', $table, $data, $extensions, 0 );
		}
		
		return implode( $glue, $value );
	}
	
	// _getCategories
	protected static function _getCategories( $value, $glue, $data )
	{
		$values	=	JCckDev::fromSTRING( $value, $glue );
		$value	=	array();
		
		foreach ( $values as $v ) {
			if ( isset( $data['categories'][$v] ) ) {
				$value[]	=	$data['categories'][$v];
			}
		}
		
		return implode( $glue, $value );
	}
}
?>