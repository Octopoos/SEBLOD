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

use Joomla\String\StringHelper;

// Plugin
class plgCCK_FieldSelect_Multiple extends JCckPluginField
{
	protected static $type		=	'select_multiple';
	protected static $friendly	=	1;
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		if ( isset( $data['string']['location'] ) && is_array( $data['string']['location'] ) ) {
			if ( !implode( '', $data['string']['location'] ) ) {
				$data['json']['options2']['options']	=	'';
			}
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
		if ( $config['doTranslation'] ) {
			$config['doTranslation']=	$field->bool8;
		}

		// Prepare
		$divider					=	( $field->divider != '' ) ? $field->divider : ',';
		if ( is_array( $value ) ) {
			$value					=	implode( $divider, $value );
		}

		// Set
		$field->text				=	parent::g_getOptionText( $value, $field->options, $divider, $config );
		$field->value				=	$value;
		
		$texts						=	explode( $divider, $field->text );
		$values						=	explode( $divider, $field->value );
		if ( count( $values ) ) {
			$field->values			=	array();
			foreach ( $values as $k=>$v ) {
				$field->values[$k]	=	(object)array( 'text'=>$texts[$k], 'typo_target'=>'text', 'value'=>$v );
			}
		}
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
		$divider	=	( $field->divider != '' ) ? $field->divider : ',';
		if ( !is_array( $value ) ) {
			$value	=	explode( $divider, $value );
		}
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$attr		=	array( 'option.attr'=>'data-cck' );
		$options	=	explode( '||', $field->options );
		if ( $field->location ) {
			$attribs	=	explode( '||', $field->location );
			$attrib		=	count( $attribs );
			$options2	=	json_decode( $field->options2 );
		} else {
			$attribs	=	array();
			$attrib		=	0;
		}
		if ( $field->bool8 ) {
			$field->bool8	=	$config['doTranslation'];
		}
		if ( $field->sorting == 1 ) {
			natsort( $options );
			$optionsSorted	=	array_slice( $options, 0 );
		} elseif ( $field->sorting == 2 ) {
			natsort( $options );
			$optionsSorted	=	array_reverse( $options, true );
		} else {
			$optionsSorted	=	$options;
		}
		$opts	=	array();
		if ( count( $optionsSorted ) ) {
			foreach ( $optionsSorted as $i=>$val ) {
				if ( trim( $val ) != '' ) {
					$text	=	$val;
					if ( StringHelper::strpos( $val, '=' ) !== false ) {
						$opt	=	explode( '=', $val );
						$text	=	$opt[0];
						$val	=	$opt[1];
					}
					if ( $field->bool8 && trim( $text ) != '' ) {
						$text	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) );
					}
					if ( $attrib ) {
						$attr['attr']	=	'';
						foreach ( $attribs as $k=>$a ) {
							$attr['attr']	.=	' '.$a.'="'.$options2->options[$i]->attr[$k].'"';
						}
						$opts[]	=	JHtml::_( 'select.option', $val, $text, $attr );
					} else {
						$opts[]	=	JHtml::_( 'select.option', $val, $text, 'value', 'text' );
					}
				}
			}
		}
		
		$class	=	'inputbox select'.$validate . ( $field->css ? ' '.$field->css : '' );
		$size	=	( @$field->rows ) ? $field->rows : count( $opts );
		
		if ( count( $value ) && $value[0] != '' ) {
			$class	.=	' has-value';
		}
		$attr	=	'class="'.$class.'" size="'.$size.'" multiple="multiple"' . ( $field->attributes ? ' '.$field->attributes : '' );
		$form	=	'';
		if ( count( $opts ) ) {
			if ( $attrib ) {
				$attr	=	array( 'id'=>$id, 'list.attr'=>$attr, 'list.select'=>$value, 'list.translate'=>false,
								   'option.attr'=>'data-cck', 'option.key'=>'value', 'option.text'=>'text' );
				$form	=	JHtml::_( 'select.genericlist', $opts, $name.'[]', $attr );			
			} else {
				$form	=	JHtml::_( 'select.genericlist', $opts, $name.'[]', $attr, 'value', 'text', $value, $id );
			}
		}
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			$field->text	=	parent::g_getOptionText( $value, $field->options, $divider, $config );

			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			$doTranslation				=	$config['doTranslation'];
			if ( $config['doTranslation'] ) {
				$config['doTranslation']=	$field->bool8;
			}
			$field->text				=	parent::g_getOptionText( $value, $field->options, $divider, $config );
			$config['doTranslation']	=	$doTranslation;
			parent::g_getDisplayVariation( $field, $field->variation, $value, $field->text, $form, $id, $name, '<select', '', '', $config );
		}
		$field->value	=	$value;
		
		$texts						=	( isset( $field->text ) ) ? explode( $divider, $field->text ) : array();
		$values						=	( is_string( $field->value ) ) ? explode( $divider, $field->value ) : $field->value;
		if ( count( $values ) ) {
			$field->values			=	array();
			foreach ( $values as $k=>$v ) {
				$field->values[$k]	=	(object)array( 'text'=>@$texts[$k], 'value'=>$v );
			}
		}

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

		// Init
		$divider			=	$field->match_value ? $field->match_value : $field->divider;
		$field->match_value	=	$divider;
		if ( is_array( $value ) ) {
			$value	=	implode( $divider, $value );
		}
		
		// Prepare
		$field->divider	=	$divider;
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
		
		// Init
		if ( count( $inherit ) ) {
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$name	=	$field->name;
		}
		$doTranslation				=	$config['doTranslation'];
		if ( $config['doTranslation'] ) {
			$config['doTranslation']=	$field->bool8;
		}

		// Prepare
		$divider	=	( $field->divider != '' ) ? $field->divider : ',';
		if ( $divider ) {
			$nb		=	count( $value );
			if ( is_array( $value ) && $nb > 0 ) {
				$value	=	implode( $divider ,$value );
			}
		}
		
		// Validate
		$text						=	parent::g_getOptionText( $value, $field->options, $divider, $config );
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
	
	// isFriendly
	public static function isFriendly()
	{
		return self::$friendly;
	}
}
?>