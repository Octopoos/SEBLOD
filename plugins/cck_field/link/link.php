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
class plgCCK_FieldLink extends JCckPluginField
{
	protected static $type		=	'link';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		if ( !@$data['json']['options2']['link_label'] ) {
			$data['json']['options2']['link_label']	=	' ';
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
		
		// Prepare
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Set
		$field->value	=	$value;	
		$field->html	=	'';
		
		// Set More
		if ( $value != '' ) {
			$options2		=	JCckDev::fromJSON( $field->options2 );
			$default_link	=	( @$options2['def_link'] != '' ) ? $options2['def_link'] : '';
			$default_text	=	( @$options2['def_text'] != '' ) ? $options2['def_text'] : '';
			$default_class	=	( @$options2['class'] != '' ) ? $options2['class'] : '';
			$default_target	=	( @$options2['target'] != '' ) ? $options2['target'] : '';
			$default_rel	=	( @$options2['rel'] != '' ) ? $options2['rel'] : '';
	
			$value		=	JCckDev::fromJSON( $value );
			$link		=	( @$value['link'] != '' ) ? $value['link'] : $default_link;
			$text		=	( @$value['text'] != '' ) ? $value['text'] : ( ( $default_text ) ? $default_text : $link );	
			$class		=	( @$value['class'] != '' ) ? $value['class'] : $default_class;	
			$target		=	( @$value['target'] != '' ) ? $value['target'] : $default_target;
			$rel		=	( @$value['rel'] != '' ) ? $value['rel'] : $default_rel;
			$extension	=	array( '.png', '.PNG', '.jpg', '.JPG', '.gif', '.GIF' );

			foreach ( $extension as $ext ){
				$text	=	( strpos($text, $ext ) !== false ) ? '<img src="'.JUri::base().$text.'" title="'.$link.'" />' : $text;
			}

			$field->text		=	$text;
			$field->link		=	( strpos( $link, 'index.php' ) === 0 || $link[0] == '/' ) ? $link : ( ( strpos( $link, 'http://' ) === false && strpos( $link, 'https://' ) === false ) ? 'http://'.$link : $link );
			$field->link_target	=	$target;
			$field->link_class	=	$class;
			$field->link_rel	=	$rel;
			$field->linked		=	true;
			$class				=	( $class != '' ) ? 'class="'.$class.'" ' : '';
			$rel				=	( $rel != '' ) ? ' rel="'.$rel.'"' : '';
			$field->html		=	'<a href="'.$field->link.'" '.$class.'target="'.$field->link_target.'"'.$rel.'>'.$field->text.'</a>';
			$field->typo_target	=	'text';
		}
	}
	
	// onCCK_FieldPrepareExport
	public function onCCK_FieldPrepareExport( &$field, $value = '', &$config = array() )
	{
		if ( static::$type != $field->type ) {
			return;
		}
		
		self::onCCK_FieldPrepareContent( $field, $value, $config );
		
		$field->output	=	$field->link;
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
		$value			=	( $value != '' ) ? $value : $field->defaultvalue;
		$value			=	( $value != ' ' ) ? $value : '';
		$value			=	JCckDev::fromJSON( $value );
		$value['text']	=	htmlspecialchars( @$value['text'], ENT_QUOTES );
		$preview		=	'';
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config, array( 'minSize'=>true ) );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$class	=	'inputbox text'.$validate . ( $field->css ? ' '.$field->css : '' );
		$class2	=	'inputbox text';
		$maxlen	=	( $field->maxlength > 0 ) ? ' maxlength="'.$field->maxlength.'"' : '';
		$attr	=	'class="'.$class.'" size="'.$field->size.'"'.$maxlen . ( $field->attributes ? ' '.$field->attributes : '' );
		$attr2	=	'class="'.$class2.'" size="'.$field->size.'"'.$maxlen;
		
