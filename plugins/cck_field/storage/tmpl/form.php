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

if ( $config['tmpl'] == 'ajax' ) {
	$js	=	'';
} else {
	$js	=	'
				$("#adminForm").on("click", "#toggle_more2", function() {
					if ($("#toggle_more2").hasClass("open")){ 	
						$("#toggle_more2").removeClass("open").addClass("closed");
					} else {
						$("#toggle_more2").removeClass("closed").addClass("open");
					}
					$("#storage_more").slideToggle();
				});
				$("#adminForm").on("click", "#toggle_adv", function() {
					if ($("#toggle_adv").hasClass("open")){ 	
						$("#toggle_adv").removeClass("open").addClass("closed");
						$(".storage_advanced").fadeOut(200);
					} else {
						$("#toggle_adv").removeClass("closed").addClass("open");
						$(".storage_advanced").fadeIn(100);
					}
					$("#storage_advanced").slideToggle();
				});
				$("#adminForm").on("change", "#storage", function() { var v = $("#storage").val(); JCck.Dev.toggleTranslation();
					if ( v == "dev" ) {
						$("#storage_more, .storage_more").show(); $("#storage_location, #storage_table, #storage_alter").hide().prop("disabled", true); $("#storage_field").prop("disabled", false).show(); $("#storage_field_pick").show(); $("#storage_field").val("");
					} else {
						$("#storage_more, .storage_more").hide();
						if ( v == "none" ) {
							$("#storage_location, #storage_table, #storage_alter").hide().prop("disabled", true); $("#storage_field").hide().prop("disabled", true); $("#storage_field_pick").hide(); $(".object-params").hide(); $(".object-params select").prop("disabled",true);
							$("#toggle_adv").hide();
						} else {
							var custom = $("#storage_location").find("option:selected").attr("data-custom"); $("#storage_location, #storage_field, #storage_alter").prop("disabled", false).show(); $("#storage_field_pick").show();
							if (v == "standard") { if (custom && custom == $("#storage_field").val()){$("#storage_field").val("");}} else if (v == "custom" && !$("#storage_field").val()) {$("#storage_field").val(custom);}
							var sl = $("#storage_location").val(); $("#op-"+sl).show();
							if (sl == "free") { $("#storage_table").prop("disabled", false).show(); } else { $("#op-"+sl+" select").prop("disabled",false); }
							$("#toggle_adv").show();
						}
					}
				});
				$("#adminForm").on("change", "#storage_location", function() {
					var v = $("#storage_location").val();
					if ( v == "free" ) {
						$("#storage_table").prop("disabled", false).show();
					} else {
						$("#storage_table").hide().prop("disabled", true);
					}
					if ($("#storage").val() == "custom") {
						var custom = $("#storage_location").find("option:selected").attr("data-custom");
						$("#storage_field").val(custom);
					}
					$(".object-params").hide(); $(".object-params select").prop("disabled",true);
					$("#op-"+v).show(); $("#op-"+v+" select").prop("disabled",false);
				});
				$("#adminForm").on("change", "#storage_alter", function() {
					$("#storage_alter_type, #storage_alter_table, #storage_alter_table_notice").toggle();
				});
				$("#adminForm").on("change", ".storage-cck-more", function() {
					var $n = $(this).next();
					var $p = $(this).closest("fieldset");
					if ($n.length) {
						var css = "", css2 = "";
						$n.children().hide();
						if ($(this).val()) {
							css = "locked"; css2 = "unlocked";
						} else {
							css = "unlocked"; css2 = "locked";
						}
						$n.find("."+css).show();
						if ($p.length && $p.hasClass("options-form")) {
							$p.addClass(css).removeClass(css2);
						}	
					}
				});
				$("#adminForm").on("click", "#storage_field_pick", function() {
					var field = ( $("#storage").val() == "dev" ) ? "dev_map" : "content_map";
					var location = $("#storage_location").val();
					var map = "map";
					if (location==null) {
						location = "free";
					}
					if (location=="free") {
						location = $("#storage_table").val();	
					} else {
						if (field == "content_map") {
							if ($("#op-"+location).length) {
								var $targt_el = $("#op-"+location).find("#storage_cck");
								if ($targt_el.length && $targt_el.val()) {
									location = "__cck_store_form_"+$targt_el.val();
								}
							}
						}
					}
					var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/views/field/tmpl/selection.php&title="+location+"&name="+map+"&type=storage_field&id="+field;
					$.colorbox({href:url, iframe:true, innerWidth:600, innerHeight:200, scrolling:false, overlayClose:false, fixed:true, className:"modal-small", onLoad: function(){ $("#cboxClose").remove();}});
				});
			'
			;
}
$js		.=	'jQuery(".hasTooltip").tooltip({});';
$js		=	'
			jQuery(document).ready(function($){
				'.$js.'
				JCck.Dev.toggleTranslation();
				if ($("#storage").val() == "dev") {
					$("#storage_location, #storage_alter").hide().prop("disabled", true);
					$("#toggle_adv").hide();
				} else if ($("#storage").val() == "none") {
					$("#storage_location, #storage_field, #storage_alter").hide().prop("disabled", true);
					$("#storage_field_pick").hide();
					$("#toggle_adv").hide();
				}
				var storage_location = $("#storage_location").val();
				if ($("#storage").val() == "none"){
					$(".storage-cck-more").prop("disabled", true); $(".storage-cck-more").parents(".object-params").hide();
				}
				if ($("#jform_id").val()==0){
					if (parent.jQuery("#element").length && parent.jQuery("#element").val() == "search") {
						$(".storage-desc.content-type").remove();
						$(".storage-cck-core").remove();
					} else if (parent.jQuery("#element").length && parent.jQuery("#element").val() == "type") {
						$(".storage-desc.search-type").remove();
						if (parent.jQuery("#storage_location").val()!="none" && (parent.jQuery("#location").val()=="none" || parent.jQuery("#location").val()=="collection")) {
							$(".storage-cck-core").remove();
						} else {
							$(".storage-cck-more").parents(".object-params").remove();	
						}
					} else {
						$(".storage-desc").remove();
						$(".storage-cck-core").remove();
					}
				} else {
					$(".storage-cck-more").parents(".object-params").remove();
					/*if ($("#storage_location").val()) {
						$("#storage_location").prop("disabled",true);
					}*/
					$(".storage-desc").remove();
				}
				$("#storage_advanced").hide();
				$("#storage_alter_type, #storage_alter_table, #storage_alter_table_notice").hide();
				var cv = false;
				if (!$("#myid").val()) {
					if (!parent.jQuery("#element").length || (parent.jQuery("#element").length && parent.jQuery("#name").val())) {
						if (parent.jQuery("#element").length && parent.jQuery("input:radio[name=\'linkage\']:checked").val() != 0) {
							if (parent.jQuery("#location").val()=="collection") {
								cv = true;
							} else {
								$("#storage_cck").val(parent.jQuery("#name").val());
							}
						}
						if ($("#storage").val() == "custom" && !$("#storage_field").val()) {
							if ( $("#force_storage").val() == "0" ) {
								$("#storage").val( "standard" );
							}
							$("#storage_field").val("");
						}
					}
				}
				if ($("#jform_id").val()==0){
					$("#storage_alter_table option").each(function() {
						if ($(this).val()== "2") {
							$(this).hide();
						}
					});
					if (parent.jQuery("#storage_location")){
						var storage_location = parent.jQuery("#storage_location").val();
						if (storage_location == "none") {
							$("#storage").val("none").trigger("change");
							storage_location = "free";
						}
						$("#storage_location").val(storage_location);
						if (storage_location == "free" && $("#storage").val() != "none") {
							$("#storage_table").parent().show();
							$("#storage_table").show();
						}
						if ($("#storage").val() == "custom") {
							var custom = $("#storage_location").find("option:selected").attr("data-custom");
							$("#storage_field").val(custom);
						}
					}
					if (cv) {
						var pv = parent.jQuery("#parent").val();
						if (pv) {
							$("#op-"+storage_location+" #storage_cck").val(pv).trigger("change");
						}
					}
				} else {
					if (storage_location != "free") {
						$("#storage_table").hide();
					}
				}
				if ($("#storage").val() != "none") {
					$("#op-"+storage_location).show(); $("#op-"+storage_location+" select").prop("disabled",false);
				}
				$("#toggle_more2").css({"top":($("#toggle_more2").parent().height() + 12)}).show();
				var ww = $("#storage_field_type").width();
				$("#storage_field_type").css({"right":(ww+26)+"px"});
				
				$(".storage-cck-more").css({"min-width":$("#storage").width()+"px"});
				$("#storage_alter_type option").each(function() {
					if ($(this).val()!= "") {
						$(this).text($(this).text().toUpperCase());
					}
				});
			});
			'
			;

