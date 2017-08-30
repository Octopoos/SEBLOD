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
				$("#adminForm").on("change", "#storage", function() { var v = $("#storage").val(); JCck.Dev.toggleTranslation();
					if ( v == "dev" ) {
						$("#storage_more, .storage_more").show(); $("#storage_location, #storage_table, #storage_alter").hide().prop("disabled", true); $("#storage_field").prop("disabled", false).show(); $("#storage_field_pick").show(); $("#storage_field").val("");
					} else {
						$("#storage_more, .storage_more").hide();
						if ( v == "none" ) {
							$("#storage_location, #storage_table, #storage_alter").hide().prop("disabled", true); $("#storage_field").hide().prop("disabled", true); $("#storage_field_pick").hide(); $(".object-params").hide(); $(".object-params select").prop("disabled",true);
						} else {
							var custom = $("#storage_location").find("option:selected").attr("data-custom"); $("#storage_location, #storage_field, #storage_alter").prop("disabled", false).show(); $("#storage_field_pick").show();
							if (v == "standard") { if (custom && custom == $("#storage_field").val()){$("#storage_field").val("");}} else if (v == "custom" && !$("#storage_field").val()) {$("#storage_field").val(custom);}
							var sl = $("#storage_location").val(); $("#op-"+sl).show();
							if (sl == "free") { $("#storage_table").prop("disabled", false).show(); } else { $("#op-"+sl+" select").prop("disabled",false); }
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
					$.colorbox({href:url, iframe:true, innerWidth:300, innerHeight:200, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $("#cboxClose").remove();}});
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
				} else if ($("#storage").val() == "none") {
					$("#storage_location, #storage_field, #storage_alter").hide().prop("disabled", true);
					$("#storage_field_pick").hide();
				}
				var storage_location = $("#storage_location").val();
				if ($("#storage").val() == "none"){
					$(".storage-cck-more").prop("disabled", true); $(".storage-cck-more").parent().hide();
				}
				if ($("#jform_id").val()==0){
					if (parent.jQuery("#element").length && parent.jQuery("#element").val() == "search") {
						$(".storage-desc.content-type").remove();
						$(".storage-cck-core").remove();
					} else if (parent.jQuery("#element").length && parent.jQuery("#element").val() == "type") {
						$(".storage-desc.search-type").remove();
						if (parent.jQuery("#storage_location").val()!="none" && parent.jQuery("#location").val()=="none") {
							$(".storage-cck-core").remove();
						} else {
							$(".storage-cck-more").parent().remove();	
						}
					} else {
						$(".storage-desc").remove();
						$(".storage-cck-core").remove();
					}
				} else {
					$(".storage-cck-more").parent().remove();
					$(".storage-desc").remove();
				}
				$("#storage_alter_type, #storage_alter_table, #storage_alter_table_notice").hide();

				var h = $("#toggle_more2").parent().height() + 12;
				$("#toggle_more2").css({"top":h});
				
				if (!$("#myid").val()) {
					if (!parent.jQuery("#element").length || (parent.jQuery("#element").length && parent.jQuery("#name").val())) {
						if (parent.jQuery("#element").length && parent.jQuery("input:radio[name=\'linkage\']:checked").val() != 0) {
							var t = parent.jQuery("#name").val();
							$("#storage_cck").val(t);
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
				} else {
					if (storage_location != "free") {
						$("#storage_table").hide();
					}
				}
				if ($("#storage").val() != "none") {
					$("#op-"+storage_location).show(); $("#op-"+storage_location+" select").prop("disabled",false);
				}
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
$cck	=	JCckDev::preload( array( 'core_storage_mode', 'core_storage_location', 'core_storage_table', 'core_storage_field',
									 'core_storage_alter', 'core_storage_alter_type', 'core_storage_alter_table', 'core_required', 'core_script', 'core_attributes', 'core_dev_text' ) );
?>
        <li class="w100">
            <?php
			echo '<label>' . JText::_( 'COM_CCK_STORAGE_LABEL' ) . '</label>';
			echo JCckDev::getForm( $cck['core_storage_mode'], $value, $config );
			echo JCckDev::getForm( $cck['core_storage_location'], $config['item']->storage_location, $config );
			// echo JCckDev::getForm( $cck['core_storage_table'], $table, $config );
			if ( $config['item']->storage_field2 ) {
				$config['item']->storage_field	.=	'['.$config['item']->storage_field2.']';
			}
			echo JCckDev::getForm( $cck['core_storage_field'], $config['item']->storage_field, $config );
			echo '<input type="hidden" id="storage_field_prev" name="storage_field_prev" value="'.$config['item']->storage_field.'" />';
			echo '<span id="storage_field_pick" name="storage_field_pick"><span class="icon-menu-2"></span></span>';
			echo JCckDev::getForm( $cck['core_storage_alter'], '', $config );
			echo JCckDev::getForm( $cck['core_storage_alter_type'], $alter_type_value, $config );
			echo JCckDev::getForm( $cck['core_storage_alter_table'], '', $config, array( 'attributes'=>'style="width:45px;"' ) );
			echo '<img id="storage_alter_table_notice" class="hasTooltip qtip_cck" title="'.htmlspecialchars( JText::_( 'COM_CCK_ALTER_TABLE_NOTICE' ) )
			 .	 '" src="components/com_cck/assets/images/16/icon-16-notice.png" alt="" />';
			echo '<input type="hidden" id="storage_cck" name="storage_cck" value="'.$linked.'" />';
			echo '<input type="hidden" id="force_storage" name="force_storage" value="0" />';
            ?>
        </li>
        <?php
        $objects	=	JPluginHelper::getPlugin( 'cck_storage_location' );
        foreach ( $objects as $o ) {
        	if ( is_file( JPATH_SITE.'/plugins/cck_storage_location/'.$o->name.'/tmpl/edit.php' ) ) {
        		echo '<li id="op-'.$o->name.'" class="object-params"'.( ( $config['item']->storage_location == $o->name ) ? '' : ' style="display:none;"' ).'><label>'.JText::_( 'COM_CCK_PARAMETERS' ).'</label>';
        		include_once JPATH_SITE.'/plugins/cck_storage_location/'.$o->name.'/tmpl/edit.php';
        		echo '</li>';
        	}
        }
        ?>
        <?php
        if ( $linked ) {
            echo '<li class="w100 switch"><label></label><span class="variation_value linked notice"><span class="icon-lock"></span>'.JText::_( 'COM_CCK_FIELD_IS_LINKED' ).' <strong>'.$linked.'</strong></span></li>';
        }
        ?>
    </ul>
    <div id="toggle_more2" class="toggle_more closed" <?php echo ( $value != 'dev' ) ? '' : 'style="display: none;"'?>></div>
</div>

<script type="text/javascript">
<?php echo $js; ?>
</script>

<div class="seblod" id="storage_more" <?php echo ( $value == 'dev' ) ? '' : 'style="display: none;"'?>>
	<div class="legend top left"><span class="hasTooltip qtip_cck" title="<?php echo htmlspecialchars( JText::_( 'COM_CCK_STUFF_DESC' ) ); ?>"><?php echo JText::_( 'COM_CCK_STUFF' ); ?></span></div>
	<ul class="adminformlist adminformlist-2cols">
        <?php
        $required	=	JCckDev::get( $cck['core_required'], $config['item']->required, $config );
        $class_css	=	JCckDev::get( $cck['core_dev_text'], $config['item']->css, $config, array( 'label'=>'Class CSS', 'storage_field'=>'css' ) );
        $attributes	=	JCckDev::get( $cck['core_attributes'], $config['item']->attributes, $config, array( 'label'=>'Custom Attributes' ) );
        $script		=	JCckDev::get( $cck['core_script'], $config['item']->script, $config );
        ?>
        <li>
            <label><?php echo $class_css->label; ?></label><?php echo $class_css->form; ?>
        </li>
        <li class="storage_more" <?php echo ( $value == 'dev' ) ? '' : 'style="display: none;"'?>>
            <label><?php echo $required->label; ?></label><?php echo $required->form; ?>
        </li>
        <li class="w100">
            <label><?php echo $attributes->label; ?></label><?php echo $attributes->form; ?>
        </li>
        <li class="w100">
            <label><?php echo $script->label; ?></label><?php echo $script->form; ?>
        </li>