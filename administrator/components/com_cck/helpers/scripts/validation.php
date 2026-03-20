<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: validation.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\HTML\HTMLHelper;

require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/helper_admin.php';

$ajax_load	=	'components/com_cck/assets/styles/seblod/images/ajax.gif';
$doc		=	JFactory::getDocument();
$id			=	$this->item->id;
$name		=	$this->item->name;
$lang   	=	JFactory::getLanguage();
$root		=	JUri::root( true );
$doc->addStyleSheet( $root.'/media/cck/scripts/jquery-colorbox/css/colorbox.css' );
$doc->addScript( $root.'/media/cck/scripts/jquery-colorbox/js/jquery.colorbox-min.js' );
$js		=	'
			(function ($){
				JCck.Dev = {
					ajaxLayer: function(elem, data, opts) {
						$.ajax({
						  cache: false,
						  data: data,
						  type: "POST",
						  url: "index.php?option=com_cck&task=box.add&layout=raw&tmpl=raw",
						  beforeSend:function(){ $(elem).html(""); },
						  success: function(response){ $(elem).html(response); if (opts) { JCck.Dev.setOptions(opts); } },
						  error:function(){ $(elem).html("<div><strong>Oops!</strong> Try to close the page & re-open it properly.</div>"); }
						});
					},
					reset: function() {
						var eid = "'.$id.'";
						parent.jQuery("#"+eid+"_required_alert").val("");
						parent.jQuery("#"+eid+"_validation").val("");
						parent.jQuery("#"+eid+"_validation_options").val("");

						if (parent.jQuery("#"+eid+"_required").val()) {
							var txt = Joomla.JText._("COM_CCK_REQUIRED");
						} else {
							var txt = Joomla.JText._("COM_CCK_OPTIONAL");
						}
						parent.jQuery("span[name=\'"+eid+"\'].c_val").html(txt);
						this.close();
					},
					setOptions: function(opts) {
						var data = $.evalJSON(opts);
						$.each(data, function(k, v) {
							if (jQuery.isArray(v)) {
								var encoded	= $.toJSON(v);
								$("#"+k).myVal(encoded);
							} else {
								$("#"+k).myVal(v);
							}
						});
					},
					submit: function() {
						if ( $("#adminForm").validationEngine("validate") === true ) {
							var eid = "'.$id.'";
							var data = $("#required").val();
							var text = data ? "'.JText::_( 'COM_CCK_REQUIRED' ).'" : "'.JText::_( 'COM_CCK_OPTIONAL' ).'";
							if (data == "grouprequired") {
								data	=	"required["+$("#required2").val()+"]";
							} else if (data == "condrequired") {
								data	=	"required[cond:"+$("#required3").val()+"]";
							} else if (data == "langrequired") {
								data	=	"required[lang:default]";
							}
							parent.jQuery("#"+eid+"_required").val(data);
							data = $("#required_alert").val();
							parent.jQuery("#"+eid+"_required_alert").val(data);
							data = $("#validation").val();
							if (data) {
								text += " + 1";
							}
							parent.jQuery("#"+eid+"_validation").val(data);
							data = {};
							data["alert"] = ($("#alert").prop("disabled") !== false) ? "" : $("#alert").myVal();
							var v = "";
							var len = 0;
							$("#layer input.text, #layer select.select, #layer fieldset.checkbox, #layer fieldset.radios").each(function(i) {
								id = $(this).attr("id");
								v = $(this).myVal();
								len = v.length;
								if (v[0] == "[" && v[(len-1)] == "]") {
									data[id] = $.evalJSON(v);
								} else {
									data[id] = v;
								}
							});
							var encoded	= $.toJSON(data);
							parent.jQuery("#"+eid+"_validation_options").val(encoded).next("span").html(text);
							this.close();
							return;
						}
					}
				};
				$(document).ready(function(){
					var eid = "'.$id.'";
					var data = parent.jQuery("#"+eid+"_required").val();
					if (data != "" && data != "required") {
						var data2 =	data.split("[");
						data2 = data2[1];

						if (data2.indexOf("lang:") !== -1) {
							data = "langrequired";
						} else if (data2.indexOf("cond:") !== -1) {
							data2 = data2.substr(5);
							data2 = data2.substr(0, data2.length - 1);
							data = "condrequired";
							$("#required3").val(data2);
						} else {
							data2 = data2.substr(0, data2.length - 1);
							data = "grouprequired";
							$("#required2").val(data2);
						}
					}
					$("#required").val(data);
					data = parent.jQuery("#"+eid+"_required_alert").val();
					$("#required_alert").val(data);
					
					var opts = parent.jQuery("#"+eid+"_validation_options").val();
					opts = (opts != "") ? opts : "{}";
					JCck.Dev.setOptions(opts);
					$("#validation").on("change", function() {
						var validation = $(this).val();
						if (validation) {
							JCck.Dev.ajaxLayer("#layer", "&file=plugins/cck_field_validation/"+validation+"/tmpl/edit.php&name="+validation, opts);
						} else {
							$("#layer").html("");
						}
					});
					$("#required2").isVisibleWhen("required","grouprequired");
					$("#required3").isVisibleWhen("required","condrequired");
					$("#blank_li").isVisibleWhen("required","grouprequired,condrequired");
				});
			})(jQuery);
			';