		$options2	=	JCckDev::fromJSON( $field->options2 );
		if ( $config['doTranslation'] ) {
			$link_label		=	trim( @$options2['link_label'] ) ? JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( @$options2['link_label'] ) ) ) : '';
			$text_label		=	trim( @$options2['text_label'] ) ? JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( @$options2['text_label'] ) ) ) : '';
		} else {
			$link_label		=	( @$options2['link_label'] != '' ) ? trim( $options2['link_label'] ) : '';
			$text_label		=	( @$options2['text_label'] != '' ) ? $options2['text_label'] : '';
		}
		
		if ( strpos( $name, '[]' ) !== false ) { //FieldX
			$nameH		=	substr( $name, 0, -2 );
			$nameLink	=	$name;
			$nameText	=	$nameH.'_text[]';
			$nameClass	=	$nameH.'_class[]';
			$nameTarget	=	$nameH.'_target[]';
		} elseif ( $name[(strlen($name) - 1 )] == ']' ) { //GroupX
			$nameH		=	substr( $name, 0, -1 );
			$nameLink	=	$name;
			$nameText	=	$nameH.'_text]';
			$nameClass	=	$nameH.'_class]';
			$nameTarget	=	$nameH.'_target]';
		} else { //Default
			$nameH		=	$name;
			$nameLink	=	$name;
			$nameText	=	$nameH.'_text';
			$nameClass	=	$nameH.'_class';
			$nameTarget	=	$nameH.'_target';
		}
		
		$form		=	self::_addInput( $id, $nameLink, $attr, $link_label, @$value['link'], 'link' );

		if ( $field->bool2 == 1 ) {
			$form	.=	self::_addInput( $id.'_text', $nameText, $attr2, $text_label, @$value['text'], 'text' );
		}

		if ( $field->bool3 == 1 ) {
			$form	.=	self::_addInput( $id.'_class', $nameClass, $attr2, JText::_( 'COM_CCK_CLASS' ), @$value['class'], 'class' );
		}

		if ( $field->bool4 == 1 ) {
			$form	.=	self::_addSelect( $id.'_target', JText::_( 'COM_CCK_TARGET' ), 'target', 'core_options_target', @$value['target'], array( 'client'=>$config['client'], 'doTranslation'=>1, 'doValidation'=>2 ), array( 'storage_field'=>$nameTarget ) );
		}

		if ( $field->bool5 == 1 && $config['pk'] && @$value['link'] != '' ) {
			$p_link		=	@$value['link'];
			$p_text		=	( @$value['text'] ) ? @$value['text'] : $p_link;
			$p_link		=	( strpos( $p_link, 'index.php' ) === 0 ) ? $p_link : ( ( strpos( $p_link, 'http://' ) === false && strpos( $p_link, 'https://' ) === false ) ? 'http://'.$p_link : $p_link );

			$preview	=	'<a href="'.$p_link.'" class="cck_preview" target="_blank">'.$p_text.'</a>';
			
			$preview	=	self::_addPreview( $id.'_preview', JText::_( 'COM_CCK_PREVIEW' ), $preview, 'preview' );
		}

		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form.$preview;
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
			$xk			=	( isset( $inherit['xk'] ) ) ? $inherit['xk'] : -1;
			$name		=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
			$itemLink	=	( isset( $inherit['post'] ) ) ? $inherit['post'][$name] : @$config['post'][$name][$xk];
			$itemText	=	( isset( $inherit['post'] ) ) ? @$inherit['post'][$name.'_text'] : @$config['post'][$name.'_text'][$xk];
			$itemClass	=	( isset( $inherit['post'] ) ) ? @$inherit['post'][$name.'_class'] : @$config['post'][$name.'_class'][$xk];
			$itemTarget	=	( isset( $inherit['post'] ) ) ? @$inherit['post'][$name.'_target'] : @$config['post'][$name.'_target'][$xk];
		} else {
			$name		=	$field->name;
			$itemLink	=	@$config['post'][$name];
			$itemText	=	@$config['post'][$name.'_text'];
			$itemClass	=	@$config['post'][$name.'_class'];
			$itemTarget	=	@$config['post'][$name.'_target'];
		}
		
		// Validate
		$value	=	$itemLink;
		$value	=	str_replace( array( '<', '>', '"' ), '', $value );
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );

		$value	=	array_filter( array( 'link'=>$itemLink, 'text'=>$itemText, 'class'=>$itemClass, 'target'=>$itemTarget ) );
		if ( count( $value ) > 0 && ( $value['link'] || $value['text'] ) ) {
			$value	=	JCckDev::toJSON( $value );
		} else {
			$value	=	NULL;
		}
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

	// _addInput
	protected static function _addInput( $id, $name, $attr, $label, $value, $suffix )
	{
		if ( $label == '' ) {
			$form	=	'<input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />';
		} else {
			$form	=	'<div class="cck_forms cck_link_'.$suffix.'">';
			$form	.=	'<div class="cck_label cck_label_link_'.$suffix.'"><label for="'.$id.'" >'.$label.'</label></div>';
			$form	.=	'<div class="cck_form cck_form_link_'.$suffix.'"><input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' /></div>';
			$form	.=	'</div>';
		}
		
		return $form;
	}

	// _addSelect
	protected static function _addSelect( $id, $label, $suffix, $field, $value, $config, $array = array() )
	{
		$form	=	'<div class="cck_forms cck_link_'.$suffix.'">';
		$form	.=	'<div class="cck_label cck_label_link_'.$suffix.'"><label for="'.$id.'" >'.$label.'</label></div>';
		$form	.=	'<div class="cck_form cck_form_link_'.$suffix.'">';
		$form	.=	JCckDev::getForm( $field, $value, $config, $array );
		$form	.=	'</div></div>';

		return $form;
	}

	// _addPreview
	protected static function _addPreview( $id, $label, $preview, $suffix )
	{
		$form	=	'<div class="cck_forms cck_link_'.$suffix.'">';
		$form	.=	'<div class="cck_label cck_label_link_'.$suffix.'"><label for="'.$id.'" >'.$label.'</label></div>';
		$form	.=	'<div class="cck_form cck_form_link_'.$suffix.'">'.$preview.'</div>';
		$form	.=	'</div>';
		
		return $form;
	}
}
?>