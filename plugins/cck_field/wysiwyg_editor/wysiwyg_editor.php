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
class plgCCK_FieldWysiwyg_editor extends JCckPluginField
{
	protected static $type		=	'wysiwyg_editor';
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
		
		$data['defaultvalue']	=	JRequest::getVar( 'defaultvalue', '', '', 'string', JREQUEST_ALLOWRAW );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
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
			$id			=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name		=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
			$inherited	=	true;
		} else {
			$id			=	$field->name;
			$name		=	$field->name;
			$inherited	=	false;
		}
		$value		=	( $value != '' ) ? htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' ) : @$field->defaultvalue;
		$value		=	( $value != ' ' ) ? $value : '';
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$app		=	JFactory::getApplication();
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$user		=	JFactory::getUser();
		if ( $config['pk'] && @$options2['import'] && $field->storage_location ) {
			if ( ! JCckDatabase::loadResult( 'SELECT pk FROM #__cck_core WHERE pk='.(int)$config['pk'].' AND storage_location="'.(string)$field->storage_location.'"' ) ) {
				$properties	=	array( 'custom', 'table' );
				$properties	=	JCck::callFunc( 'plgCCK_Storage_Location'.$field->storage_location, 'getStaticProperties', $properties );
				$custom		=	( $options2['import'] == 2 ) ? 'fulltext' : $properties['custom'];
				$value		=	$config['storages'][$properties['table']]->$custom;
			}
		}

		if ( !$user->id && $this->params->get( 'guest_access', 0 ) == 0 ) {
			$form	=	'';
		} else {
			$width				=	@$options2['width'] ? str_replace( 'px', '', $options2['width'] ) : '100%';
			$height				=	@$options2['height'] ? str_replace( 'px', '', $options2['height'] ) : '280';
			$asset				=	( $config['asset_id'] > 0 ) ? $config['asset_id'] : $config['asset'];

			if ( $app->input->get( 'option' ) == 'com_cck' && $app->input->get( 'view' ) == 'form' && $config['client'] == '' ) {
				$field->bool	=	1;
			}
			if ( $field->bool ) {
				// Default
				$buttons		=	( $field->bool4 ) ? array( 'pagebreak', 'readmore' ) : false;
				$editor			=	JFactory::getEditor( @$options2['editor'] ? $options2['editor'] : null );
				$form			=	'<div>'.$editor->display( $name, $value, $width, $height, '60', '20', $buttons, $id, $asset ).'</div>';

				JFactory::getDocument()->addStyleDeclaration('.mce-tinymce:not(.mce-fullscreen) #'.$id.'_ifr{min-height:'.((int)$height - 58).'px; max-height:'.((int)$height - 58).'px;}');
			} else {
				// Modal Box
				if ( trim( $field->selectlabel ) ) {
					if ( $config['doTranslation'] ) {
						$field->selectlabel	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->selectlabel ) ) );
					}
					$buttonlabel	=	$field->selectlabel;
				} else {
					$buttonlabel	=	JText::_( 'COM_CCK_EDITOR' );
				}
				
				$e_type					=	( @$options2['editor'] != '' ) ? '&type='.$options2['editor'] : '';
				$link					=	'index.php?option=com_cck&task=box.add&tmpl=component&file=plugins/cck_field/'.self::$type.'/tmpl/form.php'
										.	'&id='.$id.'&name='.$name.$e_type.'&params='.urlencode( urlencode( $width ) ).'||'.$height.'||'.$asset.'||'.$field->bool4;
				
				$app					=	JFactory::getApplication();
				$class					=	'wysiwyg_editor_box variation_href';

				$component				=	$app->input->get( 'option' );
				if ( ( $component == 'com_cck' && $app->input->get( 'view' ) != 'form' )
					|| $component == 'com_cck_ecommerce' || $component == 'com_cck_toolbox' || $component == 'com_cck_webservices' ) { // todo: remove later
					$class				.=	' btn';
				}
				$class					=	'class="'.trim( $class ).'" ';
				$attr					=	$class;
				$form					=	'<textarea style="display: none;" id="'.$id.'" name="'.$name.'">'.$value.'</textarea>';
				$form					.=	'<a href="'.$link.'" '.$attr.'>'.$buttonlabel.'</a>';
				$field->markup_class	.=	' cck_form_wysiwyg_editor_box';
			}
		}
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;

			self::_addScripts( $field->bool, array( 'height'=>@$height, 'inherited'=>$inherited ), $config );
		} else {
			$hidden	=	'<textarea class="inputbox" style="display: none;" id="'.$id.'" name="'.$name.'" />'.$value.'</textarea>';
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<textarea', $hidden, '', $config );
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
		
		// Init
		$field->type	=	'text';

		// Prepare
		$results		=	JEventDispatcher::getInstance()->trigger( 'onCCK_FieldPrepareSearch', array( &$field, $value, &$config, array(), true ) );
		if ( is_array( $results ) && !empty( $results[0] ) ) {
			$field		=	$results[0];
		}

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
			$value	=	JRequest::getVar( $name, '', 'post', 'string', JREQUEST_ALLOWRAW );
		}
		
		// Make it safe
		$value		=	JComponentHelper::filterText( $value );
		
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
		$doc	=	JFactory::getDocument();
		
		$doc->addStyleSheet( self::$path.'assets/css/cck_wysiwyg_editor.css' );
		
		if ( !$inline ) {
			static $loaded	=	0;
			$root			=	JUri::root( true );

			if ( !$loaded ) {
				if ( empty( $config['client'] ) ) {
					$js	=	' $(document).on("click", ".wysiwyg_editor_box", function(e) { e.preventDefault();'
						.	' $.colorbox({href:$(this).attr(\'href\'), open:true, iframe:true, innerWidth:820, innerHeight:420, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $("#cboxClose").remove();}}); return false; });';

					if ( !( isset( $config['tmpl'] ) && $config['tmpl'] == 'ajax' ) ) {
						$doc->addScript( $root.'/media/cck/scripts/jquery-colorbox/js/jquery.colorbox-min.js' );

						$js	=	'$(document).ready(function() {'.$js.'});';
					}
					$doc->addStyleSheet( $root.'/media/cck/scripts/jquery-colorbox/css/colorbox.css' );
					$doc->addScriptDeclaration( '(function ($){'.$js.'})(jQuery);' );
				} elseif ( $params['inherited'] == true ) {
					JCck::loadModalBox();
					$js	=	' $(document).on("click", ".wysiwyg_editor_box", function(e) { e.preventDefault();'
						.	' $.colorbox({href:$(this).attr(\'href\'), open:true, iframe:true, innerWidth:820, innerHeight:420, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $("#cboxClose").remove();}}); return false; });';
					$js	=	'$(document).ready(function() {'.$js.'});';
					$doc->addScriptDeclaration( '(function ($){'.$js.'})(jQuery);' );
				} else {
					JCck::loadModalBox();
					$js	=	'jQuery(document).ready(function($){ $(".wysiwyg_editor_box").colorbox({iframe:true, innerWidth:820, innerHeight:420, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){$("#cboxClose").remove();}}); });';
					$doc->addScriptDeclaration( $js );
				}
				$loaded		=	1;
			}
		}
	}
}
?>