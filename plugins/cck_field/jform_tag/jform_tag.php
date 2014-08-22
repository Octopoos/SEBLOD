<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_FieldJform_Tag extends JCckPluginField
{
	protected static $type		=	'jform_tag';
	protected static $type2		=	'tag';
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
		
		$html	=	'';

		if ( $value || ( $config['client'] == 'list' || $config['client'] == 'item'  ) ) {
			$location		=	( isset( $config['location'] ) && $config['location'] ) ? $config['location'] : 'joomla_article';
			$properties		=	array( 'context' );
			$properties		=	JCck::callFunc( 'plgCCK_Storage_Location'.$location, 'getStaticProperties', $properties );
			
			/* temporary fix for content categories */
			if ( $properties['context'] == 'com_categories.category' ) {
				$properties['context']	=	'com_content.category';	// todo: dynamic context per extension (#__categories)
			}
			/* temporary fix for content categories */
			
			if ( is_object( $value ) && isset( $value->tags ) ) {
				$value		=	$value->tags;
			}
			$tags			=	new JHelperTags;
			$tags->getItemTags( $properties['context'], $config['pk'] );
			$tagLayout		=	new JLayoutFile( 'joomla.content.tags' );
			$html			=	$tagLayout->render( $tags->itemTags );
		}
		
		// Set
		$field->value	=	$value;
		$field->html	=	$html;
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
		if ( JCck::on() ) {
			JHtml::_( 'formbehavior.chosen', 'select.tag' );
			
			$options2	=	JCckDev::fromJSON( $field->options2 );
			$class		=	'inputbox tag'.$validate . ( $field->css ? ' '.$field->css : '' );
			$mode		=	( isset( $options2['mode'] ) && $options2['mode'] ) ? 'mode="'.$options2['mode'].'"' : '';
			$custom		=	( isset( $options2['custom'] ) && !$options2['custom'] ) ? 'custom="deny"' : '';
			$xml		=	'
							<form>
								<field
									type="'.self::$type2.'"
									name="'.$name.'"
									id="'.$id.'"
									label="'.htmlspecialchars( $field->label ).'"
									class="'.$class.'"
									'.$mode.'
									'.$custom.'
									multiple="true"
								/>
							</form>
						';
			$form	=	JForm::getInstance( $id, $xml );
			$form	=	$form->getInput( $name, '', $value );
		} else {
			$form	=	'';
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