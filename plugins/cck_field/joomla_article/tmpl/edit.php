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

$options	=	JCckDev::fromSTRING( $this->item->options );
$options2	=	JCckDev::fromJSON( $this->item->options2 );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
		echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
		echo JCckDev::renderForm( 'core_options_categories', $options, $config, array( 'css'=>'adminformlist-maxwidth' ) );
		echo JCckDev::renderForm( 'core_selectlabel', $this->item->selectlabel, $config, array( 'defaultvalue'=>'Select an Article' ) );
		
		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_PROCESSING' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC_PROCESSING' ), 2 );
		echo '<li><label>'.JText::_( 'COM_CCK_PARENT_DISPLAY' ).'</label>'
		.	 JCckDev::getForm( 'core_display', $this->item->bool, $config )
		.	 '<input type="hidden" id="json_options2_parent_link_options" name="json[options2][parent_link_options]" value="'.htmlspecialchars( @$options2['parent_link_options'] ).'" />'
		.	 '<span class="c_link" id="json_options2_parent" name="json_options2_parent">+</span>'
		.	 '</li>';
		echo JCckDev::renderForm( 'core_extended', $this->item->extended, $config, array( 'label'=>'Parent Content Type', 'required'=>'' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_CHILD_DISPLAY' ).'</label>'
		.	 JCckDev::getForm( 'core_display', $this->item->bool2, $config, array( 'storage_field'=>'bool2' ) )
		.	 '<input type="hidden" id="json_options2_child_link_options" name="json[options2][child_link_options]" value="'.htmlspecialchars( @$options2['child_link_options'] ).'" />'
		.	 '<span class="c_link" id="json_options2_child" name="json_options2_child">+</span>'
		.	 '</li>';
		echo '<li><label>'.JText::_( 'COM_CCK_CHILD_ORDER_BY' ).'</label>'
		.	 JCckDev::getForm( 'core_options_orderby', @$options2['child_orderby'], $config, array( 'storage_field'=>'json[options2][child_orderby]' ) )
		.	 JCckDev::getForm( 'core_options_orderby_direction', @$options2['child_orderby_direction'], $config, array( 'storage_field'=>'json[options2][child_orderby_direction]' ) )
		.	 '</li>';
		echo JCckDev::renderForm( 'core_dev_select', @$options2['child_location'], $config, array( 'defaultvalue'=>'joomla_article', 'label'=>'Child Storage Location', 'selectlabel'=>'',
																									 'options'=>'Article=joomla_article||Category=joomla_category',
																									 'storage_field'=>'json[options2][child_location]' ) );
		echo JCckDev::renderForm( 'core_options_limit', @$options2['child_limit'], $config, array( 'label'=>'Child Limit', 'storage_field'=>'json[options2][child_limit]' ) );
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' );
		echo JCckDev::renderForm( 'core_dev_select', @$options2['child_language'], $config, array( 'label'=>'Child Language', 'selectlabel'=>'All Languages', 'options'=>'Current Language=-1', 'storage_field'=>'json[options2][child_language]' ) );
		
		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ), 3 );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config, array(), array( 'alter_type_value' => 'INT(11)' ) );
        ?>
	</ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	if ( $('#bool').val() != 0 ) { $('#json_options2_parent').hide(); }
	if ( $('#bool2').val()  != 0 ) { $('#json_options2_child').hide();	}
	$("#bool").live("change", function() {
		if ( $('#bool').val() != 0 ) { $('#json_options2_parent').hide(); } else { $('#json_options2_parent').show(); }
	});
	$("#bool2").live("change", function() {
		if ( $('#bool2').val() != 0 ) { $('#json_options2_child').hide(); } else { $('#json_options2_child').show(); }
	});
	$("div#layer").on("click", "span.c_link", function() {
		var field = $(this).attr("name");
		var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=plugins/cck_field_link/content/tmpl/edit.php&id="+field+"&name=content&validation=1";
		$.fn.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
	});
	if ( !$('#extended').val() ) {
		$('#bool2, #json_options2_child_orderby, #json_options2_child_location, #json_options2_child_limit, #blank_li, #json_options2_child_language').parent().hide();
	}
	$("#extended").live("change", function() {
		if ( !$('#extended').val() ) {
			$('#bool2, #json_options2_child_orderby, #json_options2_child_location, #json_options2_child_limit, #blank_li, #json_options2_child_language').parent().hide();
		} else {
			$('#bool2, #json_options2_child_orderby, #json_options2_child_location, #json_options2_child_limit, #blank_li, #json_options2_child_language').parent().show();
		}
	});
});
</script>