$doc->addScriptDeclaration( $js );

JPluginHelper::importPlugin( 'cck_field_validation' );
JText::script( 'COM_CCK_OPTIONAL' );
JText::script( 'COM_CCK_REQUIRED' );

$dataTmpl	=	array(
					'form'=>array(
						array(
							'fields'=>array(
								JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Required', 'selectlabel'=>'', 'options'=>'No=||Yes=required||Yes LangRequired=langrequired||Yes GroupRequired=grouprequired||Yes CondRequired=condrequired', 'storage_field'=>'required' ) ),
								JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Alert', 'storage_field'=>'required_alert' ) ),
								JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' ),
								JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Group', 'required'=>'required', 'storage_field'=>'required2' ) ),
								JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field', 'required'=>'required', 'storage_field'=>'required3' ) )
							)
						)
					)
				);
$dataTmpl2	=	array(
					'form'=>array(
						array(
							'fields'=>array(
								JCckDev::renderLayoutFile(
									'cck'.JCck::v().'.form.field', array(
										'label'=>JText::_( 'COM_CCK_VALIDATION' ),
										'html'=>JHtml::_( 'select.genericlist', Helper_Admin::getPluginOptions( 'field_validation', 'cck_', false, true, true, array( 'required' ) ), 'validation', 'class="form-select inputbox select max-width-180"', 'value', 'text', $name, 'validation' )
									)
								),
								JCckDev::renderForm( 'core_validation_alert', '', $config )
							)
						)
					)
				);
?>
<?php
if ( JCck::on( '4.0' ) ) {
	echo HTMLHelper::_( 'uitab.startTabSet', 'myTab', ['active' => 'tab0', 'recall' => true, 'breakpoint' => 768] );
	echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'tab0', JText::_( 'COM_CCK_REQUIRED' ) );
	echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.common.edit_fieldset', $dataTmpl );
	echo HTMLHelper::_( 'uitab.endTab' );

	echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'tab1', JText::_( 'COM_CCK_VALIDATION' ) );

	echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.common.edit_fieldset', $dataTmpl2 );
	?>	
	<div id="layer">
		<?php
		
		if ( $name ) {
			$layer	=	JPATH_PLUGINS.'/cck_field_validation/'.$name.'/tmpl/edit.php';
			if ( is_file( $layer ) ) {
				include_once $layer;
			}
		}
		?>
	</div>
	<?php
	echo HTMLHelper::_( 'uitab.endTab' );
	echo HTMLHelper::_( 'uitab.endTabSet' );

	return;
}
?>
<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_REQUIRED' ) ); ?>
	<ul class="adminformlist adminformlist-2cols">
		<?php
		foreach ( $dataTmpl['fields'] as $field ) {
			echo $field;
		}
		?>
	</ul>
</div>
<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_VALIDATION' ) ); ?>
	<ul class="adminformlist adminformlist-2cols">
		<?php
		$options	=	Helper_Admin::getPluginOptions( 'field_validation', 'cck_', false, true, true, array( 'required' ) );
		$validation	=	JHtml::_( 'select.genericlist', $options, 'validation', 'class="inputbox select" style="max-width:175px;"', 'value', 'text', $name, 'validation' );
		?>
		<li><label><?php echo JText::_( 'COM_CCK_VALIDATION' ); ?></label><?php echo $validation; ?></li>
		<?php echo JCckDev::renderForm( 'core_validation_alert', '', $config ); ?>
	</ul>
	<ul id="layer" class="adminformlist adminformlist-2cols">
		<?php
		if ( $name ) {
			$layer	=	JPATH_PLUGINS.'/cck_field_validation/'.$name.'/tmpl/edit.php';
			if ( is_file( $layer ) ) {
				include_once $layer;
			}
		}
		?>
	</ul>
</div>