<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit2.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;

$config		=	JCckDev::init( array(), true, array( 'item'=>$this->item, 'tmpl' => 'ajax' ) );
$cck		=	JCckDev::preload( array( 'core_pagination', 'core_cache', 'core_cache2', 'core_dev_text', 'core_limit', 'core_sef', 'core_sef_canonical',
										 'core_prepare_content', 'core_debug', 'core_auto_redirection', 'core_ordering',
										 'core_tag_title', 'core_class_title', 'core_show_hide2', 'core_action_no_access', 'core_redirection_url_no_access', 'core_label_total',
										 'core_class_total', 'core_show_pagination', 'core_class_pagination',
										 'core_message_style', 'core_action', 'core_message', 'core_validation_position', 'core_validation_scroll', 'core_validation_color',
										 'core_validation_background_color', 'core_template' ) );
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
<div class="layers" id="layer_fields" <?php echo ( $this->item->layer == 'fields' ) ? '' : 'style="display: none;"'; ?>><?php include_once __DIR__.'/edit_fields_'.$this->uix.'.php'; ?></div>
<div class="layers" id="layer_configuration" <?php echo ( $this->item->layer == 'configuration' ) ? '' : 'style="display: none;"'; ?>><?php include_once __DIR__.'/edit_configuration.php'; ?></div>
<div class="layers" id="layer_template" <?php echo ( $this->item->layer == 'template' ) ? '' : 'style="display: none;"'; ?>><?php include_once __DIR__.'/edit_template.php'; ?></div>
<script type="text/javascript">
JCck.DevHelper.setSidebar();
(function ($){
$("#pos-1 input:radio[name='positions']").prop("checked", true);
var id = "<?php echo @$this->item->id; ?>"; if ($("#jform_id").val()==0) {$("#jform_id,#myid").val(id);}
var block_item = <?php echo $block_item; ?>;
if (block_item) {$("#client5_label").addClass("disabled"); $("#client5").prop("disabled", true);} else {$("#client5_label").removeClass("disabled"); $("#client5").prop("disabled", false);}
$("#options_tag_list_title").isVisibleWhen('options_show_list_title','1',true,'visibility'); $("#options_label_items_number").isVisibleWhen('options_show_items_number','1');
$("#options_cache_per_user").isVisibleWhen('options_cache','1,2',false); $("#options_callback_pagination,#options_label_pagination").isVisibleWhen('options_show_pagination','2,8');
$("#options_tmpl_resource,#options_json_resource").isVisibleWhen('options_load_resource','1'); $("#blank_li7").isVisibleWhen('options_load_resource','0');
if($("#quick_menuitem").length>0){if($("#quick_menuitem").val()){$("#quick_menuitem").val("").prop("disabled",true);}}
if($("div#more").is(":visible") && $("#jform_id").val()){ if ($("#toggle_more").hasClass("open")){ $("#toggle_more").removeClass("open").addClass("closed"); } else { $("#toggle_more").removeClass("closed").addClass("open"); } $("#more").slideToggle("slow"); }
<?php echo $this->js['tooltip']; ?>
function sanitizeAndReplaceNotes(str) {
    let processedText = str.replace(/\n/g, '<br />');
    processedText = processedText
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    return processedText;
}
$(document).ready(function() {
	$("#sortable1").on("click", ".f-nt", function() {
		if ($(this).hasClass("f-nt-1")) {
			var href = $(this).attr("data-href");

			if (href) {
				var otherWindow = window.open();
				otherWindow.opener = null;
				otherWindow.location = href;
			}
		} else {
			var initiatorElement	=	this;
            var modalElement = $('#collapseModal_note')[0];
            var modal = new bootstrap.Modal(modalElement, { backdrop: true });
			$(modalElement).data('initiator', initiatorElement);
			if (modal) modal.show();	
		}
	});
	$('#collapseModal_note').on("click", "#resetNote", function() {
		var $el = $('#collapseModal_note');
		var el_id = $el.attr('data-note');

		if (el_id) {
			var n = $("#collapseModal_note").attr("data-n");
			var nn = n.indexOf("textarea") == -1 ? "1" : "0";

			if ( $("#"+el_id+" "+n).length ) {
				$("#"+el_id+" "+n).myVal("").remove();
			}
			$("#"+el_id+" .f-nt"+nn).attr("data-original-title","");
		}
		var modal = bootstrap.Modal.getInstance($el[0]);
		if (modal) modal.hide();
	});
	$('#collapseModal_note').on("click", "#submitNote", function() {
		var $el = $('#collapseModal_note');
		var el_id = $el.attr('data-note');

		if (el_id) {
			var n = $("#collapseModal_note").attr("data-n");
			var nn = n.indexOf("textarea") == -1 ? "1" : "0";

			if ( !$("#"+el_id+" "+n).length ) {
				if (nn == "1") {
					var $ta = $( '<input data-n type="hidden" name="'+$("#k"+el_id).attr("name").replace("ff[", "ffp[")+'[notes][-1]" />' );

					if ( !$("#"+el_id+" textarea").length ) {
						$("#"+el_id+" > a.cbox.b").after($ta);
					} else {
						$("#"+el_id+" textarea").after($ta);
					}	
				} else {
					var $ta = $( '<textarea data-n class="hidden" name="'+$("#k"+el_id).attr("name").replace("ff[", "ffp[")+'[notes][0]"></textarea>' );
					$("#"+el_id+" > a.cbox.b").after($ta);
				}
			}
			
			var v = $("#collapseModal_note "+n).myVal();
			$("#"+el_id+" "+n).myVal(v);
			$("#"+el_id+" .f-nt"+nn).attr("data-original-title",sanitizeAndReplaceNotes(v));
			if (!$("#"+el_id+" .f-nt").hasClass("hasTooltip")) {
				$("#"+el_id+" .f-nt").addClass("hasTooltip");
				$("#"+el_id+" .f-nt").tooltip({"html": true,"placement": "right"});
			}
		}
		var modal = bootstrap.Modal.getInstance($el[0]);
		if (modal) modal.hide();
	});
	$('#collapseModal_note').on('show.bs.modal', function (event) {
		var initiatorElement = $(this).data('initiator');

		if (initiatorElement !== undefined) {
			var $el = $(initiatorElement).parent().parent().parent();
			var el_id = $el[0].id;
			var n = "";
			var v = "";
			
			n = $(initiatorElement).hasClass("f-nt1") ? "input[data-n]" : "textarea[data-n]";
			if ( $("#"+el_id+" "+n).length ) {
				v = $("#"+el_id+" "+n).myVal();
			}
			$("#collapseModal_note "+n).myVal(v);
			$("#collapseModal_note").attr("data-n", n);
			$("#collapseModal_note").attr("data-note", el_id);
		}
	});
	$('#collapseModal_note').on('hidden.bs.modal', function () {
		$("#collapseModal_note").attr("data-note", "0");
	});
});
})(jQuery);
</script>
<style>body .modal-backdrop{background:none!important;} body .subhead .btn{box-shadow:none;} body .subhead a:not(:hover).btn > span {color: var(--subhead-btn-icon)!important} body .subhead a.btn > span {line-height: 22px!important;}</style>
<div class="modal modal-small hide fade" id="collapseModal_note" data-note="0" data-n="">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header subhead" id="toolbarBox">
				<a href="javascript:void(0);" id="submitNote" class="btn btn-small btn-success"><span class="icon-save"></span><?php echo Text::_( 'COM_CCK_SAVE_AND_CLOSE' ); ?></a>
				<a href="javascript:void(0);" id="resetNote" class="btn btn-small"><span class="icon-refresh"></span><?php echo Text::_( 'COM_CCK_RESET' ); ?></a>
				<a href="javascript:void(0);" id="closeNote" class="btn btn-small btn-danger" data-bs-dismiss="modal" aria-hidden="true"><span class="icon-unpublish"></span><?php echo Text::_( 'COM_CCK_CANCEL' ); ?></a>
		    </div>
			<div class="modal-body">
				<textarea data-n class="input-xxlarge" id="f_note_textarea" cols="100" rows="3" maxlength="512"></textarea>
				<input data-n class="input-small" type="text" id="f_note_input" value="" placeholder="#0" />
			</div>
		</div>
	</div>
</div>