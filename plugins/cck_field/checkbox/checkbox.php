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
class plgCCK_FieldCheckbox extends JCckPluginField
{
	protected static $type		=	'checkbox';
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
	
	// onCCK_FieldPrepareExport
	public function onCCK_FieldPrepareExport( &$field, $value = '', &$config = array() )
	{
		if ( static::$type != $field->type ) {
			return;
		}
		
		self::onCCK_FieldPrepareContent( $field, $value, $config );
		
		$field->output	=	$field->text;
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
			$name	=	( strpos( $name, '[]' ) !== false ) ? substr( $name, 0, -1 ).$inherit['xk'].']' : $name;
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
			parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config );
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
		
		$count	=	count( $opts );
		$count2	=	0;
		$form	=	'';
		if ( $field->bool ) {
			$orientation	=	' vertical';
			$field->bool2	=	( !$field->bool2 ) ? 1 : $field->bool2;
			$modulo			=	$count % $field->bool2;
			$columns		=	(int)( $count / ( !$field->bool2 ? 1 : $field->bool2 ) );
		} else {
			$orientation	=	'';
		}
		$attr	=	'class="checkbox'.$validate.'" size="1"';
		$attr_key	=	'data-cck';

		if ( $field->bool && $field->bool2 > 1 && $count > 1 ) {
			$k	=	0;
			foreach ( $opts as $i=>$o ) {
				if ( $i == 0 ) {
					$form	.=	'<div class="cck-fl">';
				} elseif ( ( $modulo && ( $k % ($columns+1) == 0 ) )
						|| ( $modulo <= 0 && ( $k % $columns == 0 ) ) ) {
					$form	.=	'</div><div class="cck-fl">';
					$modulo--;
					$k	=	0;
				}
				$k++;
				$attr2		=	( isset( $o->$attr_key ) ) ? $o->$attr_key : '';
				if ( in_array( (string)$o->value, (array)$value ) ) {
					$checked	=	'checked="checked" ';
					$count2++;
				} else {
					$checked	=	'';
				}
				$form		.=	'<input type="checkbox" id="'.$id.$i.'" name="'.$name.'[]" value="'.$o->value.'" '.$checked.$attr.$attr2.' />';

				$form		.=	'<label for="'.$id.$i.'">'.$o->text.'</label>';
			}
			$form		.=	'</div>';
		} else {
			if ( $count == 1 && strpos( $optionsSorted[0], '=' ) === false ) {
				foreach ( $opts as $i=>$o ) {
					$attr2		=	( isset( $o->$attr_key ) ) ? $o->$attr_key : '';
					if ( in_array( (string)$o->value, (array)$value ) ) {
						$checked	=	'checked="checked" ';
						$count2++;
					} else {
						$checked	=	'';
					}
					$form		.=	'<input type="checkbox" id="'.$id.$i.'" name="'.$name.'[]" value="'.$o->value.'" '.$checked.$attr.$attr2.' />';
				}
			} else {
				foreach ( $opts as $i=>$o ) {
					$attr2		=	( isset( $o->$attr_key ) ) ? $o->$attr_key : '';
					if ( in_array( (string)$o->value, (array)$value ) ) {
						$checked	=	'checked="checked" ';
						$count2++;
					} else {
						$checked	=	'';
					}
					$form		.=	'<input type="checkbox" id="'.$id.$i.'" name="'.$name.'[]" value="'.$o->value.'" '.$checked.$attr.$attr2.' />';
					$form		.=	'<label for="'.$id.$i.'">'.$o->text.'</label>';
				}
			}
		}
		if ( $field->bool7 ) {
			$checked	=	( $count == $count2 ? '1' : '' ) ? 'checked="checked" ' : '';
			$attr		=	'onclick="Joomla.checkAll(this,\''.$id.'\');"';
			$check_all	=	'<input type="checkbox" id="'.$id.'_toggle'.'" name="'.$name.'_toggle" value="" '.$checked.$attr.' />'
						.	'<label for="'.$id.'_toggle">'.JText::_( 'COM_CCK_CHECK_ALL' ).'</label>';
			if ( $field->bool && $field->bool2 > 1 && $count > 1 ) {
				$check_all	=	'<div class="cck-clrfix">'.$check_all.'</div>';
			}
			if ( $field->bool7 == 2 ) {
				$form	.=	$check_all;
			} else {
				$form	=	$check_all.$form;
			}
		}
		$class		=	'checkboxes'.$orientation . ( $field->css ? ' '.$field->css : '' );
		$attr		=	'class="'.$class.'"' . ( $field->attributes ? ' '.$field->attributes : '' );
		$form		=	'<fieldset id="'.$id.'" '.$attr.'>'.$form.'</fieldset>';
		
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
			parent::g_getDisplayVariation( $field, $field->variation, $value, $field->text, $form, $id, $name, '<input', '', '', $config );
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
			$nb 	=	count( $value );
			if ( is_array( $value ) && $nb > 0 ) {
				$value	=	implode( $divider, $value );
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