<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( $config['tmpl'] == 'ajax' ) {
	$js	=	'';
} else {
	$js	=	'
				$("#toggle_more2").live("click", function() {
					if ($("#toggle_more2").hasClass("open")){ 	
						$("#toggle_more2").removeClass("open").addClass("closed");
					} else {
						$("#toggle_more2").removeClass("closed").addClass("open");
					}
					$("#storage_more").slideToggle();
				});
				$("#storage").live("change", function() { var v = $("#storage").val(); JCck.Dev.toggleTranslation();
					if ( v == "dev" ) { $("#storage_more, .storage_more").show(); $("#storage_location, #storage_table, #storage_alter").hide().attr("disabled", "disabled"); $("#storage_field").removeAttr("disabled").show(); $("#storage_field_pick").show(); $("#storage_field").val(""); } else { $("#storage_more, .storage_more").hide(); if ( v == "none" ) { $("#storage_location, #storage_table, #storage_alter").hide().attr("disabled", "disabled"); $("#storage_field").hide().attr("disabled", "disabled"); $("#storage_field_pick").hide(); } else { var custom = $("#storage_location").find("option:selected").attr("data-custom"); $("#storage_location, #storage_field, #storage_alter").removeAttr("disabled").show(); $("#storage_field_pick").show(); if (v == "standard") { if (custom && custom == $("#storage_field").val()){$("#storage_field").val("");}} else if (v == "custom" && !$("#storage_field").val()) {$("#storage_field").val(custom);} if ( $("#storage_location").val() == "free" ) { $("#storage_table").attr("disabled", "").show(); } } }
				});
				$("#storage_location").live("change", function() {
					var v = $("#storage_location").val();
					if ( v == "free" ) {
						$("#storage_table").removeAttr("disabled").show();
					} else {
						$("#storage_table").hide().attr("disabled", "disabled");
					}
					if ($("#storage").val() == "custom") {
						var custom = $("#storage_location").find("option:selected").attr("data-custom");
						$("#storage_field").val(custom);
					}
					$(".object-params").hide();
					$("#op-"+v).show();
				});
				$("#storage_alter").live("change", function() {
					$("#storage_alter_type, #storage_alter_table, #storage_alter_table_notice").toggle();
				});
				$("#storage_field_pick").live("click", function() {
					var field = ( $("#storage").val() == "dev" ) ? "dev_map" : "content_map";
					var location = $("#storage_location").val();
					if (location==null) {
						location = "free";
					}
					if (location=="free") {
						location = $("#storage_table").val();	
					}
					var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/views/field/tmpl/selection.php&title="+location+"&name=map&type=storage_field&id="+field;
					$.fn.colorbox({href:url, iframe:true, innerWidth:300, innerHeight:200, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $("#cboxClose").remove();}});
				});
			'
			;
}
if ( JCck::on() ) {
	$js	.=	'jQuery(".hasTooltip").tooltip({});';
}
$js		=	'
			jQuery(document).ready(function($){
				JCck.Dev.toggleTranslation();
				if ($("#storage").val() == "dev") {
					$("#storage_location, #storage_alter").hide().attr("disabled", "disabled");
				} else if ($("#storage").val() == "none") {
					$("#storage_location, #storage_field, #storage_alter").hide().attr("disabled", "disabled");
					$("#storage_field_pick").hide();
				}
				var v = $("#storage_location").val();
				if (v != "free") {
					$("#storage_table").hide();
				}
				$("#op-"+v).show();
				if ($("#jform_id").val()==0){
					if (parent.jQuery("#element").length && parent.jQuery("#element").val() == "type") {
						$(".storage-cck-more").parent().remove();
					} else {
						$(".storage-cck-core").remove();
					}
				} else {
					$(".storage-cck-more").parent().remove();
				}
				$("#storage_alter_type, #storage_alter_table, #storage_alter_table_notice").hide();

				var h = $("#toggle_more2").parent().height() + 12;
				$("#toggle_more2").css({"top":h});
				
				if (parent.jQuery("input:radio[name=\'linkage\']:checked") && !$("#myid").val()) {
					if (parent.jQuery("input:radio[name=\'linkage\']:checked").val() == 1 && parent.jQuery("#name").val()) {
						var t = parent.jQuery("#name").val();
						$("#storage_cck").val(t);
						//
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
							storage_location = "free";
						}
						$("#storage_location").val(storage_location);
						if (storage_location == "free") {
							$("#storage_table").parent().show();
							$("#storage_table").show();
						}
						if ($("#storage").val() == "custom") {
							var custom = $("#storage_location").find("option:selected").attr("data-custom");
							$("#storage_field").val(custom);
						}
					}
				}
				'.$js.'
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
			echo '<span id="storage_field_pick" name="storage_field_pick">&laquo;</span>';
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
            echo '<li class="w100 switch"><label></label><span class="variation_value linked notice">'.JText::_( 'COM_CCK_FIELD_IS_LINKED' ).' <strong>'.$linked.'</strong></span></li>';
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