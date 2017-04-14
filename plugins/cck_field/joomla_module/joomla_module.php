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
class plgCCK_FieldJoomla_Module extends JCckPluginField
{
	protected static $type		=	'joomla_module';
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
		
		// Prevent Joomla! modules to be rendered on format=raw as there is no renderer class
		if ( JFactory::getApplication()->input->get( 'format' ) == 'raw' ) {
			$field->value	=	'';

			return;
		}

		// Prepare
		if ( $field->defaultvalue ) {
			$mode	=	$field->bool ? 'module' : 'position';
			$style	=	$field->style ? ','.$field->style : '';
			$value	=	'{load'.$mode.' '.$field->defaultvalue.$style.'}';

			if ( $field->bool2 ) {
				$value	=	JHtml::_( 'content.prepare', $value );
			}
		}
		
		// Set
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
		
		// Init
		$form		=	'';
		$value		=	'';

		// Prevent Joomla! modules to be rendered on format=raw as there is no renderer class
		if ( JFactory::getApplication()->input->get( 'format' ) == 'raw' ) {
			$field->form	=	$form;
			$field->value	=	$value;

			return;
		}

		// Prepare
		if ( $field->defaultvalue ) {
			$mode	=	$field->bool ? 'module' : 'position';
			$style	=	$field->style ? ','.$field->style : '';
			$form	=	'{load'.$mode.' '.$field->defaultvalue.$style.'}';
			$value	=	$field->defaultvalue;

			if ( $field->bool2 ) {
				$form	=	JHtml::_( 'content.prepare', $form );
			}
		}

		// Set
		$field->form	=	$form;
		$field->value	=	$value;
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Prepare
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		
		// Return
		if ( $return === true ) {
			return $field;
		}
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