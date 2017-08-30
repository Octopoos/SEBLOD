<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit2.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$config	=	JCckDev::init( array(), true, array( 'item' => $this->item, 'tmpl' => 'ajax' ) );
if ( $this->item->master == 'content' ) {
	$cck	=	JCckDev::preload( array( 'core_title', 'core_typo', 'core_sef', 'core_linkage', 'core_template' ) );
} else {
	$cck	=	JCckDev::preload( array( 'core_message_style', 'core_redirection', 'core_dev_select', 'core_message',
										 'core_redirection_url', 'core_menuitem', 'core_dev_text', 'core_show_hide', 'core_tag_title',
										 'core_class_title', 'core_show_hide2', 'core_action_no_access', 'core_redirection_url_no_access', 'core_stages',
										 'core_validation_position', 'core_validation_scroll', 'core_validation_color',
										 'core_validation_background_color', 'core_linkage', 'core_template' ) );
}
Helper_Include::addDependencies( $this->getName(), $this->getLayout(), 'ajax' );
?>
<div class="layers" id="layer_fields" <?php echo ( $this->item->layer == 'fields' ) ? '' : 'style="display: none;"'; ?>><?php include_once __DIR__.'/edit_fields_'.$this->uix.'.php'; ?></div>
<div class="layers" id="layer_configuration" <?php echo ( $this->item->layer == 'configuration' ) ? '' : 'style="display: none;"'; ?>><?php include_once __DIR__.'/edit_configuration.php'; ?></div>
<div class="layers" id="layer_template" <?php echo ( $this->item->layer == 'template' ) ? '' : 'style="display: none;"'; ?>><?php include_once __DIR__.'/edit_template.php'; ?></div>
<script type="text/javascript">
JCck.DevHelper.setSidebar();
(function ($){
$("#pos-1 input:radio[name='positions']").prop("checked", true);
var id = "<?php echo @$this->item->id; ?>"; if ($("#jform_id").val()==0) {$("#jform_id,#myid").val(id);}
$("#options_tag_form_title").isVisibleWhen('options_show_form_title','1');
$("#options_validation_background_color").isDisabledWhen('options_validation_position','inline');
if($("#quick_menuitem").length>0){if($("#quick_menuitem").val()){$("#quick_menuitem").val("").prop("disabled",true);}}
if($("div#more").is(":visible") && $("#jform_id").val()){ if ($("#toggle_more").hasClass("open")){ $("#toggle_more").removeClass("open").addClass("closed"); } else { $("#toggle_more").removeClass("closed").addClass("open"); } $("#more").slideToggle("slow"); }
<?php echo $this->js['tooltip']; ?>
<?php if ($this->item->locked == 0) { ?>$("#linkage").click();<?php } ?>
})(jQuery);
</script>