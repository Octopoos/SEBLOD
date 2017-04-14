<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cck.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JFormField
class JFormFieldCCK extends JFormField
{
	protected $type	=	'CCK';
	
	// getInput
	protected function getInput()
	{
		JPluginHelper::importPlugin( 'cck_field' );
		require_once JPATH_PLUGINS.'/cck_field_validation/required/required.php';
		
		$attributes		=	isset( $this->element['cck_attributes'] ) ? (string)$this->element['cck_attributes'] : '';
		$class			=	isset( $this->element['class'] ) ? (string)$this->element['class'] : '';
		$name			=	(string)$this->element['construction'];
		$name2			=	(string)$this->element['construction2'];
		$options		=	isset( $this->element['cck_options'] ) ? (string)$this->element['cck_options'] : '';
		$rows			=	isset( $this->element['rows'] ) ? (string)$this->element['rows'] : '';
		$suffix			=	(string)$this->element['more'] ? '<span class="variation_value">'.(string)$this->element['more'].'</span>' : '';
		$suffix2		=	(string)$this->element['more2'] ? '<span class="variation_value">'.(string)$this->element['more2'].'</span>' : '';
		$selectlabel	=	isset( $this->element['cck_selectlabel'] ) ? (string)$this->element['cck_selectlabel'] : 'undefined';
		
		if ( ! $name ) {
			return;
		}
		
		$format			=	(string)$this->element['js_format'];
		$lang  	 		=	JFactory::getLanguage();
		$lang->load( 'com_cck' );
		$lang->load( 'com_cck_default', JPATH_SITE );
		if ( $format != 'raw' ) {
			JCck::loadjQuery( true, true, array( 'cck.dev-3.7.0.min.js', 'jquery.json.min.js', 'jquery.ui.effects.min.js' ) );
		}
		
		$force_id		=	(string)$this->element['id'];
		$config			=	array( 'asset'=>'',
								   'asset_id'=>0,
								   'client'=>'',
								   'doTranslation'=>1,
								   'doValidation'=>2,
								   'pk'=>''
								);
		if ( $format == 'raw' ) {
			$config['tmpl']	=	'ajax';
		}
		$inherit		=	( $force_id != '' ) ? array( 'id' => (string)$this->element['id'] ) : array();
		
		$field					=	JCckDevField::getObject( $name );
		if ( ! $field ) {
			return;
		}
		$storage_field			=	$field->storage_field;
		$field->storage_field	=	$this->name;
		if ( $attributes != '' ) {
			$attributes	=	str_replace( "'", '"', $attributes );
			
			if ( $field->attributes ) {
				$field->attributes	.=	' '.$attributes;
			} else {
				$field->attributes	=	$attributes;
			}
		}
		if ( $options != '' ) {
			$field->options		=	$options;
		}
		if ( $selectlabel != 'undefined' ) {
			$field->selectlabel	=	$selectlabel;
		}
		if ( $class != '' ) {
			$field->css			=	$class;
		}
		if ( $rows != '' ) {
			$field->rows		=	$rows;
		}
		$field					=	JCckDevField::get( $field, $this->value, $config, $inherit );
		
		$more			=	'';
		if ( $name2 ) {
			$field2					=	JCckDevField::getObject( $name2 );
			$storage_field2			=	$field2->storage_field;
			$field2->storage_field	=	str_replace( $storage_field, $storage_field2, $this->name );
			$field2					=	JCckDevField::get( $field2, (string)$this->element['value2'], $config, $inherit );
			$more					=	$field2->form;
		}
		
		$script	=	$this->_addScripts( $this->id, array(
													'appendTo'=>(string)$this->element['js_appendto'],
													'isVisibleWhen'=>(string)$this->element['js_isvisiblewhen'],
													'isDisabledWhen'=>(string)$this->element['js_isdisabledwhen'],
													'replaceHtml'=>(string)$this->element['js_replacehtml']
												   ), $format );
		
		return $field->form.$suffix.$more.$suffix2.$script;
	}
	
	// _addScripts
	protected function _addScripts( $id, $events, $format )
	{
		$doc	=	JFactory::getDocument();
		$js		=	'';		
		$js2	=	'';
		$js3	=	'';
		$js4	=	'';
		
		// appendTo
		if ( $events['appendTo'] ) {
			$e	=	explode( '=', $events['appendTo'] );
			if ( isset( $e[1] ) ) {
				$fragments	=	explode( ',', $e[1] );
				if ( count( $fragments ) ) {
					foreach ( $fragments as $fragment ) {
						$fragment	=	trim( $fragment );
						if ( strpos( $fragment, 'J(' ) !== false ) {
							$fragment		=	substr( $fragment, 2, -1 );
							if ( strpos( $fragment, '|' ) !== false ) {
								$frag		=	explode( '|', $fragment );
								$frag[1]	=	' '.$frag[1];
							} else {
								$frag		=	array( 0=>$fragment, 1=>'' );
							}
							$frag[0]	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', $frag[0] ) );
							$f			=	'<div class=\'textdesc'.$frag[1].'\'>'.$frag[0].'</div>';
							$js			.=	'.append("'.$f.'")';
						} else {
							$js			.=	'.append($("'.$fragment.'"))';
						}
					}
				}
				$js	=	'if ($("#'.$e[0].'")) { $("#'.$e[0].'").parent().append("<div id=\"'.$id.'-more\"></div>"); $("div#'.$id.'-more")'.$js.'; }';
			} else {
				$js	.=	'if ($("#'.$e[0].'")) { $("#'.$e[0].'").parent().append("<div id=\"'.$id.'-more\"></div>"); $("div#'.$id.'-more").append($("#'.$id.'")); }';
			}
			$js	.=	'if ($("#'.$id.'-lbl")) { $("#'.$id.'-lbl").parent().remove(); }';
		}
		
		// isVisibleWhen
		if ( $events['isVisibleWhen'] ) {
			$e	=	explode( '=', $events['isVisibleWhen'] );
			if ( isset( $e[1] ) ) {
				$js2	=	'$("'.$e[0].'").isVisibleWhen('.$e[1].');';
			} else {
				$js2	=	'$("#'.$id.'").isVisibleWhen('.$e[0].');';
			}
		}
		
		// isDisabledWhen
		if ( $events['isDisabledWhen'] ) {
			$e	=	explode( '=', $events['isDisabledWhen'] );
			if ( isset( $e[1] ) ) {
				$js3	=	'$("'.$e[0].'").isDisabledWhen('.$e[1].');';
			} else {
				$js3	=	'$("#'.$id.'").isDisabledWhen('.$e[0].');';
			}
		}
		
		// replaceHtml
		if ( $events['replaceHtml'] ) {
			$js4	=	'$("#'.$id.'").on( "focus", function(ev){ev.preventDefault(); var v = $(this).myVal(); v = v.replace(/\[\[/g, "<"); v = v.replace(/\]\]/g, ">"); $(this).val(v); });';
			$js4	.=	'$("#'.$id.'").on( "blur", function(ev){ev.preventDefault(); var v = $(this).myVal(); v = v.replace(/\</g, "[["); v = v.replace(/\>/g, "]]"); $(this).val(v); });'; // v.replace(/</g, "[[");
		}

		// Set
		if ( $js || $js2 || $js3 || $js4 ) {
			$js	=	'jQuery(document).ready(function($){'.$js.' '.$js2.' '.$js3.$js4.'});';
			
			if ( $format == 'raw' ) {
				return '<script type="text/javascript">'.$js.'</script>';
			} else {
				$doc->addScriptDeclaration( $js );
			}
		}
		
		return;
	}
}
?>