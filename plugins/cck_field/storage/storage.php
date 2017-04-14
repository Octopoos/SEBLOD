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
class plgCCK_FieldStorage extends JCckPluginField
{
	protected static $type		=	'storage';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		$field->value	=	$value;
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );

		// Prepare
		$app	=	JFactory::getApplication();
		if ( $app->input->get( 'option' ) == 'com_cck' && $app->input->get( 'view' ) == 'form' ) {
			$form		=	'';
			$value		=	'';
		} else {
			$alter				=	true;
			$alter_type_default	=	( isset( $inherit['alter_type_value'] ) ) ? $inherit['alter_type_value'] : '';
			if ( isset( $config['item']->id ) && $config['item']->id && isset( $config['item']->storage_table ) && $config['item']->storage_table != '' ) {
				$db		=	JFactory::getDbo();
				$prefix	=	$db->getPrefix();
				$table	=	str_replace( '#__', $prefix, $config['item']->storage_table );
				$tables	=	$db->getTableList();
				if ( in_array( $table, $tables ) ) {
					$column				=	JCckDatabase::loadObject( 'SHOW COLUMNS FROM '.$table.' WHERE field = "'.$config['item']->storage_field.'"' );
					$alter_type_value	=	( isset( $column->Type ) ) ? strtoupper( $column->Type ) : $alter_type_default;
				} else {
					$alter				=	false;
					$alter_type_value	=	$alter_type_default;
					$alter_type_default	=	'';	
				}
			} else {
				$alter_type_value	=	$alter_type_default;
				$alter_type_default	=	'';
			}
			ob_start();
			include_once __DIR__.'/tmpl/form.php';				
			$form	=	ob_get_clean();
		}

		// Set
		$field->form	=	$form;
		$field->value	=	$value;

		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Set
		$field->form	=	'';
		$field->value	=	'';
	}
	
	// onCCK_FieldPrepareStore
	public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{		
		return parent::g_onCCK_FieldRenderForm( $field );
	}
}
?>