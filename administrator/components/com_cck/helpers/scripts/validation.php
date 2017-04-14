<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: validation.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

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
						var loading = \'<img align="center" src="'.$ajax_load.'" alt="" />\';
						$.ajax({
						  cache: false,
						  data: data,
						  type: "POST",
						  url: "index.php?option=com_cck&task=box.add&layout=raw&tmpl=component",
						  beforeSend:function(){ $("#loading").html(loading); $(elem).html(""); },
						  success: function(response){ $("#loading").html(""); $(elem).html(response); if (opts) { JCck.Dev.setOptions(opts); } },
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
    			}
				$(document).ready(function(){
					var eid = "'.$id.'";
					var data = parent.jQuery("#"+eid+"_required").val();
					if (data != "" && data != "required") {
						var data2 =	data.split("[");
						data2 = data2[1];
						len2 = data2.length;
						data2 = data2.substr(0, len2-1);
						data = "grouprequired";
						$("#required2").val(data2);
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
					$("#required2,#blank_li").isVisibleWhen("required","grouprequired");
				});
			})(jQuery);
			';
$doc->addScriptDeclaration( $js );

JText::script( 'COM_CCK_OPTIONAL' );
JText::script( 'COM_CCK_REQUIRED' );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_REQUIRED' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Required', 'selectlabel'=>'', 'options'=>'No=||Yes=required||Yes GroupRequired=grouprequired', 'storage_field'=>'required' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Alert', 'storage_field'=>'required_alert' ) );
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Group', 'required'=>'required', 'storage_field'=>'required2' ) );
        ?>
    </ul>
</div>
<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_VALIDATION' ) ); ?>
    <div id="loading" class="loading"></div>
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