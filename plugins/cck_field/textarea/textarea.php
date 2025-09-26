<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_FieldTextarea extends JCckPluginField
{
	protected static $type		=	'textarea';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
		
		$data['defaultvalue']	=	JFactory::getApplication()->input->post->get( 'defaultvalue', '', 'raw' );
	}
	
	// onCCK_FieldConstruct_TypeForm
	public static function onCCK_FieldConstruct_TypeForm( &$field, $style, $data = array(), &$config = array() )
	{
		if ( !isset( $config['construction']['variation'][self::$type] ) ) {
			$data['variation']['201']			=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_VALUE_CUSTOM' ) );
			$data['variation']['custom_code']	=	JHtml::_( 'select.option', 'custom_code', JText::_( 'COM_CCK_CODE' ) );
			$data['variation']['202']			=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
			$data['variation']['203']			=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_STAR_IS_SECURED' ) );
			$data['variation']['204']			=	JHtml::_( 'select.option', '</OPTGROUP>', '' );

			$config['construction']['variation'][self::$type]	=	$data['variation'];
		} else {
			$data['variation']									=	$config['construction']['variation'][self::$type];
		}
		parent::onCCK_FieldConstruct_TypeForm( $field, $style, $data, $config );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		$value			=	( $field->bool3 ) ? self::_bn2clear( $value ) : $value;

		if ( $value ) {
			if ( (int)$field->bool2 != -1 ) {
				$value		=	( $field->bool2 ) ? ( ( $field->bool2 == 2 ) ? self::_bn2br_in_p( $value ) : self::_bn2p( $value ) ) : self::_bn2br( $value, true );
			}
		}
		
		$field->value	=	$value;
	}

	// onCCK_FieldPrepareExport
	public function onCCK_FieldPrepareExport( &$field, $value = '', &$config = array() )
	{
		if ( static::$type != $field->type ) {
			return;
		}
		
		if ( $this->params->get( 'export_prepare_output', '' ) == 0 ) {
			$field->output	=	$value;
		} else {
			self::onCCK_FieldPrepareContent( $field, $value, $config );
			
			$field->output	=	strip_tags( $field->value );
		}
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
		$value		=	( $value != '' ) ? ( ( $field->bool2 ) ? self::_p2nl( $value ) : self::_br2nl( $value ) ) : $field->defaultvalue;
		$value		=	( $value != ' ' ) ? $value : '';
		
		if ( $field->variation ==  'custom_code' ) {
			// $value			=	'1';
			$field->form	=	'<pre>'.htmlentities( $value ).'</pre>';
		} else {
			// Validate
			$validate	=	'';
			if ( $config['doValidation'] > 1 ) {
				plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
				parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config, array( 'minSize'=>true, 'maxSize'=>true ) );
				$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
			}
			
			// Prepare
			$class	=	'form-control inputbox textarea'.$validate . ( $field->css ? ' '.$field->css : '' );
			$cols	=	( $field->cols ) ? $field->cols : 25;
			$rows	=	( $field->rows ) ? $field->rows : 3;
			$maxlen	=	( $field->maxlength > 0 ) ? ' maxlength="'.$field->maxlength.'"' : '';
			$attr	=	'class="'.$class.'"'.$maxlen;

			if ( $field->attributes != '' ) {
				$attr	.=	' '.$field->attributes;
			}
			$form	= 	'<textarea id="'.$id.'" name="'.$name.'" cols="'.$cols.'" rows="'.$rows.'" '.$attr.'>'.$value.'</textarea>';
			$form 	.=	( $field->bool4 ) ? self::_checkRemaingCharacters( $id, $field->maxlength ) : '';
			
			// Set
			if ( ! $field->variation ) {
				$field->form	=	$form;
				if ( $field->script ) {
					parent::g_addScriptDeclaration( $field->script );
				}
			} else {
				$hidden	=	'<textarea class="inputbox" style="display: none;" id="_'.$id.'" name="'.$name.'" />'.$value.'</textarea>';
				parent::g_getDisplayVariation( $field, $field->variation, $value, self::_bn2br( self::_bn2clear( $value ) ), $form, $id, $name, '<textarea', $hidden, '', $config );
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
			$value	=	JFactory::getApplication()->input->post->get( $name, '', 'raw' );
		}
		
		// Make it safe
		if ( $field->bool7 ) {
			$value	=	JComponentHelper::filterText( $value );
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
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _checkRemaingCharacters
	protected static function _checkRemaingCharacters( $id, $length = 0 )
	{
		if ( !$length ) {
			return '';
		}

		$js	=	'$("#'.$id.'").keyup(function() {
					if ( $(this).attr("maxlength") != "undefined" ) {
							$("#chars-'.$id.' span").html($(this).attr("maxlength")-$(this).val().length);
					}
				}).trigger("keyup");';

		JFactory::getDocument()->addScriptDeclaration( 'jQuery(document).ready(function($) {'.$js.'});' );
		
		return '<div id="chars-'.$id.'" class="remaining-chars">'.JText::sprintf( 'COM_CCK_N_CHARACTERS_REMAINING', $length ).'</div>';
	}

	// _br2nl
	protected static function _br2nl( $text )
	{
		return preg_replace( '/\<br(\s*)?\/?\>/i', "\n", $text );
	}

	// _bn2br
	protected static function _bn2br( $text, $clean = false )
	{
		if ( $clean ) {
			$text	=	preg_replace('/>\s+</', '><', $text );
		}

		return preg_replace( '/\\n/i', "<br />", $text );
	}

	// _p2nl
	protected static function _p2nl( $text )
	{
		return preg_replace( '/\<p\>\<\/p\>/i', "\n", $text );
	}

	// _bn2p
	protected static function _bn2p( $text )
	{
		return preg_replace( '/\\n/i', "</p><p>", '<p>'.$text.'</p>' );
	}

	// _bn2p_br
	protected static function _bn2br_in_p( $text )
	{
		return preg_replace( '/\\n/i', "<br />", '<p>'.$text.'</p>' );
	}

	// _bn2clear
	protected static function _bn2clear( $text )
	{
		return preg_replace( '/\\n\\r/i', '', $text );
	}
}
?>
