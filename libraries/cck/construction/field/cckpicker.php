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
class JFormFieldCckPicker extends JFormField
{
	protected $type	=	'CckPicker';
	
	// getInput
	protected function getInput()
	{
		JPluginHelper::importPlugin( 'cck_field' );
		require_once JPATH_PLUGINS.'/cck_field_validation/required/required.php';
		
		
		
		$name		=	'core_options';
		$override	=	array( 'rows'=>1 );
		$storage	=	(string)$this->element['cck_storage_field_prefix'];
		$and		=	( $storage != '' ) ? ' AND a.storage_field LIKE "'.$storage.'%"' : '';
		$fields		=	JCckDatabase::loadObjectList( 'SELECT a.title as text, a.name as value FROM #__cck_core_fields AS a'
													. ' WHERE a.storage = "dev" AND a.id > 500'.$and.' ORDER BY text' );
		$fields		=	is_array( $fields ) ? array_merge( array( JHtml::_( 'select.option', '', '- '.JText::_( 'COM_CCK_ADD_A_FIELD' ).' -' ) ), $fields ) : array();
		$html		=	JHtml::_( 'select.genericlist', $fields, 'fields_list', 'size="1" class="inputbox select" style="max-width:175px;"', 'value', 'text', '', 'fields_list' );
		
		$format		=	(string)$this->element['js_format'];
		$lang  	 	=	JFactory::getLanguage();
		$lang->load( 'com_cck' );
		$lang->load( 'com_cck_default', JPATH_SITE );
		if ( $format != 'raw' ) {
			JCck::loadjQuery( true, true, true );
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
		$inherit	=	( $force_id != '' ) ? array( 'id' => (string)$this->element['id'] ) : array();

		$field		=	JCckDevField::getObject( $name );
		if ( ! $field ) {
			return;
		}
		$storage_field			=	$field->storage_field;
		$field->storage_field	=	$this->name;
		$field					=	JCckDevField::get( $field, $this->value, $config, $inherit, $override );
		$script					=	$this->_addScripts( (string)$this->element['name'], array( 'value'=>$this->value ), $format );
		
		return $field->form.$html.$script;
	}
	
	// _addScripts
	protected function _addScripts( $id, $params, $format )
	{
		$doc	=	JFactory::getDocument();
		$css	=	'';
		$js		=	'';
		$js2	=	'';

		// Prepare
		$css	.=	'.button-add{display:none;}';
		$values	=	'';
		if ( is_array( $this->value ) && count( $this->value ) == 1 && $this->value[0] == '' ) {
			$this->value	=	'';
		}
		if ( !is_array( $this->value ) ) {
			$css	.=	'#collection-group-wrap-core_options__0{display:none;}';
			$js2	=	'var len = $("#sortable_core_options > div").length; if (len == 2) { $("#collection-group-wrap-core_options__0").remove(); }';
		} else {
			$values	=	implode( '","', $this->value );
		}
		$js		.=	'var cur = 9999; var values = ["'.$values.'"];
					$.fn.JCckFieldxDelBefore = function() {
						var $el = $(this).find("input");
						if ($el.length){
							var v = $el.myVal();
							if (v) {
								var idx = $.inArray(v, values);
								values.splice(idx,1);
							}
						}
					}
					$("form").on("change", "select#fields_list", function() {
						var val = $(this).val();
						if (val && $.inArray(val, values) == -1) {
							$("#sortable_core_options>div:last .button-add-core_options").click();
							$("#sortable_core_options>div:last input:text[name=\'jform['.$id.'][]\']").val(val);
							values.push(val);
						}
						'.$js2.'
					});
					';

		// Set
		if ( $js ) {
			$js	=	'jQuery(document).ready(function($){'.$js.'});';
			
			if ( $format == 'raw' ) {
				return '<script type="text/javascript">'.$js.'</script>';
			} else {
				$doc->addScriptDeclaration( $js );
			}
		}
		if ( $css ) {
			$doc->addStyleDeclaration( $css );
		}
		
		return;
	}
}
?>