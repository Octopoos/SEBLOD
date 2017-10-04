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

JCckDev::initScript( 'field', $this->item, array( 'hasOptions'=>true, 'fieldPicker'=>true ) );
JCckDev::forceStorage();

$options	=	JCckDev::fromSTRING( $this->item->options );
$options2	=	JCckDev::fromJSON( $this->item->options2 );
$task_id	=	array( 'export'=>'', 'process'=>'' );

if ( isset( $options2['task'] ) ) {
	$options2_task	=	str_replace( '_ajax', '', $options2['task'] );

	if ( $options2_task == 'export' || $options2_task == 'process' ) {
		$task_id[$options2_task]	=	$options2['task_id'];
	}
}
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
		echo '<li><label>'.JText::_( 'COM_CCK_LABEL_ICON' ).'</label>'
		 .	 JCckDev::getForm( 'core_dev_select', $this->item->bool6, $config, array( 'label'=>'Label Icon', 'defaultvalue'=>'0', 'selectlabel'=>'',
		 																			  'options'=>'Hide=0||Show=optgroup||Prepend=1||Append=2||Replace=3', 'storage_field'=>'bool6' ) )
		 .	 JCckDev::getForm( 'core_icons', @$options2['icon'], $config, array( 'css'=>'max-width-150' ) )
		 .	 '</li>';
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li3" value="" />' );
		echo JCckDev::renderForm( 'core_bool', $this->item->bool, $config, array( 'label'=>'TYPE', 'defaultvalue'=>'1', 'options'=>'Input=0||Button=1' ) );
		echo JCckDev::renderForm( 'core_task', @$options2['task'], $config );
		echo JCckDev::renderForm( 'core_bool3', @$options2['task_auto'], $config, array( 'label'=>'Auto Selection', 'defaultvalue'=>'1', 'storage_field'=>'json[options2][task_auto]' ) );
		echo JCckDev::renderForm( 'core_task_exporter', $task_id['export'], $config, array( 'storage_field'=>'json[options2][task_id_export]' ) );
		echo JCckDev::renderForm( 'core_task_processing', $task_id['process'], $config, array( 'storage_field'=>'json[options2][task_id_process]' ) );

		echo JCckDev::renderForm( 'core_bool2', $this->item->bool2, $config, array( 'label'=>'SHOW_ALTERNATIVE_LINK', 'defaultvalue'=>'0', 'options'=>'Hide=0||Show=optgroup||Use Default=1||Custom=2' ) );
		echo JCckDev::renderForm( 'core_bool3', $this->item->bool3, $config, array( 'label'=>'Show Alternative Or', 'defaultvalue'=>'0', 'options'=>'Hide=0||Show=1' ) );
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li2" value="" />' );
		echo '<li><label>'.JText::_( 'COM_CCK_TEXT_LINK' ).'</label>'
		 .	 JCckDev::getForm( 'core_dev_text', @$options2['alt_link_text'], $config, array( 'label'=>'Text', 'required'=>'required', 'size'=>14, 'storage_field'=>'json[options2][alt_link_text]' ) )
		 .	 JCckDev::getForm( 'core_plugins', @$options2['alt_link'], $config, array( 'selectlabel'=>'Select', 'location'=>'field_link', 'required'=>'required',
																					   'storage_field'=>'json[options2][alt_link]', 'attributes'=>'style="max-width:98px"' ) )
		 .	 '<input type="hidden" id="json_options2_alt_link_options" name="json[options2][alt_link_options]" value="'.htmlspecialchars( @$options2['alt_link_options'] ).'" />'
		 .	 '<span class="c_link" id="json_options2_alt" name="json_options2_alt">+</span>'
		 .	 '</li>';
		echo JCckDev::renderForm( 'core_menuitem', @$options2['itemid'], $config, array( 'label'=>'Redirection', 'selectlabel'=>'None', 'storage_field'=>'json[options2][itemid]' ) );
		echo JCckDev::renderForm( 'core_dev_textarea', @$options2['custom'], $config, array( 'label'=>'Custom variables', 'cols'=>92, 'rows'=>1, 'storage_field'=>'json[options2][custom]' ), array(), 'w100' );
		
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li4" value="" />' );
		echo JCckDev::renderForm( 'core_options', $options, $config, array( 'label'=>'Fields', 'rows'=>1 ), array( 'after'=>$this->item->init['fieldPicker'] ) );

		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#bool6').isVisibleWhen('bool','1');
	$('#blank_li3').isVisibleWhen('bool','0');
	$('#json_options2_icon').isVisibleWhen('bool6','1,2,3',false);
	$('#bool3').isVisibleWhen('bool2','1,2');
	$('#json_options2_task_auto').isVisibleWhen('json_options2_task','export,export_ajax,process,process_ajax');
	$('#json_options2_task_id_export').isVisibleWhen('json_options2_task','export,export_ajax');
	$('#json_options2_task_id_process').isVisibleWhen('json_options2_task','process,process_ajax');
	$('#json_options2_custom,#json_options2_itemid').isVisibleWhen('json_options2_task','save2redirect');
	$('#json_options2_alt_link_text, #blank_li2').isVisibleWhen('bool2','2');
	$('#core_options, #blank_li4').isVisibleWhen('json_options2_task','process_ajax');
	$("div#layer").on("click", "span.c_link", function() {
		var type = $("#json_options2_alt_link").val();
		if (type) {
			var field = $(this).attr("name");
			var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=plugins/cck_field_link/"+type+"/tmpl/edit.php&id="+field+"&name="+type+"&validation=1";
			$.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
		}
	});
});
</script>