<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_FieldJform_Associations extends JCckPluginField
{
	protected static $type		=	'jform_associations';
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
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$app	=	JFactory::getApplication();
		$assoc	=	isset( $app->item_associations ) ? $app->item_associations : 0;
		$form	=	'';

		if ( $assoc && $config['pk'] ) {
			$languages	=	JLanguageHelper::getLanguages( 'lang_code' );

			// Create Form
			$addform	=	new SimpleXMLElement( '<form />' );
			$fields		=	$addform->addChild( 'fields' );
			$fields->addAttribute( 'name', $name );
			$fieldset	=	$fields->addChild( 'fieldset' );
			$fieldset->addAttribute( 'name', 'item_associations' );
			$fieldset->addAttribute( 'description', 'COM_CONTENT_ITEM_ASSOCIATIONS_FIELDSET_DESC' );
			$fieldset->addAttribute( 'addfieldpath', '/administrator/components/com_content/models/fields' );
			$hasForm	=	false;
			foreach ( $languages as $tag=>$language ) {
				if ( empty( $config['language'] ) || $tag != $config['language'] ) {
					$hasForm	=	true;
					$f			=	$fieldset->addChild( 'field' );
					$f->addAttribute( 'name', $tag );
					$f->addAttribute( 'type', 'modal_article' );
					$f->addAttribute( 'language', $tag );
					$f->addAttribute( 'label', $language->title );
					$f->addAttribute( 'translate_label', 'false' );
				}
			}
			$form	=	JForm::getInstance( $id, $addform->asXML() );
			if ( $hasForm ) {
				$form->load( $addform, false );
				$associations	=	JLanguageAssociations::getAssociations( 'com_content', '#__content', 'com_content.item', $config['pk'] );
				if ( count( $associations ) ) {
					foreach ( $associations as $tag=>$association ) {
						$form->setValue( $tag, $name, $association->id );
					}
				}
				if ( $config['translate_id'] && isset( $config['translate'] ) ) {
					$form->setValue( $config['translate'], $name, $config['translate_id'] );
				}
			}
			
			// Render Form
			$fields	=	$form->getFieldset( 'item_associations' );
			$form	=	'';
			foreach ( $fields as $f ) {
				$form	.=	'<div class="control-group"><div class="control-label">'.$f->label.'</div><div class="controls">'.$f->input.'</div></div>';
			}
		}

		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			//
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
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
}
?>