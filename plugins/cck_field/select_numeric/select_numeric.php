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
class plgCCK_FieldSelect_Numeric extends JCckPluginField
{
	protected static $type			=	'select_numeric';
	protected static $convertible	=	1;
	protected static $friendly		=	1;
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
		
		// Init
		$doTranslation				=	$config['doTranslation'];

		// Prepare
		$options2					=	JCckDev::fromJSON( $field->options2 );
		$opts						=	self::_getOptionsList( $options2, $field, $config );
		$config['doTranslation']	=	0;

		// Set
		$field->text				=	parent::g_getOptionText( $value, $field->options, '', $config );
		$field->value				=	$value;
		$field->typo_target			=	'text';
		$config['doTranslation']	=	$doTranslation;
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
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$opts		=	self::_getOptionsList( $options2, $field, $config );
		$class		=	'inputbox select'.$validate . ( $field->css ? ' '.$field->css : '' );
		if ( $value != '' ) {
			$class	.=	' has-value';
		}
		$attr	=	'class="'.$class.'"' . ( $field->attributes ? ' '.$field->attributes : '' );
		$form	=	( count( $opts ) ) ? JHtml::_( 'select.genericlist', $opts, $name, $attr, 'value', 'text', $value, $id ) : '';
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			$field->text	=	parent::g_getOptionText( $value, $field->options, ( $config['client'] == 'search' ? ',' : '' ), $config );

			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			$field->text	=	parent::g_getOptionText( $value, $field->options, ( $config['client'] == 'search' ? ',' : '' ), $config );
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<select', '', '', $config );
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
		
		// Set
		$field->match_value	=	$field->match_value ? $field->match_value : ',';
		
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
		$doTranslation	=	$config['doTranslation'];
		$options2		=	JCckDev::fromJSON( $field->options2 );
		$opts			=	self::_getOptionsList( $options2, $field, $config );
		
		// Validate
		$config['doTranslation']	=	0;
		$text						=	parent::g_getOptionText( $value, $field->options, '', $config );
		$config['doTranslation']	=	$doTranslation;
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		$field->text	=	$text;
		$field->value	=	$value;
		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field, 'text' );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{		
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _getOptionsList
	protected static function _getOptionsList( $options2, &$field, $config )
	{
		$opts		=	array();
		$options	=	array();
		
		if ( trim( $field->selectlabel ) ) {
			if ( $config['doTranslation'] ) {
				$field->selectlabel	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->selectlabel ) ) );
			}
			$opts[]	=	JHtml::_( 'select.option',  '', '- '.$field->selectlabel.' -', 'value', 'text' );
		}
		if ( isset( $options2['first'] ) && $options2['first'] != '' ) {
			if ( strpos( $options2['first'], '=' ) !== false ) {
				$opt		=	explode( '=', $options2['first'] );
				$opt[0]		=	trim( $opt[0] );

				if ( $opt[0] != '' ) {
					if ( $config['doTranslation'] ) {
						$opt[0]		=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', $opt[0] ) );
					}
				}
				$opts[]		=	JHtml::_( 'select.option', $opt[1], $opt[0], 'value', 'text' );
				$options[]	=	$opt[0].'='.$opt[1];
			} else {
				$opts[]		=	JHtml::_( 'select.option', $options2['first'], $options2['first'], 'value', 'text' );
				$options[]	=	$options2['first'].'='.$options2['first'];
			}
		}
		$val	=	( $options2['start'] ? $options2['start'] : 0 );
		$step	=	( $options2['step'] ? $options2['step'] : 0 );
		$limit 	=	( $options2['end'] ? $options2['end'] : 0 );
		$math	=	isset( $options2['math'] ) ? $options2['math'] : NULL;
		$force	=	( isset( $options2['force_digits'] ) && $options2['force_digits'] ) ? $options2['force_digits'] : 0;
		
		if ( $step && $val || $step && $limit || $step && $val && $limit ) {
			while ( 69 ) {
				if ( $force ) {
					$val	=	str_pad( $val, $force, '0' , STR_PAD_LEFT );
				}
				if ( $math == 0 && $val <= $limit  ) {
					$opts[]		=	JHtml::_('select.option', $val, $val, 'value', 'text' );
					$options[]	=	$val.'='.$val;
					$val		=	$val + $step;
				} elseif ( $math == 1 && $val <= $limit  ) {
					$opts[]		=	JHtml::_('select.option', $val, $val, 'value', 'text' );
					$options[]	=	$val.'='.$val;
					$val		=	$val * $step;
				} elseif ( $math == 2 && $val >= $limit  ) {
					$opts[]		=	JHtml::_('select.option', $val, $val, 'value', 'text' );
					$options[]	=	$val.'='.$val;
					$val		=	$val - $step;
				} elseif ( $math == 3 && $val > $limit  ) {
					$opts[]		=	JHtml::_('select.option', $val, $val, 'value', 'text' );
					$options[]	=	$val.'='.$val;
					$val		=	floor( $val / $step );
				} else {
					break;
				}
			}
		}
		if ( isset( $options2['last'] ) && $options2['last'] != '' ) {
			if ( strpos( $options2['last'], '=' ) !== false ) {
				$opt		=	explode( '=', $options2['last'] );
				$opt[0]		=	trim( $opt[0] );

				if ( $opt[0] != '' ) {
					if ( $config['doTranslation'] ) {
						$opt[0]		=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', $opt[0] ) );
					}
				}
				$opts[]		=	JHtml::_( 'select.option', $opt[1], $opt[0], 'value', 'text' );
				$options[]	=	$opt[0].'='.$opt[1];
			} else {
				$opts[]		=	JHtml::_( 'select.option', $options2['last'], $options2['last'], 'value', 'text' );
				$options[]	=	$options2['last'].'='.$options2['last'];
			}
		}
		$field->options	=	implode( '||', $options );

		return $opts;
	}

	// _getOptionsListProperty
	protected static function _getOptionsListProperty( $property, $field, $value, $config = array() )
	{
		$method		=	'get'.ucfirst( $property).'FromOptions';
		$options2	=	JCckDev::fromJSON( $field->options2 );
		
		/* tmp */
		$jtext						=	$config['doTranslation'];
		$config['doTranslation']	=	0;
		/* tmp */
		
		self::_getOptionsList( $options2, $field, $config );
		
		$result				=	parent::$method( $field, $value, $config );

		/* tmp */
		$config['doTranslation']	=	$jtext;
		/* tmp */

		return $result;
	}

	// getTextFromOptions
	public static function getTextFromOptions( $field, $value, $config = array() )
	{
		return self::_getOptionsListProperty( 'text', $field, $value, $config );
	}

	// isConvertible
	public static function isConvertible()
	{
		return self::$convertible;
	}
	
	// isFriendly
	public static function isFriendly()
	{
		return self::$friendly;
	}
}
?>