$prefix	=	JFactory::getConfig()->get( 'dbprefix' );
if ( strpos( $config['item']->storage_table, '#__cck_store_form_' ) !== false ) {
	$linked	=	str_replace( '#__cck_store_form_', '', $config['item']->storage_table );
} else {
	$linked	=	'';
}
$table	=	str_replace( '#__', $prefix, $config['item']->storage_table );
$cck	=	JCckDev::preload( array( 'core_storage_table', 'core_storage_field',
									 'core_storage_alter', 'core_storage_alter_type', 'core_storage_alter_table', 'core_required', 'core_script', 'core_attributes', 'core_dev_text' ) );

if ( $config['item']->storage_field2 ) {
	$config['item']->storage_field	.=	'['.$config['item']->storage_field2.']';
}

$dataObj	=	array();
$objects	=	JPluginHelper::getPlugin( 'cck_storage_location' );

foreach ( $objects as $o ) {
	if ( is_file( JPATH_SITE.'/plugins/cck_storage_location/'.$o->name.'/tmpl/edit.php' ) ) {
		$dataObj[$o->name]	=	array(
									'active'=>$config['item']->storage_location == $o->name,
									'name'=>$o->name
								);

		ob_start();
		include_once JPATH_SITE.'/plugins/cck_storage_location/'.$o->name.'/tmpl/edit.php';
		$dataObj[$o->name]['html']	=	ob_get_clean();
	}
}

