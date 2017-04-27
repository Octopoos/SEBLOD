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
class plgCCK_FieldJForm_Rules extends JCckPluginField
{
	protected static $type		=	'jform_rules';
	protected static $type2		=	'rules';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		if ( $data['storage'] != 'dev' ) {
			$data['bool']	=	'1';
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
			$id			=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name		=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
			$inherited	=	true;
		} else {
			$id			=	$field->name;
			$name		=	$field->name;
			$inherited	=	false;
		}
		$value		=	( $value != '' ) ? (int)$value : @(int)$config['asset_id'];
		
		// Validate
		$validate	=	'';
		
		// Prepare
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$component	=	@$options2['extension'] ? $options2['extension'] : 'com_content';
		$section	=	@$options2['section'] ? $options2['section'] : 'article';
						
		if ( $field->bool ) {
			// Default
			$class	=	'inputbox select'.$validate . ( $field->css ? ' '.$field->css : '' );
			$xml	=	'
						<form>
							<field
								type="'.self::$type2.'"
								id="'.$id.'"
								name="'.$name.'"
								label="'.htmlspecialchars( $field->label ).'"
								filter="rules"
								component="'.$component.'"
								section="'.$section.'"
								class="'.$class.'"
							/>
							<field
								type="hidden"
								name="asset_id"
								readonly="true"
								class="readonly"
							/>
						</form>
					';
			$form	=	JForm::getInstance( $id, $xml );
			$form->setValue( 'asset_id', null, $value );
			$form	=	$form->getInput( $name );
			$form	=	str_replace( 'onchange="sendPermissions.call(this, event)"', '', $form );
		} else {
			// Modal Box
			$app			=	JFactory::getApplication();
			if ( trim( $field->selectlabel ) ) {
				if ( $config['doTranslation'] ) {
					$field->selectlabel	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->selectlabel ) ) );
				}
				$buttonlabel	=	$field->selectlabel;
			} else {
				$buttonlabel	=	JText::_( 'COM_CCK_PERMISSIONS' );
			}
			
			$link					=	'index.php?option=com_cck&task=box.add&tmpl=component&file=plugins/cck_field/'.self::$type.'/tmpl/form.php'
									.	'&id='.$id.'&name='.$name.'&type='.$value.'&params='.$component.'||'.$section;
			
			$class					=	'jform_rules_box variation_href';
			if ( $app->input->get( 'option' ) == 'com_cck' && $app->input->get( 'view' ) != 'form' ) { // todo: remove later
					$class			.=	' btn';
			}
			$class					=	'class="'.$class.'" ';
			$attr					=	$class;
			$rules					=	'';
			$form					=	'<textarea style="display: none;" id="'.$id.'" name="'.$name.'">'.$rules.'</textarea>';
			$form					.=	'<a href="'.$link.'" '.$attr.'>'.$buttonlabel.'</a>';
			$field->markup_class	.=	' cck_form_wysiwyg_editor_box';
		}
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			self::_addScripts( $field->bool, array( 'inherited'=>$inherited ), $config );
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<select', '', '', $config );
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
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _addScript
	protected static function _addScripts( $inline, $params = array(), &$config = array() )
	{
		if ( !$inline ) {
			$doc	=	JFactory::getDocument();
			$root	=	JUri::root( true );

			if ( empty( $config['client'] ) ) {
				$js	=	' $(document).on("click", ".'.self::$type.'_box", function(e) { e.preventDefault();'
					.	' $.colorbox({href:$(this).attr(\'href\'), open:true, iframe:true, innerWidth:820, innerHeight:550, scrolling:true, overlayClose:false, fixed:true, onLoad: function(){ $("#cboxClose").remove();}}); return false; });';

				if ( !( isset( $config['tmpl'] ) && $config['tmpl'] == 'ajax' ) ) {
					$doc->addScript( $root.'/media/cck/scripts/jquery-colorbox/js/jquery.colorbox-min.js' );

					$js	=	'$(document).ready(function() {'.$js.'});';
				}
				$doc->addStyleSheet( $root.'/media/cck/scripts/jquery-colorbox/css/colorbox.css' );
				$doc->addScriptDeclaration( '(function ($){'.$js.'})(jQuery);' );
			} elseif ( $params['inherited'] == true ) {
				JCck::loadModalBox();
				$js	=	' $(document).on("click", ".'.self::$type.'_box", function(e) { e.preventDefault();'
					.	' $.colorbox({href:$(this).attr(\'href\'), open:true, iframe:true, innerWidth:820, innerHeight:440, scrolling:true, overlayClose:false, fixed:true, onLoad: function(){ $("#cboxClose").remove();}}); return false; });';
				$js	=	'$(document).ready(function() {'.$js.'});';
				$doc->addScriptDeclaration( '(function ($){'.$js.'})(jQuery);' );
			} else {
				JCck::loadModalBox();
				$js	=	'jQuery(document).ready(function($){ $(".'.self::$type.'_box").colorbox({iframe:true, innerWidth:820, innerHeight:440, scrolling:true, overlayClose:true, fixed:true, onLoad: function(){$("#cboxClose").remove();}}); });';
				$doc->addScriptDeclaration( $js );
			}
		}
	}
}
?>