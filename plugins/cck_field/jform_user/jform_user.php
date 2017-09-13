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
class plgCCK_FieldJForm_User extends JCckPluginField
{
	protected static $type		=	'jform_user';
	protected static $type2		=	'user';
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

		$field->value		=	$value;
		$field->text		=	JCckDatabase::loadResult( 'SELECT name FROM #__users WHERE id = '.(int)$value ); //@
		$field->typo_target	=	'text';
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

		$value		=	( $value !== '' ) ? $value : $field->defaultvalue;
		$userid		=	JFactory::getUser()->id;

		if ( $config['client'] != 'search' ) {
			if ( ( ! $value && $userid && !( $field->storage_field == 'modified_by' || $field->storage_field == 'modified_user_id' ) ) || ( $config['pk'] > 0 && ( $field->storage_field == 'modified_by' || $field->storage_field == 'modified_user_id' ) ) ) { // todo: this must be changed asap!
				$value	=	$userid;
			}
		}

		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}

		// Prepare
		if ( parent::g_isStaticVariation( $field, $field->variation, true ) ) {
			$form			=	'';
			$field->text	=	JCckDatabase::loadResult( 'SELECT name FROM #__users WHERE id = '.(int)$value );
			parent::g_getDisplayVariation( $field, $field->variation, $value, $field->text, $form, $id, $name, '<select', '', '', $config );
		} else {
			$class	=	'inputbox text'.$validate . ( $field->css ? ' '.$field->css : '' );
			$readonly	=	( $field->variation == 'disabled' ) ? 'readonly="true"' : '';
			$xml		=	'
							<form>
								<field
									type="'.self::$type2.'"
									name="'.$name.'"
									id="'.$id.'"
									label="'.htmlspecialchars( $field->label ).'"
									class="'.$class.'"
									size="18"
									'.$readonly.'
								/>
							</form>
						';
			$form	=	JForm::getInstance( $id, $xml );
			$form	=	$form->getInput( $name, '', $value );

			$form		=	str_replace( 'value="0"', 'value=""', $form );

			if ( JFactory::getApplication()->isClient( 'site' ) ) {
				$form		=	str_replace( '="index.php?', '="' . JUri::root() . 'administrator/index.php?', $form );
			}

			// Set
			if ( ! $field->variation ) {
				$field->form	=	$form;
				if ( $field->script ) {
					parent::g_addScriptDeclaration( $field->script );
				}
			} else {
				$field->text	=	JCckDatabase::loadResult( 'SELECT name FROM #__users WHERE id = '.(int)$value );
				parent::g_getDisplayVariation( $field, $field->variation, $value, $field->text, $form, $id, $name, '<input', '', '', $config );
			}
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
		$value	=	( $value > 0 ) ? $value : '';
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
		return parent::g_onCCK_FieldRenderContent( $field, 'text' );
	}

	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
}
?>