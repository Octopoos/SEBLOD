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
class plgCCK_FieldSelect_Simple extends JCckPluginField
{
	protected static $type			=	'select_simple';
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
		if ( isset( $data['string']['location'] ) && is_array( $data['string']['location'] ) ) {
			if ( !implode( '', $data['string']['location'] ) ) {
				$data['json']['options2']['options']	=	'';
			}
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		if ( !isset( $config['construction']['variation'][self::$type] ) ) {
			$data['variation']['201']			=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_LIST' ) );
			$data['variation']['list']			=	JHtml::_( 'select.option', 'list', JText::_( 'COM_CCK_DEFAULT' ) );
			$data['variation']['list_filter']	=	JHtml::_( 'select.option', 'list_filter', JText::_( 'COM_CCK_FORM_FILTER' ) );
			/*
			$data['variation']['list_filter_ajax']	=	JHtml::_( 'select.option', 'list_filter_ajax', JText::_( 'COM_CCK_FORM_FILTER_AJAX' ) );
			*/
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
		
		// Init
		$doTranslation				=	$config['doTranslation'];
		if ( $config['doTranslation'] ) {
			$config['doTranslation']=	$field->bool8;
		}

		// Set
		$field->text				=	parent::g_getOptionText( $value, $field->options, '', $config );
		$field->value				=	$value;
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
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$value		=	( $value != '' ) ? $value : $field->defaultvalue;
		
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
		$attributes	=	array();
		$opts		=	array();
		$options	=	array();
		if ( trim( $field->selectlabel ) ) {
			if ( $config['doTranslation'] ) {
				$field->selectlabel	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->selectlabel ) ) );
			}
			if ( $attrib ) {
				$attr['attr']	=	'';
				foreach ( $attribs as $k=>$a ) {
					$attr['attr']	.=	' '.$a.'=""';
				}
				$attributes[]	=	$attr['attr'];
				$opts[]			=	JHtml::_( 'select.option',  '', '- '.$field->selectlabel.' -', $attr );
			} else {
				$opts[]			=	JHtml::_( 'select.option',  '', '- '.$field->selectlabel.' -', 'value', 'text' );
			}
			$options[]			=	$field->selectlabel.'=';
		}
		$optgroup	=	0;
		
		if ( 1 == 1 ) { // !
			if ( count( $optionsSorted ) ) {
				foreach ( $optionsSorted as $i=>$val ) {
					if ( trim( $val ) != '' ) {
						if ( StringHelper::strpos( $val, '=' ) !== false ) {
							$opt	=	explode( '=', $val );
							if ( $opt[1] == 'optgroup' ) {
								if ( $optgroup == 1 ) {
									$opts[]	=	JHtml::_( 'select.option', '</OPTGROUP>' );
								}
								if ( $field->bool8 && trim( $opt[0] ) ) {
									$opt[0]	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $opt[0] ) ) );
								}
								$opts[]		=	JHtml::_( 'select.option', '<OPTGROUP>', $opt[0] );
								$optgroup	=	1;
							} elseif ( $opt[1] == 'endgroup' && $optgroup == 1 ) {
								$opts[]		=	JHtml::_( 'select.option', '</OPTGROUP>' );
								$optgroup	=	0;
							} else {
								if ( $field->bool8 && trim( $opt[0] ) ) {
									$opt[0]	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $opt[0] ) ) );
								}
								if ( $attrib ) {
									$attr['attr']	=	'';
									foreach ( $attribs as $k=>$a ) {
										$attr['attr']	.=	' '.$a.'="'.$options2->options[$i]->attr[$k].'"';
									}
									$attributes[]	=	$attr['attr'];
									$opts[]			=	JHtml::_( 'select.option', $opt[1], $opt[0], $attr );
								} else {
									$opts[]			=	JHtml::_( 'select.option', $opt[1], $opt[0], 'value', 'text' );
								}
								$options[]			=	$opt[0].'='.$opt[1];
							}
						} else {
							if ( $val == 'endgroup' && $optgroup == 1 ) {
								$opts[]		=	JHtml::_( 'select.option', '</OPTGROUP>' );
								$optgroup	=	0;
							} else {
								$text		=	$val;
								if ( $field->bool8 && trim( $text ) != '' ) {
									$text	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) );
								}
								if ( $attrib ) {
									$attr['attr']	=	'';
									foreach ( $attribs as $k=>$a ) {
										$attr['attr']	.=	' '.$a.'="'.$options2->options[$i]->attr[$k].'"';
									}
									$attributes[]	=	$attr['attr'];
									$opts[]			=	JHtml::_( 'select.option', $val, $text, $attr );
								} else {
									$opts[]			=	JHtml::_( 'select.option', $val, $text, 'value', 'text' );
								}
								$options[]			=	$text.'='.$val;
							}
						}
					}
				}
				if ( $optgroup == 1 ) {
					$opts[]		=	JHtml::_( 'select.option', '</OPTGROUP>' );
				}
			}
		} else {
			if ( count( $options2->options ) ) {
				foreach ( $options2->options as $o ) {
					if ( trim( $o->value ) != '' ) {
						if ( !$o->text ) {
							$o->text	=	$o->value;
						}
						if ( $o->value == 'optgroup' ) {
							if ( $optgroup == 1 ) {
								$opts[]	=	JHtml::_( 'select.option', '</OPTGROUP>' );
							}
							if ( $field->bool8 && trim( $opt[0] ) ) {
								$opt[0]	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $opt[0] ) ) );
							}
							$opts[]		=	JHtml::_( 'select.option', '<OPTGROUP>', $opt[0] );
							$optgroup	=	1;
						} elseif ( $o->value == 'endgroup' && $optgroup == 1 ) {
							$opts[]		=	JHtml::_( 'select.option', '</OPTGROUP>' );
							$optgroup	=	0;
						} else {
							if ( $field->bool8 && trim( $o->text ) ) {
								$o->text	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $o->text ) ) );
							}
							if ( $attrib ) {
								$attr['attr']	=	'';
								foreach ( $attribs as $k=>$a ) {
									$attr['attr']	.=	' '.$a.'="'.$options2->options[$i]->attr[$k].'"';
								}
								$attributes[]	=	$attr['attr'];
								$opts[]			=	JHtml::_( 'select.option', $o->value, $o->text, $attr );
							} else {
								$opts[]			=	JHtml::_( 'select.option', $o->value, $o->text, 'value', 'text' );
							}
							$options[]			=	$o->text.'='.$o->val;
						}
					}
				}
				if ( $optgroup == 1 ) {
					$opts[]		=	JHtml::_( 'select.option', '</OPTGROUP>' );
				}
			}
		}
		
		$class	=	'inputbox select'.$validate . ( $field->css ? ' '.$field->css : '' );
		if ( $value != '' ) {
			$class	.=	' has-value';
		}
		$attr	=	'class="'.$class.'"' . ( $field->attributes ? ' '.$field->attributes : '' );
		$form	=	'';
		if ( count( $opts ) ) {
			if ( $attrib ) {
				$attr	=	array( 'id'=>$id, 'list.attr'=>$attr, 'list.select'=>$value, 'list.translate'=>false,
								   'option.attr'=>'data-cck', 'option.key'=>'value', 'option.text'=>'text' );
				$form	=	JHtml::_( 'select.genericlist', $opts, $name, $attr );
			} else {
				$form	=	JHtml::_( 'select.genericlist', $opts, $name, $attr, 'value', 'text', $value, $id );
			}
		}
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			$field->text	=	parent::g_getOptionText( $value, $field->options, ( $config['client'] == 'search' ? ',' : '' ), $config );
			
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			$doTranslation				=	$config['doTranslation'];
			if ( $config['doTranslation'] ) {
				$config['doTranslation']=	$field->bool8;
			}
			$field->attributesList		=	( count( $attributes ) ) ? implode( '||', $attributes ) : '';
			$field->optionsList			=	( count( $options ) ) ? implode( '||', $options ) : '';
			$field->text				=	parent::g_getOptionText( $value, $field->options, ( $config['client'] == 'search' ? ',' : '' ), $config );
			$config['doTranslation']	=	$doTranslation;
			parent::g_getDisplayVariation( $field, $field->variation, $value, $field->text, $form, $id, $name, '<select', '', '', $config );
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
		$doTranslation				=	$config['doTranslation'];
		if ( $config['doTranslation'] ) {
			$config['doTranslation']=	$field->bool8;
		}

		// Validate
		$text						=	parent::g_getOptionText( $value, $field->options, '', $config );
		$config['doTranslation']	=	$doTranslation;
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		// Set or Return
		// $field->text		=	$text;
		// $field->value	=	$value;
		if ( $return === true ) {
			return $value;
		}
		$field->text	=	$text;	//todo: move up
		$field->value	=	$value;	//todo: move up
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