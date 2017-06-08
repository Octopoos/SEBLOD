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
class plgCCK_FieldJForm_MenuItem extends JCckPluginField
{
	protected static $type		=	'jform_menuitem';
	protected static $type2		=	'menuitem';
	protected static $friendly	=	1;
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
		
		// Prepare
		$link			=	'';
		$html			=	'';
		$text			=	'';
		if ( $value ) {
			$app		=	JFactory::getApplication();
			$menu		=	$app->getMenu()->getItem( $value );
			if ( is_object( $menu ) ) {
				$link		=	JRoute::_( 'index.php?Itemid='.$value );
				$text		=	$menu->title;
				$html		=	'<a href="'.$link.'">'.$text.'</a>';
			} else {
				$value		=	'';
			}
		}

		// Set
		$field->html		=	$html;
		$field->link		=	$link;
		$field->linked		=	true;
		$field->text		=	$text;
		$field->typo_target	=	'text';
		$field->value		=	$value;
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
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$opt		=	'';
		$options	=	explode( '||', $field->options );
		if ( trim( $field->selectlabel ) ) {
			if ( $config['doTranslation'] ) {
				$field->selectlabel	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->selectlabel ) ) );
			}
			$opt	=	'<option value="">'.'- '.$field->selectlabel.' -'.'</option>';
		}
		$class		=	'inputbox select'.$validate . ( $field->css ? ' '.$field->css : '' );
		if ( count( $options ) ) {
			$group	=	false;
			foreach ( $options as $i=>$val ) {
				if ( trim( $val ) != '' ) {
					if ( StringHelper::strpos( $val, '=' ) !== false ) {
						$o		=	explode( '=', $val );
					} else {
						$o		=	array( 0=>$val, 1=>$val );	
					}
					if ( $field->bool8 && trim( $o[0] ) ) {
						$o[0]	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $o[0] ) ) );
					}
					if ( $o['1'] == 'optgroup' ) {
						if ( $group ) {
							$opt	.=	'</group>';
						}
						$opt	.=	'<group label="'.$o['0'].'">';
						$group	=	true;
					} else {
						$opt	.=	'<option value="'.$o['1'].'">'.$o['0'].'</option>';
					}
				}
			}
			if ( $group ) {
				$opt	.=	'</group>';
			}
		}
		$xml		=	'
						<form>
							<field
								type="'.self::$type2.'"
								name="'.$name.'"
								id="'.$id.'"
								label="'.htmlspecialchars( $field->label ).'"
								class="'.$class.'"
							>'.$opt.'</field>
						</form>
					';
		$form		=	JForm::getInstance( $id, $xml );
		$form		=	$form->getInput( $name, '', $value );
		if ( strpos( $id, '-' ) !== false ) {
			$id2	=	str_replace( '-', '_', $id );
			$form	=	str_replace( 'id="'.$id2.'"', 'id="'.$id.'"', $form );
		}
		
		// Set
		if ( ! $field->variation ) {
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
		return parent::g_onCCK_FieldRenderContent( $field, 'html' );
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