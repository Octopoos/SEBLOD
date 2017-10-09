<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cckintegration.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JFormField
class JFormFieldCckIntegration extends JFormField
{
	public $type = 'CckIntegration';

	// getInput
	protected function getInput()
	{
		$app		=	JFactory::getApplication();
		$doc		=	JFactory::getDocument();
		$lang		=	JFactory::getLanguage();
		$component	=	'com_cck_integration';
		$config		=	JCckDev::init( array(), true );
		$location	=	(string)$this->element['location'];
		$lang->load( 'com_cck_default', JPATH_SITE );

		// Init
		$actions	=	array(
							(object)array(
								'title'=>'COM_CCK_INTEGRATION_DEFAULT_CONTENT_TYPE',
								'name'=>'default_type',
								'description'=>'COM_CCK_INTEGRATION_DEFAULT_CONTENT_TYPE_DESC',
								'dev_fieldname'=>'core_dev_select',
								'dev_override'=>array( 'defaultvalue'=>'', 'selectlabel'=>'None', 'bool8'=>0, 'css'=>'cck-integration' )
							),
							(object)array(
								'title'=>'COM_CCK_INTEGRATION_ADD_BUTTON',
								'name'=>'add',
								'description'=>'COM_CCK_INTEGRATION_ADD_BUTTON_DESC',
								'dev_fieldname'=>'core_dev_select',
								'dev_override'=>array( 'defaultvalue'=>'1', 'type'=>'radio', 'options'=>'PREFERENCES_OPTION_TOOLTIP=2||PREFERENCES_OPTION_MODAL_BOX=1||None=0', 'css'=>'cck-integration btn-group btn-group-yesno pull-left cck-integration-add' )
							),
							(object)array(
								'title'=>'COM_CCK_INTEGRATION_ADD_BUTTON_ALT',
								'name'=>'add_alt',
								'description'=>'COM_CCK_INTEGRATION_ADD_BUTTON_ALT_DESC',
								'dev_fieldname'=>'core_dev_select',
								'dev_override'=>array( 'defaultvalue'=>'2', 'type'=>'radio', 'options'=>'Above=1||Below=2||None=0', 'css'=>'cck-integration btn-group btn-group-yesno pull-left' )
							),
							(object)array(
								'title'=>'COM_CCK_INTEGRATION_ADD_REDIRECT',
								'name'=>'add_redirect',
								'description'=>'COM_CCK_INTEGRATION_ADD_REDIRECT_DESC',
								'dev_fieldname'=>'core_dev_bool',
								'dev_override'=>array( 'defaultvalue'=>'1', 'type'=>'radio', 'css'=>'cck-integration btn-group btn-group-yesno pull-left' )
							),
							(object)array(
								'title'=>'COM_CCK_INTEGRATION_EDIT_LINK',
								'name'=>'edit',
								'description'=>'COM_CCK_INTEGRATION_EDIT_LINK_DESC',
								'dev_fieldname'=>'core_dev_select',
								'dev_override'=>array( 'defaultvalue'=>'0', 'type'=>'radio', 'options'=>'FORCE_SEBLOD=1||Auto=0', 'css'=>'cck-integration btn-group btn-group-yesno pull-left' )
							),
							(object)array(
								'title'=>'COM_CCK_INTEGRATION_EDIT_LINK_ALT',
								'name'=>'edit_alt',
								'description'=>'COM_CCK_INTEGRATION_EDIT_LINK_ALT_DESC',
								'dev_fieldname'=>'core_dev_bool',
								'dev_override'=>array( 'defaultvalue'=>'1', 'type'=>'radio', 'css'=>'cck-integration btn-group btn-group-yesno pull-left' )
							)
							/*
							(object)array(
								'title'=>'COM_CCK_INTEGRATION_EDIT_REDIRECT',
								'name'=>'edit_redirect',
								'description'=>'COM_CCK_INTEGRATION_EDIT_REDIRECT_DESC',
								'dev_fieldname'=>'core_dev_select',
								'dev_override'=>array( 'type'=>'radio', 'options'=>'FORCE_SEBLOD=1||Auto=0', 'css'=>'cck-integration btn-group pull-left' )
							)
							*/
						);
		$groups		=	JCckDatabase::loadObjectList( 'SELECT a.* FROM #__cck_core_objects AS a ORDER BY a.title' );
		$options	=	JCckDatabase::loadObjectListArray( 'SELECT a.title AS text, a.name AS value, a.storage_location FROM #__cck_core_types AS a WHERE a.storage_location != "" AND a.storage_location != "none" ORDER BY text', 'storage_location' );

		// Prepare
		$html	=	array();
		$html[]	=	'<p class="rule-desc">' . JText::_( 'COM_CCK_CONFIG_INTEGRATION_CONTENT_OBJECTS_DESC' ) . '</p>';
		$html[] =	'<div id="integration-sliders" class="tabbable tabs-left">';
		$html[]	=	'<ul class="nav nav-tabs">';
		foreach ( $groups as $i=>$group ) {
			$active	=	'';
			if ( $i == 0 ) {
				$active	=	'active';
			}
			$html[]	=	'<li class="' . $active . '">';
			$html[]	=	'<a href="#permission-' . $group->name . '" data-toggle="tab">';
			$html[]	=	$group->title;
			$html[]	=	'</a>';
			$html[] =	'</li>';
		}
		$html[]	=	'</ul>';
		$html[]	=	'<div class="tab-content">';

		foreach ( $groups as $i=>$group ) {
			$actions2	=	$actions;
			$active		=	'';
			if ( $i == 0 ) {
				$active	=	' active';
			}
			$values	=	json_decode( $group->options );

			$html[] =	'<div class="tab-pane' . $active . '" id="permission-' . $group->name . '">';
			$html[] =	'<table class="table table-striped">';
			$html[] =	'<thead>';
			$html[] =	'<tr>';

			$html[] =	'<th class="actions" id="actions-th' . $group->name . '">';
			$html[] =	'<span class="acl-action">' . JText::_( 'COM_CCK_SETTINGS' ) . '</span>';
			$html[] =	'</th>';

			$html[] =	'<th class="settings" id="settings-th' . $group->name . '">';
			$html[] =	'<span class="acl-action">' . JText::_( 'COM_CCK_UPDATE_SETTINGS' ) . '</span>';
			$html[] =	'</th>';

			$html[] =	'</tr>';
			$html[] =	'</thead>';
			$html[] =	'<tbody>';

			$xml	=	JPATH_SITE.'/plugins/cck_storage_location/'.$group->name.'/config.xml';				
			if ( is_file( $xml ) ) {
				$k			=	count( $actions );
				$overrides	=	array( 'options', 'selectlabel' );
				$xml		=	JCckDev::fromXML( $xml );
				$lang->load( 'plg_cck_storage_location_'.$group->name );
				foreach ( $xml->children() as $fieldset ) {
					foreach ( $fieldset->children() as $field ) {
						$attr	=	$field->attributes();
						if ( isset( $attr->construction ) ) {
							$actions2[$k]	=	(object)array(
													'title'=>(string)$attr->label,
													'name'=>(string)$attr->name,
													'description'=>(string)$attr->description,
													'dev_fieldname'=>(string)$attr->construction,
													'dev_override'=>array( 'css'=>'cck-integration btn-group btn-group-yesno pull-left' )
												);
							$actions2[$k]->dev_override['defaultvalue']	=	(string)$attr->default;
							if ( isset( $attr->size ) ) {
								$actions2[$k]->dev_override['size']		=	(string)$attr->size;
							}
							foreach ( $overrides as $o ) {
								if ( isset( $attr->{'cck_'.$o} ) ) {
									$actions2[$k]->dev_override[$o]		=	(string)$attr->{'cck_'.$o};
								}	
							}
							$k++;
						}
					}
				}
			}

			foreach ( $actions2 as $j=>$action ) {
				$inherit								=	array( 'id'=>'integration_'.$group->name.'-'.$action->name );
				$action->dev_override['storage_field']	=	'integration['.$group->name.']['.$action->name.']';
				$value									=	isset( $values->{$action->name} ) ? $values->{$action->name} : '';
				if ( $location == 'form' ) {
					$action->dev_override['type']		=	'select_simple';
					$action->dev_override['selectlabel']=	'Inherit';
					$action->dev_override['css']		=	str_replace( ' btn-group', '', $action->dev_override['css'] );
				}

				$html[] =	'<tr>';
				$html[] =	'<td headers="actions-th' . $group->name . '">';
				$html[] =	'<label class="tip hasTooltip" for="' . $this->id . '_' . $action->name . '_' . $group->name . '" title="'. htmlspecialchars( JText::_( $action->description ), ENT_COMPAT, 'UTF-8' ) . '">';
				$html[] =	JText::_( $action->title );
				$html[] =	'</label>';
				$html[] =	'</td>';
				$html[] =	'<td headers="settings-th' . $group->name . '">';

				if ( $action->name == 'default_type' ) {
					$opts	=	array();
					if ( isset( $options[$group->name] ) && count( $options[$group->name] ) ) {
						foreach ( $options[$group->name] as $o ) {
							$opts[]	=	$o->text.'='.$o->value;
						}
					}
					$opts	=	implode( '||', $opts );
					$action->dev_override['options']	=	$opts;
					$html[] =	JCckDev::getForm( $action->dev_fieldname, $value, $config, $action->dev_override, $inherit );
				} elseif ( $action->name == 'add' ) {
					$html[] =	JCckDev::getForm( $action->dev_fieldname, $value, $config, $action->dev_override, $inherit );
					if ( $value == '' ) {
						$value	=	$action->dev_override['defaultvalue'];
					}
					if ( $value == '2' ) {
						$disabled	=	' disabled';
					} elseif ( $value == '1' ) {
						$disabled	=	'';
					} else {
						$disabled	=	' disabled';
						$actions[$j+1]->dev_override['css']	.=	' disabled';
					}
					$name	=	'add_layout';
					$value	=	isset( $values->$name ) ? $values->$name : '';
					if ( $location == 'form' ) {
						$html[] =	JCckDev::getForm( 'core_dev_select', $value, $config, array( 'defaultvalue'=>'', 'selectlabel'=>'Inherit', 'options'=>'CONFIG_OPTION_ICON=icon||CONFIG_OPTION_LIST=list', 'css'=>'cck-integration input-small pull-left'.$disabled, 'storage_field'=>'integration['.$group->name.']['.$name.']' ), array( 'id'=>'integration_'.$group->name.'-'.$name ) );
					} else {
						$html[] =	JCckDev::getForm( 'core_dev_select', $value, $config, array( 'defaultvalue'=>'icon', 'type'=>'radio', 'options'=>'CONFIG_OPTION_ICON=icon||CONFIG_OPTION_LIST=list', 'css'=>'cck-integration btn-group btn-group-yesno pull-left'.$disabled, 'storage_field'=>'integration['.$group->name.']['.$name.']' ), array( 'id'=>'integration_'.$group->name.'-'.$name ) );						
					}
				} else {
					$html[] =	JCckDev::getForm( $action->dev_fieldname, $value, $config, $action->dev_override, $inherit );
				}

				$html[] =	'</td>';
				$html[] =	'</tr>';
			}

			$html[] =	'</tbody>';
			$html[] =	'</table></div>';
		}

		$html[] =	'</div></div>';

		// Stuff
		JCck::loadjQuery( true, true, array( 'cck.dev-3.7.0.min.js', 'jquery.json.min.js', 'jquery.ui.effects.min.js' ) );
		$ajax	=	'../media/system/images/modal/spinner.gif';
		$js		=	'
					(function ($){
						JCck.Dev = {
							doIntegration: 0,
							submit: function(task) {
								if (JCck.Dev.doIntegration) {
									var data = {};
									var loading = \'<img src="'.$ajax.'" alt="" />\';
									$("#integration-sliders .cck-integration").each(function(i) {
										var id = $(this).attr("id").substring(12);
										var tab = id.split("-");
										if (!data[tab[0]]) {
											data[tab[0]] = {};
										}
										data[tab[0]][tab[1]] = String($(this).myVal());
									});
									var encoded = $.toJSON(data);
									$.ajax({
										cache: false,
										data: encoded,
										type: "POST",
										url: "index.php?option=com_cck&task=ajaxSaveIntegration",
										beforeSend:function(){ $("#toolbar-help").after(\'<div id="toolbar-spinner" class="btn-group">\'+loading+\'</div>\'); },
										success: function(response){ $("#toolbar-spinner").remove(); Joomla.submitbutton("config.save.component."+task); },
										error:function(){}
									});
								} else {
									Joomla.submitbutton("config.save.component."+task);
								}
							}
						}
						$(document).ready(function() {
							$("#toolbar-apply button").attr("onclick","JCck.Dev.submit(\'apply\')");
							$("#toolbar-save button").attr("onclick","JCck.Dev.submit(\'save\')");
							$("#integration-sliders").on("change", "select.cck-integration,input.cck-integration", function() {
								JCck.Dev.doIntegration = 1;
							});
							$("#integration-sliders .btn-group label:not(.active)").click(function() {
								JCck.Dev.doIntegration = 1;
							});
							$(".cck-integration-add").click(function(e) {
								e.preventDefault();
								var id = $(this).attr("id")+"_layout";
								var id2 = $(this).attr("id")+"_alt";
								if ($(this).myVal() == 2) {
									$("#"+id).addClass("disabled");
									$("#"+id2).removeClass("disabled");
								} else if ($(this).myVal() == 1) {
									$("#"+id).removeClass("disabled");
									$("#"+id2).removeClass("disabled");
								} else {
									$("#"+id).addClass("disabled");
									$("#"+id2).addClass("disabled");
								}
							});
							$(".tip").tooltip({});
						});
					})(jQuery);
					';
		$doc->addScriptDeclaration( $js );

		return implode( "\n", $html );
	}
}
?>