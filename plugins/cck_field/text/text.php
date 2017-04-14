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
class plgCCK_FieldText extends JCckPluginField
{
	protected static $type		=	'text';
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

	// onCCK_FieldConstruct_TypeForm
	public static function onCCK_FieldConstruct_TypeForm( &$field, $style, $data = array(), &$config = array() )
	{
		if ( !isset( $config['construction']['variation'][self::$type] ) ) {
			$data['variation']['201']			=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_CUSTOM' ) );
			$data['variation']['custom_number']	=	JHtml::_( 'select.option', 'custom_number', JText::_( 'COM_CCK_NUMBER' ) );
			$data['variation']['custom_url']	=	JHtml::_( 'select.option', 'custom_url', JText::_( 'COM_CCK_URL' ) );
			$data['variation']['202']			=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
			$data['variation']['203']			=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_STAR_IS_SECURED' ) );
			$data['variation']['204']			=	JHtml::_( 'select.option', '</OPTGROUP>', '' );

			$config['construction']['variation'][self::$type]	=	$data['variation'];
		} else {
			$data['variation']									=	$config['construction']['variation'][self::$type];
		}
		
		parent::onCCK_FieldConstruct_TypeForm( $field, $style, $data, $config );
	}

	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		if ( !isset( $config['construction']['variation'][self::$type] ) ) {
			/*
			$data['variation']['form_filter_ajax']	=	JHtml::_( 'select.option', 'form_filter_ajax', JText::_( 'COM_CCK_FORM_FILTER_AJAX' ) );
			*/
			$data['variation']['201']			=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_CUSTOM' ) );
			$data['variation']['custom_number']	=	JHtml::_( 'select.option', 'custom_number', JText::_( 'COM_CCK_NUMBER' ) );
			$data['variation']['custom_url']	=	JHtml::_( 'select.option', 'custom_url', JText::_( 'COM_CCK_URL' ) );
			$data['variation']['202']			=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
			$data['variation']['203']			=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_STAR_IS_SECURED' ) );
			$data['variation']['204']			=	JHtml::_( 'select.option', '</OPTGROUP>', '' );

			$config['construction']['variation'][self::$type]	=	$data['variation'];
		} else {
			$data['variation']									=	$config['construction']['variation'][self::$type];
		}
		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
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
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$value		=	( $value != '' ) ? $value : $field->defaultvalue;
		$value		=	( $value != ' ' ) ? $value : '';
		$value		=	htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' );
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config, array( 'minSize'=>true ) );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$class	=	'inputbox text'.$validate . ( $field->css ? ' '.$field->css : '' );
		if ( $value != '' ) {
			$class	.=	' has-value';
		}
		$maxlen	=	( $field->maxlength > 0 ) ? ' maxlength="'.$field->maxlength.'"' : '';
		$attr	=	'class="'.$class.'" size="'.$field->size.'"'.$maxlen;

		if ( $field->attributes != '' ) {
			$attr	.=	' '.$field->attributes;
		}

		if ( strpos( $field->variation, 'custom_' ) !== false ) {
			$isCustom	=	true;
			$type		=	substr( $field->variation, 7 );
		} else {
			$isCustom	=	false;
			$type		=	'text';
		}
		$form	=	'<input type="'.$type.'" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />';
		
		// Set
		if ( ! $field->variation || $isCustom ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<input', '', '', $config );
		}
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
		
		// Prepare
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		if ( strpos( $field->value, '&' ) !== false ) {
			$field->value	=	str_replace( array( '&amp;', '&quot;' ), array( '&', '\"' ), $field->value );
		}
		
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
		
		// Init
		if ( count( $inherit ) ) {
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$name	=	$field->name;
		}
		
		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		$field->value	=	$value;
		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
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