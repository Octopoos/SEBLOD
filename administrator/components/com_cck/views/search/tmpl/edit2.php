<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit2.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$config		=	JCckDev::init( array(), true, array( 'item'=>$this->item ) );
$cck		=	JCckDev::preload( array( 'core_pagination', 'core_cache', 'core_cache2', 'core_dev_text', 'core_limit', 'core_sef',
										 'core_prepare_content', 'core_debug', 'core_auto_redirection', 'core_ordering',
										 'core_tag_title', 'core_class_title', 'core_show_hide2', 'core_action_no_access', 'core_redirection_url_no_access', 'core_label_total',
										 'core_class_total', 'core_show_pagination', 'core_class_pagination',
										 'core_message_style', 'core_action', 'core_message', 'core_template' ) );
Helper_Include::addDependencies( $this->getName(), $this->getLayout(), 'ajax' );

if ( $this->item->client == 'list' ) {
	$block_item	=	( strpos( @$this->style->params, '"cck_client_item":"1"' ) !== false || strpos( @$this->style->params, '"cck_client_item":"2"' ) !== false ) ? 0 : 1;	
} else {
	if ( $this->item->template_list ) {
		$params		=	JCckDatabase::loadResult( 'SELECT params FROM #__template_styles WHERE id = '.(int)$this->item->template_list );
		$block_item	=	( strpos( $params, '"cck_client_item":"1"' ) !== false || strpos( $params, '"cck_client_item":"2"' ) !== false ) ? 0 : 1;	
	} else {
		$block_item	=	1;
	}
}
?>

<div class="layers" id="layer_fields" <?php echo ( $this->item->layer == 'fields' ) ? '' : 'style="display: none;"'; ?>><?php include_once dirname(__FILE__).'/edit_fields_'.$this->uix.'.php'; ?></div>
<div class="layers" id="layer_configuration" <?php echo ( $this->item->layer == 'configuration' ) ? '' : 'style="display: none;"'; ?>><?php include_once dirname(__FILE__).'/edit_configuration.php'; ?></div>
<div class="layers" id="layer_template" <?php echo ( $this->item->layer == 'template' ) ? '' : 'style="display: none;"'; ?>><?php include_once dirname(__FILE__).'/edit_template.php'; ?></div>

<script type="text/javascript">
JCck.Dev.setSidebar();
(function ($){
$("#pos-1 input:radio[name='positions']").attr("checked", "checked");
var id = "<?php echo @$this->item->id; ?>"; if ($("#jform_id").val()==0) {$("#jform_id,#myid").val(id);}
if ($("#jform_id").val()>0) {JCck.Dev.trash = $("#sortable2 li:not(.f-"+$("#folder").val()+")").hide().detach(); $("#sortable2 li").show();}else{JCck.Dev.trash = "";}
var block_item = <?php echo $block_item; ?>;
if (block_item) {$("#client5_label").addClass("disabled"); $("#client5").prop("disabled", true);} else {$("#client5_label").removeClass("disabled"); $("#client5").prop("disabled", false);}
$("#options_tag_list_title").isVisibleWhen('options_show_list_title','1',true,'visibility'); $("#options_label_items_number").isVisibleWhen('options_show_items_number','1');
$("#options_cache_per_user").isVisibleWhen('options_cache','1,2',false); $("#options_callback_pagination,#blank_li6").isVisibleWhen('options_show_pagination','2');
if($("#quick_menuitem").length>0){if($("#quick_menuitem").val()){$("#quick_menuitem").val("").prop("disabled",true);}}
if($("div#more").is(":visible") && $("#jform_id").val()){ if ($("#toggle_more").hasClass("open")){ $("#toggle_more").removeClass("open").addClass("closed"); } else { $("#toggle_more").removeClass("closed").addClass("open"); } $("#more").slideToggle("slow"); }
<?php echo $this->js['tooltip']; ?>
})(jQuery);
</script>