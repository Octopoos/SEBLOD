<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'cck.construction.field.generic_app' );

// Class
class plgCCK_FieldJoomla_Article_App extends plgCCK_FieldGeneric_App
{
	// -------- -------- -------- -------- -------- -------- -------- -------- // Export
	
	// onCCK_FieldExportField
	public static function onCCK_FieldExportField( &$field, &$data, &$extensions )
	{
		$field->options	=	self::_exportCategories( $field->options, '||', $data );
		
		$name		=	( $field->storage_field2 != '' ) ? $field->storage_field2 : $field->storage_field;
		$sql_table	=	'#__cck_store_join_'.$name;
		$sql_path	=	$data['root_sql'].'/'.$sql_table.'.sql';
		$sql_buffer	=	JCckDatabase::getTableCreate( array( $sql_table ) );
		JFile::write( $sql_path, $sql_buffer );
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
		$field->options	=	self::_getCategories( $field->options, '||', $data );
	}
	
	// onCCK_FieldImportType_Field
	public static function onCCK_FieldImportType_Field( &$xml, $data )
	{
	}
	
	// onCCK_FieldImportSearch_Field
	public static function onCCK_FieldImportSearch_Field( &$xml, $data )
	{
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// _exportCategories
	protected static function _exportCategories( $value, $glue, &$data )
	{
		$values	=	JCckDev::fromSTRING( $value, $glue );
		$value	=	array();
		
		foreach ( $values as $v ) {
			$table	=	JTable::getInstance( 'category' );
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