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

JLoader::register( 'JFormFieldCckCategory', JPATH_PLATFORM.'/cck/joomla/form/field/category.php' );

// Plugin
class plgCCK_FieldJForm_Category extends JCckPluginField
{
	protected static $type			=	'jform_category';
	protected static $type2			=	'cckcategory';
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
		
		// Set
		$field->value		=	$value;
		if ( $field->storage == 'standard' && isset( $config['storages'][$field->storage_table]->category_title ) ) {
			$field->text	=	$config['storages'][$field->storage_table]->category_title;
		} else {
			$field->text	=	JCckDatabase::loadResult( 'SELECT title FROM #__categories WHERE id = '.(int)$value ); // #
		}
		$field->typo_target	=	'text';
	}
	
	// onCCK_FieldPrepareContentDebug
	public function onCCK_FieldPrepareContentDebug( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Set
		$field->value		=	$value;
		$field->text		=	plgCCK_StorageLipsum::getLipsum( 2 );
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
		$app		=	JFactory::getApplication();
		$value		=	( $value != '' ) ? $value : $field->defaultvalue;
		$value		=	( $value != ' ' ) ? $value : '';
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		if ( parent::g_isStaticVariation( $field, $field->variation, true ) ) {
			$form			=	'';
			$field->text	=	'';
			parent::g_getDisplayVariation( $field, $field->variation, $value, $field->text, $form, $id, $name, '<select', '', '', $config );
		} elseif ( $field->variation == 'value' ) {
			$form			=	'';
			$field->text	=	JCckDatabase::loadResult( 'SELECT title FROM #__categories WHERE id = '.(int)$value );
			parent::g_getDisplayVariation( $field, $field->variation, $value, $field->text, $form, $id, $name, '<select', '', '', $config );
		} else {
			$opt		=	'';
			$options2	=	JCckDev::fromJSON( $field->options2 );
			if ( trim( $field->selectlabel ) ) {
				if ( $config['doTranslation'] ) {
					$field->selectlabel	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->selectlabel ) ) );
				}
				$opt	=	'<option value="'.( ( $field->storage_field == 'parent_id' && $config['client'] != 'search' ) ? 1 : '' ).'">'.'- '.$field->selectlabel.' -'.'</option>';
			}
			$multiple	=	( $field->bool3 == 1 ) ? 'multiple="multiple"' : '';
			$size		=	( $field->rows ) ? $field->rows : 1;
			$size		=	( (int)$size > 1 ) ? ' size="'.$size.'"' : '';
			$extension	=	$app->input->get( 'extension', @$options2['extension'] );
			$extension	=	( $extension ) ? $extension : 'com_content';
			
			$class	=	'inputbox select'.$validate . ( $field->css ? ' '.$field->css : '' );	
			$xml	=	'
						<form>
							<field
								type="'.self::$type2.'"
								name="'.$name.'"
								id="'.$id.'"
								label="'.htmlspecialchars( $field->label ).'"
								extension="'.$extension.'"
								'.$multiple.'
								class="'.$class.'"'.$size.'
							>'.$opt.'</field>
						</form>
					';			
			$form	=	JForm::getInstance( $id, $xml );

			if ( $config['client'] == 'admin' || $config['client'] == 'site' || $config['client'] == 'search' ) {
				if ( $config['pk'] ) {
					$form->setFieldAttribute( $name, 'action', 'core.edit' );
					$form->setFieldAttribute( $name, 'action', 'core.edit.own' );
				} else {
					$form->setFieldAttribute( $name, 'action', 'core.create' );
				}
			}
			$form_field		=	$form->getField( $name );
			$form_opts		=	$form_field->getOptionList();
			$form			=	$form->getInput( $name, '', $value );
			$field->options	=	'';

			if ( $field->attributes ) {
				$form	=	str_replace( '<select', '<select '.$field->attributes, $form );
			}
			if ( $form_opts ) {
				$options	=	array();

				foreach ( $form_opts as $opt ) {
					$options[]	=	$opt->text.'='.$opt->value;
				}

				$field->options	=	( count( $options ) ) ? implode( '||', $options ) : '';
			}
			
			// Set
			if ( ! $field->variation ) {
				$field->form	=	$form;
				
				if ( $field->options != '' ) {
					$field->text	=	parent::g_getOptionText( $value, $field->options, ( $config['client'] == 'search' ? ',' : '' ), $config );
				} else {
					$field->text	=	JCckDatabase::loadResult( 'SELECT title FROM #__categories WHERE id = '.(int)$value );
				}

				if ( $field->script ) {
					parent::g_addScriptDeclaration( $field->script );
				}
			} else {
				if ( $field->options != '' ) {
					$field->text	=	parent::g_getOptionText( $value, $field->options, ( $config['client'] == 'search' ? ',' : '' ), $config );
				} else {
					$field->text	=	JCckDatabase::loadResult( 'SELECT title FROM #__categories WHERE id = '.(int)$value );
				}

				parent::g_getDisplayVariation( $field, $field->variation, $value, $field->text, $form, $id, $name, '<select', '', '', $config );
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