// Set HTML
$attr	=	array(
				'style="order:1"',
				'style="order:3"',
				'style="order:5"',
				'style="order:4"'
			);
$fields	=	array(
				JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getStorageMode', 'name'=>'core_storage_mode' ), $value, $config, array( 'storage_field'=>'storage' ) ),
				JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getStorageLocation', 'name'=>'core_storage_location' ), $config['item']->storage_location, $config, array( 'storage_field'=>'storage_location' ) ),
				JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.input', array(
					'input'=>JCckDev::getForm( $cck['core_storage_field'], $config['item']->storage_field, $config, array( 'css'=>'storage-target', 'attributes'=>'placeholder="'.JText::_( "COM_CCK_STORAGE_COLUMN_NAME" ).'"' ) ),
					'button'=>'<button type="button" id="storage_field_pick" name="storage_field_pick" class="btn btn-secondary"><span class="icon-expand"></span></button>'
				) ),
				JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit.storage_objects', array( 'items'=>$dataObj ) )
			);
$html	=	'';

// if ( isset( $config['item']->id ) && $config['item']->id ) {
	$html	.=	JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.construction.cck_field.edit.storage_type',
						array(
							'isNew'=>( isset( $config['item']->id ) && $config['item']->id ) ? false : true,
							'storage'=>array(
								'alter'=>JCckDev::getForm( $cck['core_storage_alter'], '', $config ),
								'alter_table'=>JCckDev::getForm( $cck['core_storage_alter_table'], '', $config ),
								'alter_type'=>JCckDev::getForm( $cck['core_storage_alter_type'], $alter_type_value, $config )
							),
							'type'=>$linked
						) );
// }

// Set
$displayData	=	array(
						'config'=>$config,
						'form'=>array(
							array(
								'attributes'=>$attr,
								'fields'=>$fields
							)
						),
						'html'=>array(
							'append'=>$html,
							'prepend'=>'<input type="hidden" id="storage_field_prev" name="storage_field_prev" value="'.$config['item']->storage_field.'" />'
									 . '<input type="hidden" id="storage_cck" name="storage_cck" value="'.$linked.'" />'
									 . '<input type="hidden" id="force_storage" name="force_storage" value="0" />'
						),
						'params'=>array(
							'linked'=>$linked,
							'value'=>$value
						),
						'script'=>$js,
						'type'=>$config['item']->type
					);

$layout	=	new JLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit.storage' );

echo $layout->render( $displayData );

include_once __DIR__.'/form_more.php';
?>